<?php

namespace App\Http\Requests\Api\V1\Customer;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateCustomerRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $organizationId = $this->user()->organization_id;
        $customer = $this->route('customer');

        return [
            'name' => ['required', 'string', 'max:150'],

            'document' => [
                'required',
                'string',
                'max:20',
                Rule::unique('customers', 'document')
                    ->where('organization_id', $organizationId)
                    ->ignore($customer),
            ],

            'email' => [
                'required',
                'email',
                'max:255',
                Rule::unique('customers', 'email')
                    ->where('organization_id', $organizationId)
                    ->ignore($customer),
            ],

            'phone' => ['required', 'string', 'max:20'],
        ];
    }
}
