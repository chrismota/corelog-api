<?php

namespace App\DTOs\Order;

class CreateOrderItemDTO
{
    public function __construct(
        public readonly string $productName,
        public readonly int $quantity,
        public readonly string $unitPrice,
    ) {
    }

    public static function fromArray(array $data): self
    {
        return new self(
            productName: $data['product_name'],
            quantity: (int) $data['quantity'],
            unitPrice: (string) $data['unit_price'],
        );
    }
}
