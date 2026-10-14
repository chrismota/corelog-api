<?php

namespace App\Services;

use App\Contexts\OrganizationContext;
use App\DTOs\Address\CreateAddressDTO;
use App\DTOs\Address\UpdateAddressDTO;
use App\Models\Address;
use App\Models\Customer;

class AddressService
{
    public function __construct(
        private readonly OrganizationContext $organizationContext,
        private readonly OrganizationAccessService $organizationAccessService
    ) {
    }

    public function list(Customer $customer)
    {
        $this->organizationAccessService->ensureCustomerBelongsToOrganization($customer);

        return $customer->addresses()
            ->latest()
            ->get();
    }

    public function show(Address $address): Address
    {
        $this->organizationAccessService->ensureCustomerBelongsToOrganization($address->customer);

        return $address;
    }

    public function create(Customer $customer, CreateAddressDTO $dto): Address
    {
        $this->organizationAccessService->ensureCustomerBelongsToOrganization($customer);

        return $customer->addresses()->create([
            'zip_code' => $dto->zipCode,
            'street' => $dto->street,
            'number' => $dto->number,
            'complement' => $dto->complement,
            'neighborhood' => $dto->neighborhood,
            'city' => $dto->city,
            'state' => $dto->state,
        ]);
    }

    public function update(Address $address, UpdateAddressDTO $dto): Address
    {
        $this->organizationAccessService->ensureCustomerBelongsToOrganization($address->customer);

        $address->update([
            'zip_code' => $dto->zipCode,
            'street' => $dto->street,
            'number' => $dto->number,
            'complement' => $dto->complement,
            'neighborhood' => $dto->neighborhood,
            'city' => $dto->city,
            'state' => $dto->state,
        ]);

        return $address->refresh();
    }

    public function destroy(Address $address): void
    {
        $this->organizationAccessService->ensureCustomerBelongsToOrganization($address->customer);

        $address->delete();
    }
}
