<?php

namespace App\Http\Requests\Api\V1\Order;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreOrderRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'customer_id' => [
                'required',
                'uuid',
                Rule::exists('customers', 'id')
                    ->where('organization_id', $this->user()->organization_id),
            ],

            'address_id' => [
                'required',
                'uuid',
                Rule::exists('addresses', 'id')
                    ->where('customer_id', $this->input('customer_id')),
            ],

            'items' => [
                'required',
                'array',
                'min:1',
            ],

            'items.*.product_name' => [
                'required',
                'string',
                'max:255',
            ],

            'items.*.quantity' => [
                'required',
                'integer',
                'min:1',
            ],

            'items.*.unit_price' => [
                'required',
                'numeric',
                'min:0',
                'decimal:0,2',
            ],

            'shipping_cost' => [
                'required',
                'numeric',
                'min:0',
                'decimal:0,2',
            ],

            'discount' => [
                'required',
                'numeric',
                'min:0',
                'decimal:0,2',
            ],
        ];
    }
}
