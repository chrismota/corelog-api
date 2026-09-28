<?php

namespace App\DTOs\Order;

use App\Http\Requests\Api\V1\Order\StoreOrderRequest;

class CreateOrderDTO
{
    /**
     * @param CreateOrderItemDTO[] $items
     */
    public function __construct(
        public readonly string $customerId,
        public readonly string $addressId,
        public readonly array $items,
        public readonly string $shippingCost,
        public readonly string $discount,
    ) {
    }

    public static function fromRequest(StoreOrderRequest $request): self
    {
        $data = $request->validated();

        return new self(
            customerId: $data['customer_id'],
            addressId: $data['address_id'],
            items: array_map(
                fn (array $item) => CreateOrderItemDTO::fromArray($item),
                $data['items'],
            ),
            shippingCost: (string) $data['shipping_cost'],
            discount: (string) $data['discount'],
        );
    }
}
