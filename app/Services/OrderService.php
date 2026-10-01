<?php

namespace App\Services;

use App\Contexts\OrganizationContext;
use App\DTOs\Order\CreateOrderDTO;
use App\Enums\OrderStatus;
use App\Models\Customer;
use App\Models\Order;
use Illuminate\Database\Eloquent\Collection;
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

    public function list(): Collection
    {
        $organizationId = $this->organizationContext->id();

        return Order::query()
            ->where('organization_id', $organizationId)
            ->with([
                'customer',
                'items',
            ])
            ->latest()
            ->get();
    }

    public function show(string $orderId): Order
    {
        $organizationId = $this->organizationContext->id();

        $order = Order::query()
            ->where('organization_id', $organizationId)
            ->with([
                'customer',
                'items',
            ])
            ->find($orderId);

        if (!$order) {
            throw ValidationException::withMessages([
                'order' => 'Pedido não encontrado.',
            ]);
        }

        return $order;
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
