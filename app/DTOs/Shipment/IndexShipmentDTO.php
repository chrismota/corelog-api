<?php

namespace App\DTOs\Shipment;

use App\Enums\ShipmentStatus;
use App\Http\Requests\Api\V1\Shipment\IndexShipmentRequest;

class IndexShipmentDTO
{
    public function __construct(
        public readonly ?ShipmentStatus $status,
        public readonly ?string $trackingCode,
        public readonly ?string $deliveryServiceId,
        public readonly ?string $orderId,
        public readonly ?string $shippedFrom,
        public readonly ?string $shippedTo,
        public readonly ?string $deliveredFrom,
        public readonly ?string $deliveredTo,
    ) {
    }

    public static function fromRequest(
        IndexShipmentRequest $request
    ): self {
        $data = $request->validated();

        return new self(
            status: isset($data['status'])
                ? ShipmentStatus::from($data['status'])
                : null,
            trackingCode: $data['tracking_code'] ?? null,
            deliveryServiceId: $data['delivery_service_id'] ?? null,
            orderId: $data['order_id'] ?? null,
            shippedFrom: $data['shipped_from'] ?? null,
            shippedTo: $data['shipped_to'] ?? null,
            deliveredFrom: $data['delivered_from'] ?? null,
            deliveredTo: $data['delivered_to'] ?? null,
        );
    }
}
