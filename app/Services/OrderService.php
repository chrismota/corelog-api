<?php

namespace App\Services;

use App\Contexts\OrganizationContext;
use App\DTOs\Order\CreateOrderDTO;
use App\DTOs\Order\IndexOrderDTO;
use App\DTOs\Order\UpdateOrderStatusDTO;
use App\Enums\OrderStatus;
use App\Models\Customer;
use App\Models\Order;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class OrderService
{
    /**
     * Create a new class instance.
     */
    public function __construct(
        private readonly OrganizationContext $organizationContext
    ){}

    public function list(IndexOrderDTO $dto): LengthAwarePaginator
    {
        $organizationId = $this->organizationContext->id();

        $query = Order::query()
            ->where('organization_id', $organizationId)
            ->with([
                'customer',
                'items',
            ]);

        if($dto->status !== null) {
            $query->where('status', $dto->status);
        }

        if($dto->orderNumber !== null) {
            $query->where('order_number', $dto->orderNumber);
        }

        if($dto->customerId !== null) {
            $query->where('customer_id', $dto->customerId);
        }

        if($dto->dateFrom !== null) {
            $query->where('created_at', '>=', $dto->dateFrom);
        }

        if($dto->dateTo !== null) {
            $query->where('created_at', '<=', $dto->dateTo);
        }

        return $query
            ->latest()
            ->paginate(15);
    }

    public function show(Order $order): Order
    {
        return $order->load([
            'customer',
            'items',
        ]);
    }

     public function create(CreateOrderDTO $dto): Order
    {
        return DB::transaction(function () use ($dto) {
            $organizationId = $this->organizationContext->id();

            $customer = Customer::query()
                ->where('organization_id', $organizationId)
                ->find($dto->customerId);

            if (!$customer) {
                throw ValidationException::withMessages([
                    'customer_id' => 'Cliente não encontrado.',
                ]);
            }

            $address = $customer->addresses()
                ->whereKey($dto->addressId)
                ->first();

            if (!$address) {
                throw ValidationException::withMessages([
                    'address_id' => 'Endereço não pertence ao cliente informado.',
                ]);
            }

            $subtotal = 0.00;

            foreach ($dto->items as $item) {
                $itemTotal = round($item->unitPrice * $item->quantity, 2);

                $subtotal += $itemTotal;
            }

            $total = round($subtotal + $dto->shippingCost - $dto->discount, 2);

            if ($total < 0) {
                throw ValidationException::withMessages([
                    'discount' => 'O desconto não pode ser maior que o valor do pedido.',
                ]);
            }

            $order = Order::create([
                'organization_id' => $organizationId,
                'customer_id' => $customer->id,

                'order_number' => $this->generateOrderNumber(),

                'status' => OrderStatus::PENDING,

                'shipping_name' => $customer->name,
                'shipping_zip_code' => $address->zip_code,
                'shipping_street' => $address->street,
                'shipping_number' => $address->number,
                'shipping_complement' => $address->complement,
                'shipping_neighborhood' => $address->neighborhood,
                'shipping_city' => $address->city,
                'shipping_state' => $address->state,

                'subtotal' => $subtotal,
                'shipping_cost' => $dto->shippingCost,
                'discount' => $dto->discount,
                'total' => $total,
            ]);

            foreach ($dto->items as $item) {
                $itemTotal = round($item->unitPrice * $item->quantity, 2);

                $order->items()->create([
                    'product_name' => $item->productName,
                    'quantity' => $item->quantity,
                    'unit_price' => $item->unitPrice,
                    'total_price' => $itemTotal,
                ]);
            }

            return $order->load([
                'customer',
                'items',
            ]);
        });
    }

    public function updateStatus(Order $order, UpdateOrderStatusDTO $dto): Order
    {
        return DB::transaction(function () use ($order, $dto) {
            if (!$order) {
                throw ValidationException::withMessages([
                    'order' => 'Pedido não encontrado.',
                ]);
            }

            if ($dto->status === OrderStatus::SHIPPED) {
                throw ValidationException::withMessages([
                    'status' => 'O pedido deve ser enviado através da sua remessa.',
                ]);
            }

            if ($dto->status === OrderStatus::COMPLETED) {
                throw ValidationException::withMessages([
                    'status' => 'O pedido é concluído automaticamente quando a remessa é entregue.',
                ]);
            }

            if (!$order->status->canTransitionTo($dto->status)) {
                throw ValidationException::withMessages([
                    'status' => 'Não é possível alterar o pedido de '
                        . $order->status->value
                        . ' para '
                        . $dto->status->value
                        . '.',
                ]);
            }

            $order->update([
                'status' => $dto->status,
            ]);

            return $order->refresh();
        });

    }

    private function generateOrderNumber(): string
    {
        $number = DB::selectOne(
            "SELECT nextval('order_number_seq') AS number"
        )->number;

        return now()->format('Y') . str_pad(
            (string) $number,
            6,
            '0',
            STR_PAD_LEFT
        );
    }
}
