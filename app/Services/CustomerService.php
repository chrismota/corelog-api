<?php

namespace App\Services;

use App\Contexts\OrganizationContext;
use App\DTOs\Customer\CreateCustomerDTO;
use App\DTOs\Customer\IndexCustomerDTO;
use App\DTOs\Customer\UpdateCustomerDTO;
use App\Models\Customer;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

class CustomerService
{
    public function __construct(
        private OrganizationContext $organizationContext,
        private OrganizationAccessService $organizationAccessService
    ) {}

    public function index(IndexCustomerDTO $dto): LengthAwarePaginator
    {
        $organizationId = $this->organizationContext->id();

        $query = Customer::query()
            ->where('organization_id', $organizationId);

        if ($dto->name !== null) {
            $query->where('name', 'ilike', "%{$dto->name}%");
        }

        if ($dto->document !== null) {
            $query->where('document', 'ilike', "%{$dto->document}%");
        }

        if ($dto->email !== null) {
            $query->where('email', 'ilike', "%{$dto->email}%");
        }

        if ($dto->phone !== null) {
            $query->where('phone', 'ilike', "%{$dto->phone}%");
        }

        return $query
            ->latest()
            ->paginate(15)
            ->withQueryString();
    }

    public function show(Customer $customer): Customer
    {
        $this->organizationAccessService->ensureCustomerBelongsToOrganization($customer);

        return $customer;
    }

    public function create(CreateCustomerDTO $dto): Customer
    {
        return Customer::create([
            'organization_id' => $this->organizationContext->id(),
            'name' => $dto->name,
            'document' => $dto->document,
            'email' => $dto->email,
            'phone' => $dto->phone,
        ]);
    }

    public function update(Customer $customer, UpdateCustomerDTO $dto): Customer
    {
        $this->organizationAccessService->ensureCustomerBelongsToOrganization($customer);

        $customer->update([
            'name' => $dto->name,
            'document' => $dto->document,
            'email' => $dto->email,
            'phone' => $dto->phone,
        ]);

        return $customer->refresh();
    }

    public function delete(Customer $customer): void
    {
        $this->organizationAccessService->ensureCustomerBelongsToOrganization($customer);
        $customer->delete();
    }
}
