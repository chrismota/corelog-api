<?php

namespace App\Services;

use App\Contexts\OrganizationContext;
use App\DTOs\Address\CreateAddressDTO;
use App\DTOs\Address\UpdateAddressDTO;
use App\Models\Address;
use App\Models\Customer;
use Illuminate\Validation\ValidationException;

class AddressService
{
    public function __construct(
        private readonly OrganizationContext $organizationContext,
    ) {
    }

    public function list(Customer $customer)
    {
        $organizationId = $this->organizationContext->id();

        if ($customer->organization_id !== $organizationId) {
            throw ValidationException::withMessages([
                'customer' => 'Cliente não pertence à organização atual.',
            ]);
        }

        return $customer->addresses()
            ->latest()
            ->get();
    }

    public function show(Address $address): Address
    {
        return $address;
    }

    public function create(Customer $customer, CreateAddressDTO $dto): Address
    {
        $organizationId = $this->organizationContext->id();

        if ($customer->organization_id !== $organizationId) {
            throw ValidationException::withMessages([
                'customer' => 'Cliente não pertence à organização atual.',
            ]);
        }

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
        $address->delete();
    }
}
