<?php

namespace App\Services;

use App\Contexts\OrganizationContext;
use App\DTOs\Customer\CreateCustomerDTO;
use App\DTOs\Customer\UpdateCustomerDTO;
use App\Models\Customer;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

class CustomerService
{
    public function __construct(
        private OrganizationContext $organizationContext
    ) {}

    public function index(): LengthAwarePaginator
    {
        return Customer::query()
            ->where(
                'organization_id',
                $this->organizationContext->id()
            )
            ->orderBy('created_at', 'desc')
            ->paginate(15);
    }

    public function show(string $customerId): Customer
    {
        return Customer::query()
            ->where('organization_id', $this->organizationContext->id())
            ->findOrFail($customerId);
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

    public function update(string $customerId, UpdateCustomerDTO $dto): Customer
    {
        $customer = Customer::query()
            ->where(
                'organization_id',
                $this->organizationContext->id()
            )
            ->findOrFail($customerId);

        $customer->update([
            'name' => $dto->name,
            'document' => $dto->document,
            'email' => $dto->email,
            'phone' => $dto->phone,
        ]);

        return $customer->refresh();
    }

    public function delete(string $customerId): void
    {
        $customer = Customer::query()
            ->where(
                'organization_id',
                $this->organizationContext->id()
            )
            ->findOrFail($customerId);

        $customer->delete();
    }

}
