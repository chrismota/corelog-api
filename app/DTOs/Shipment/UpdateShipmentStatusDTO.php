<?php

namespace App\DTOs\Shipment;

use App\Enums\ShipmentStatus;
use App\Http\Requests\Api\V1\Shipment\UpdateShipmentStatusRequest;
use Carbon\Carbon;

class UpdateShipmentStatusDTO
{
    public function __construct(
        public readonly ShipmentStatus $status,
        public readonly string $description,
        public readonly ?string $location,
        public readonly ?Carbon $occurredAt,
    ) {
    }

    public static function fromRequest(
        UpdateShipmentStatusRequest $request
    ): self {
        $data = $request->validated();

        return new self(
            status: ShipmentStatus::from($data['status']),
            description: $data['description'],
            location: $data['location'] ?? null,
            occurredAt: isset($data['occurred_at'])
                ? Carbon::parse($data['occurred_at'])
                : null,
        );
    }
}
