<?php

namespace App\DTOs\DeliveryService;

use App\Http\Requests\Api\V1\DeliveryService\UpdateDeliveryServiceRequest;

class UpdateDeliveryServiceDTO
{
    public function __construct(
        public readonly string $name,
        public readonly string $code,
        public readonly bool $active,
    ) {
    }

    public static function fromRequest(
        UpdateDeliveryServiceRequest $request
    ): self {
        $data = $request->validated();

        return new self(
            name: $data['name'],
            code: $data['code'],
            active: (bool) $data['active'],
        );
    }
}
