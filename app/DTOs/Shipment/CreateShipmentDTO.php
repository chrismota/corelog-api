<?php

namespace App\DTOs\Shipment;

use App\Http\Requests\Api\V1\Shipment\StoreShipmentRequest;

class CreateShipmentDTO
{
    public function __construct(
        public readonly string $orderId,
        public readonly string $deliveryServiceId,
        public readonly ?string $trackingCode,
    ) {
    }

    public static function fromRequest(
        StoreShipmentRequest $request
    ): self {
        $data = $request->validated();

        return new self(
            orderId: $data['order_id'],
            deliveryServiceId: $data['delivery_service_id'],
            trackingCode: $data['tracking_code'] ?? null,
        );
    }
}
