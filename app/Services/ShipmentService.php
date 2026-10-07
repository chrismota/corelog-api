<?php

namespace App\Services;

use App\Contexts\OrganizationContext;
use App\DTOs\Shipment\CreateShipmentDTO;
use App\DTOs\Shipment\IndexShipmentDTO;
use App\DTOs\Shipment\UpdateShipmentStatusDTO;
use App\Enums\OrderStatus;
use App\Enums\ShipmentStatus;
use App\Models\DeliveryService;
use App\Models\Order;
use App\Models\Shipment;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class ShipmentService
{
    public function __construct(
        private readonly OrganizationContext $organizationContext,
    ) {
    }

    public function list(IndexShipmentDTO $dto): LengthAwarePaginator
    {
        $organizationId = $this->organizationContext->id();

        $query = Shipment::query()
            ->whereHas('order', function ($query) use ($organizationId) {
                $query->where('organization_id', $organizationId);
            })
            ->with([
                'order',
                'deliveryService',
            ]);

        if ($dto->status !== null) {
            $query->where('status', $dto->status);
        }

        if ($dto->trackingCode !== null) {
            $query->where('tracking_code', $dto->trackingCode);
        }

        if ($dto->deliveryServiceId !== null) {
            $query->where('delivery_service_id', $dto->deliveryServiceId);
        }

        if ($dto->orderId !== null) {
            $query->where('order_id', $dto->orderId);
        }

        if ($dto->shippedFrom !== null) {
            $query->whereDate('shipped_at', '>=', $dto->shippedFrom);
        }

        if ($dto->shippedTo !== null) {
            $query->whereDate('shipped_at', '<=', $dto->shippedTo);
        }

        if ($dto->deliveredFrom !== null) {
            $query->whereDate('delivered_at', '>=', $dto->deliveredFrom);
        }

        if ($dto->deliveredTo !== null) {
            $query->whereDate('delivered_at', '<=', $dto->deliveredTo);
        }

        return $query
            ->latest()
            ->paginate(15);
    }

    public function show(string $shipmentId): Shipment
    {
        $organizationId = $this->organizationContext->id();

        $shipment = Shipment::query()
            ->whereHas('order', function ($query) use ($organizationId) {
                $query->where('organization_id', $organizationId);
            })
            ->with([
                'order',
                'deliveryService',
                'trackingEvents',
            ])
            ->find($shipmentId);

        if (!$shipment) {
            throw ValidationException::withMessages([
                'shipment' => 'Remessa não encontrada.',
            ]);
        }

        return $shipment;
    }

    public function create(CreateShipmentDTO $dto): Shipment
    {
        return DB::transaction(function () use ($dto) {
            $organizationId = $this->organizationContext->id();

            $order = Order::query()
                ->where('organization_id', $organizationId)
                ->find($dto->orderId);

            if (!$order) {
                throw ValidationException::withMessages([
                    'order_id' => 'Pedido não encontrado.',
                ]);
            }

            if ($order->shipment()->exists()) {
                throw ValidationException::withMessages([
                    'order_id' => 'O pedido já possui uma remessa.',
                ]);
            }

            if ($order->status !== OrderStatus::READY_TO_SHIP) {
                throw ValidationException::withMessages([
                    'order_id' => 'O pedido não está pronto para envio.',
                ]);
            }

            $deliveryService = DeliveryService::query()
                ->where('active', true)
                ->find($dto->deliveryServiceId);

            if (!$deliveryService) {
                throw ValidationException::withMessages([
                    'delivery_service_id' =>
                        'Serviço de entrega não encontrado ou inativo.',
                ]);
            }

            $shipment = Shipment::create([
                'order_id' => $order->id,
                'delivery_service_id' => $deliveryService->id,
                'tracking_code' => $dto->trackingCode,
                'status' => ShipmentStatus::CREATED,
            ]);

            $shipment->trackingEvents()->create([
                'status' => ShipmentStatus::CREATED,
                'description' => 'Envio criado',
                'location' => null,
                'occurred_at' => now(),
            ]);

            return $shipment->load([
                'order',
                'deliveryService',
                'trackingEvents',
            ]);
        });
    }

    public function updateStatus(string $shipmentId, UpdateShipmentStatusDTO $dto): Shipment
    {
        return DB::transaction(function () use ($shipmentId, $dto) {
            $organizationId = $this->organizationContext->id();

            $shipment = Shipment::query()
                ->whereHas('order', function ($query) use ($organizationId) {
                    $query->where('organization_id', $organizationId);
                })
                ->with('order')
                ->find($shipmentId);

            if (!$shipment) {
                throw ValidationException::withMessages([
                    'shipment' => 'Remessa não encontrada.',
                ]);
            }

            if (!$shipment->status->canTransitionTo($dto->status)) {
                throw ValidationException::withMessages([
                    'status' => 'Não é possível alterar a remessa de '
                        . $shipment->status->value
                        . ' para '
                        . $dto->status->value
                        . '.',
                ]);
            }

            $now = now();

            $shipment->status = $dto->status;

            if ($dto->status === ShipmentStatus::POSTED) {
                $shipment->shipped_at = $dto->occurredAt ?? $now;
            }

            if ($dto->status === ShipmentStatus::DELIVERED) {
                $shipment->delivered_at = $dto->occurredAt ?? $now;
            }

            $shipment->save();

            $shipment->trackingEvents()->create([
                'status' => $dto->status,
                'description' => $dto->description,
                'location' => $dto->location,
                'occurred_at' => $dto->occurredAt ?? $now,
            ]);

            if ($dto->status === ShipmentStatus::POSTED) {
                $shipment->order->update([
                    'status' => OrderStatus::SHIPPED,
                ]);
            }

            if ($dto->status === ShipmentStatus::DELIVERED) {
                $shipment->order->update([
                    'status' => OrderStatus::COMPLETED,
                ]);
            }

            return $shipment->load([
                'order',
                'deliveryService',
                'trackingEvents',
            ]);
        });
    }
}
