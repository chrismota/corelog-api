<?php

namespace App\Http\Requests\Api\V1\Order;

use App\Enums\OrderStatus;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class IndexOrderRequest extends FormRequest
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
                Rule::enum(OrderStatus::class),
            ],
            'order_number' => [
                'sometimes',
                'string',
                'max:20',
            ],
            'customer_id' => [
                'sometimes',
                'uuid',
            ],
            'date_from' => [
                'sometimes',
                'date',
            ],
            'date_to' => [
                'sometimes',
                'date',
                'after_or_equal:date_from',
            ],
        ];
    }
}
