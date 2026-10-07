<?php

namespace App\DTOs\Order;

use App\Enums\OrderStatus;
use App\Http\Requests\Api\V1\Order\IndexOrderRequest;

class IndexOrderDTO
{
    public function __construct(
        public readonly ?OrderStatus $status,
        public readonly ?string $orderNumber,
        public readonly ?string $customerId,
        public readonly ?string $dateFrom,
        public readonly ?string $dateTo,
    ) {
    }

    public static function fromRequest(IndexOrderRequest $request): self
    {
        $data = $request->validated();

        return new self(
            status: isset($data['status'])
                ? OrderStatus::from($data['status'])
                : null,
            orderNumber: $data['order_number'] ?? null,
            customerId: $data['customer_id'] ?? null,
            dateFrom: $data['date_from'] ?? null,
            dateTo: $data['date_to'] ?? null,
        );
    }
}
