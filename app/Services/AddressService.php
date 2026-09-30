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

    public function show(Customer $customer, string $addressId,): Address
    {
        $organizationId = $this->organizationContext->id();

        if ($customer->organization_id !== $organizationId) {
            throw ValidationException::withMessages([
                'customer' => 'Cliente não pertence à organização atual.',
            ]);
        }

        $address = $customer->addresses()
            ->whereKey($addressId)
            ->first();

        if (!$address) {
            throw ValidationException::withMessages([
                'address' => 'Endereço não encontrado.',
            ]);
        }

        return $address;
    }

    public function create(
        Customer $customer,
        CreateAddressDTO $dto,
    ): Address {
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

    public function update(Customer $customer, string $addressId, UpdateAddressDTO $dto): Address
    {
        $organizationId = $this->organizationContext->id();

        if ($customer->organization_id !== $organizationId) {
            throw ValidationException::withMessages([
                'customer' => 'Cliente não pertence à organização atual.',
            ]);
        }

        $address = $customer->addresses()
            ->whereKey($addressId)
            ->first();

        if (!$address) {
            throw ValidationException::withMessages([
                'address' => 'Endereço não encontrado.',
            ]);
        }

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

    public function destroy(Customer $customer, string $addressId): void
    {
        $organizationId = $this->organizationContext->id();

        if ($customer->organization_id !== $organizationId) {
            throw ValidationException::withMessages([
                'customer' => 'Cliente não pertence à organização atual.',
            ]);
        }

        $address = $customer->addresses()
            ->whereKey($addressId)
            ->first();

        if (!$address) {
            throw ValidationException::withMessages([
                'address' => 'Endereço não encontrado.',
            ]);
        }

        $address->delete();
    }
}
