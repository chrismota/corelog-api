<?php

namespace App\Policies;

use App\Models\Shipment;
use App\Models\User;

class ShipmentPolicy
{
    public function viewAny(User $user): bool
    {
        return true;
    }

    public function view(User $user, Shipment $shipment): bool
    {
        return $user->organization_id === $shipment->order->organization_id;
    }

    public function create(User $user): bool
    {
        return true;
    }

    public function updateStatus(User $user, Shipment $shipment): bool
    {
        return $user->organization_id === $shipment->order->organization_id;
    }
}
