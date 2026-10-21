<?php

namespace App\Http\Requests\Api\V1\Shipment;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

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
                Rule::exists('orders', 'id')
                    ->where('organization_id', $this->user()->organization_id),
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
