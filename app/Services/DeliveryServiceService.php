<?php

namespace App\Services;

use App\DTOs\DeliveryService\DeliveryServiceDTO;
use App\Models\DeliveryService;
use Illuminate\Database\Eloquent\Collection;

class DeliveryServiceService
{
    public function list(?bool $active = null): Collection
    {
        $query = DeliveryService::query();

        if ($active !== null) {
            $query->where('active', $active);
        }

        return $query
            ->orderBy('name')
            ->get();
    }

    public function create(DeliveryServiceDTO $dto): DeliveryService
    {
        return DeliveryService::create([
            'name' => $dto->name,
            'code' => $dto->code,
            'active' => $dto->active,
        ]);
    }
}
