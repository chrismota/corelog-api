<?php

namespace App\DTOs\DeliveryService;

use App\Http\Requests\Api\V1\DeliveryService\StoreDeliveryServiceRequest;

class DeliveryServiceDTO
{
    public function __construct(
        public readonly string $name,
        public readonly string $code,
        public readonly bool $active,
    ) {
    }

    public static function fromRequest(
        StoreDeliveryServiceRequest $request
    ): self {
        $data = $request->validated();

        return new self(
            name: $data['name'],
            code: strtoupper($data['code']),
            active: $data['active'] ?? true,
        );
    }
}
