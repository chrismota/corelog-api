<?php

namespace App\Http\Requests\Api\V1\Customer;

use Illuminate\Foundation\Http\FormRequest;

class StoreCustomerRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:150'],
            'document' => ['required', 'string', 'max:20'],
            'email' => ['required', 'email'],
            'phone' => ['required', 'string', 'max:20'],
        ];
    }
}
