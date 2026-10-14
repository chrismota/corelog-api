<?php

namespace App\Services;

use App\Contexts\OrganizationContext;
use App\Models\Customer;
use Illuminate\Validation\ValidationException;

class OrganizationAccessService
{
    public function __construct(
        private readonly OrganizationContext $organizationContext
    ) {}

    public function ensureCustomerBelongsToOrganization(Customer $customer): void
    {
        if (
            $customer->organization_id !==
            $this->organizationContext->id()
        ) {
            throw ValidationException::withMessages([
                'customer' => 'Cliente não pertence à organização atual.',
            ]);
        }
    }
}
