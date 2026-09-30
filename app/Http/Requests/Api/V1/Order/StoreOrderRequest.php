<?php

namespace App\Http\Requests\Api\V1\Order;

use Illuminate\Foundation\Http\FormRequest;

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
                'exists:customers,id',
            ],

            'address_id' => [
                'required',
                'uuid',
                'exists:addresses,id',
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
