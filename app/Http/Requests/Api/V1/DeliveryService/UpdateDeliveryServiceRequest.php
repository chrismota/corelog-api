<?php

namespace App\Http\Requests\Api\V1\DeliveryService;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateDeliveryServiceRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'name' => [
                'required',
                'string',
                'max:255',
            ],
            'code' => [
                'required',
                'string',
                'max:50',
                Rule::unique('delivery_services', 'code')
                    ->ignore($this->route('deliveryService')),
            ],
            'active' => [
                'required',
                'boolean',
            ],
        ];
    }
}
