<?php

namespace App\Http\Requests\Api\V1\Customer;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreCustomerRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $organizationId = $this->user()->organization_id;

        return [
            'name' => ['required', 'string', 'max:150'],
            'document' => [
                'required',
                'string',
                'max:20',
                Rule::unique('customers', 'document')
                    ->where('organization_id', $organizationId),
            ],
            'email' => [
                'required',
                'email',
                'max:255',
                Rule::unique('customers', 'email')
                    ->where('organization_id', $organizationId),
            ],
            'phone' => ['required', 'string', 'max:20'],
        ];
    }
}
