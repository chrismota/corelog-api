<?php

namespace App\Http\Resources\Api\V1;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class OrderResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'order_number' => $this->order_number,
            'status' => $this->status,

            'customer' => [
                'id' => $this->customer->id,
                'name' => $this->customer->name,
            ],

            'shipping_address' => [
                'name' => $this->shipping_name,
                'zip_code' => $this->shipping_zip_code,
                'street' => $this->shipping_street,
                'number' => $this->shipping_number,
                'complement' => $this->shipping_complement,
                'neighborhood' => $this->shipping_neighborhood,
                'city' => $this->shipping_city,
                'state' => $this->shipping_state,
            ],

            'items' => $this->items->map(fn ($item) => [
                'id' => $item->id,
                'product_name' => $item->product_name,
                'quantity' => $item->quantity,
                'unit_price' => $item->unit_price,
                'total_price' => $item->total_price,
            ]),

            'subtotal' => $this->subtotal,
            'shipping_cost' => $this->shipping_cost,
            'discount' => $this->discount,
            'total' => $this->total,

            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
    }
}
