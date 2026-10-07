<?php

namespace App\Http\Requests\Api\V1\Shipment;

use App\Enums\ShipmentStatus;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class IndexShipmentRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'status' => [
                'sometimes',
                Rule::enum(ShipmentStatus::class),
            ],

            'tracking_code' => [
                'sometimes',
                'string',
                'max:255',
            ],

            'delivery_service_id' => [
                'sometimes',
                'uuid',
            ],

            'order_id' => [
                'sometimes',
                'uuid',
            ],

            'shipped_from' => [
                'sometimes',
                'date',
            ],

            'shipped_to' => [
                'sometimes',
                'date',
                'after_or_equal:shipped_from',
            ],

            'delivered_from' => [
                'sometimes',
                'date',
            ],

            'delivered_to' => [
                'sometimes',
                'date',
                'after_or_equal:delivered_from',
            ],
        ];
    }
}
