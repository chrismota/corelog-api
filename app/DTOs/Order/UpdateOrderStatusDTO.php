<?php

namespace App\DTOs\Order;

use App\Enums\OrderStatus;
use App\Http\Requests\Api\V1\Order\UpdateOrderStatusRequest;

class UpdateOrderStatusDTO
{
    public function __construct(
        public readonly OrderStatus $status,
    ) {
    }

    public static function fromRequest(
        UpdateOrderStatusRequest $request
    ): self {
        $data = $request->validated();

        return new self(
            status: OrderStatus::from($data['status']),
        );
    }
}
