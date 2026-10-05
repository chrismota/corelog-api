<?php

namespace App\Http\Requests\Api\V1\Shipment;

use Illuminate\Foundation\Http\FormRequest;

class StoreShipmentRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'order_id' => [
                'required',
                'uuid',
                'exists:orders,id',
            ],
            'delivery_service_id' => [
                'required',
                'uuid',
                'exists:delivery_services,id',
            ],
            'tracking_code' => [
                'nullable',
                'string',
                'max:255',
            ],
        ];
    }
}
