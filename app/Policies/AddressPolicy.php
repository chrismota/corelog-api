<?php

namespace App\Policies;

use App\Models\Address;
use App\Models\Customer;
use App\Models\User;

class AddressPolicy
{
    public function viewAny(User $user, Customer $customer): bool
    {
        return $user->organization_id === $customer->organization_id;
    }

    public function view(User $user, Address $address): bool
    {
        return $user->organization_id === $address->customer->organization_id;
    }

    public function create(User $user, Customer $customer): bool
    {
        return $user->organization_id === $customer->organization_id;
    }

    public function update(User $user, Address $address): bool
    {
        return $user->organization_id === $address->customer->organization_id;
    }

    public function delete(User $user, Address $address): bool
    {
        return $user->organization_id === $address->customer->organization_id;
    }
}
