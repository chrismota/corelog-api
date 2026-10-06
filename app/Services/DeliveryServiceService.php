<?php

namespace App\Services;

use App\DTOs\DeliveryService\CreateDeliveryServiceDTO;
use App\DTOs\DeliveryService\UpdateDeliveryServiceDTO;
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

    public function show(DeliveryService $deliveryService): DeliveryService
    {
        return $deliveryService;
    }

    public function create(CreateDeliveryServiceDTO $dto): DeliveryService
    {
        return DeliveryService::create([
            'name' => $dto->name,
            'code' => $dto->code,
            'active' => $dto->active,
        ]);
    }

    public function update(DeliveryService $deliveryService, UpdateDeliveryServiceDTO $dto): DeliveryService
    {
        $deliveryService->update([
            'name' => $dto->name,
            'code' => $dto->code,
            'active' => $dto->active,
        ]);

        return $deliveryService->refresh();
    }
}
