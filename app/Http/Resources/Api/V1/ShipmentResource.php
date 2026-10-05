<?php

namespace App\Http\Resources\Api\V1;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ShipmentResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,

            'order_id' => $this->order_id,

            'delivery_service' => [
                'id' => $this->deliveryService->id,
                'name' => $this->deliveryService->name,
                'code' => $this->deliveryService->code,
            ],

            'tracking_code' => $this->tracking_code,

            'status' => $this->status->value,

            'shipped_at' => $this->shipped_at,
            'delivered_at' => $this->delivered_at,

            'tracking_events' => TrackingEventResource::collection(
                $this->whenLoaded('trackingEvents')
            ),

            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
    }
}
