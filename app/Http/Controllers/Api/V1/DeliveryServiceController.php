<?php

namespace App\Http\Controllers\Api\V1;

use App\DTOs\DeliveryService\CreateDeliveryServiceDTO;
use App\DTOs\DeliveryService\UpdateDeliveryServiceDTO;
use App\Http\Controllers\Controller;
use App\Http\Requests\Api\V1\DeliveryService\StoreDeliveryServiceRequest;
use App\Http\Requests\Api\V1\DeliveryService\UpdateDeliveryServiceRequest;
use App\Http\Resources\Api\V1\DeliveryServiceResource;
use App\Models\DeliveryService;
use App\Services\DeliveryServiceService;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

class DeliveryServiceController extends Controller
{
    public function __construct(
        private readonly DeliveryServiceService $deliveryServiceService,
    ) {
    }

    public function index(Request $request): AnonymousResourceCollection
    {
        $active = $request->has('active')
            ? $request->boolean('active')
            : null;

        $deliveryServices = $this->deliveryServiceService->list($active);

        return DeliveryServiceResource::collection($deliveryServices);
    }

    public function show(DeliveryService $deliveryService): DeliveryService
    {
        return $this->deliveryServiceService->show($deliveryService);
    }

    public function store(StoreDeliveryServiceRequest $request): DeliveryServiceResource
    {
        $deliveryService = $this->deliveryServiceService->create(
            CreateDeliveryServiceDTO::fromRequest($request),
        );

        return new DeliveryServiceResource($deliveryService);
    }

    public function update(UpdateDeliveryServiceRequest $request, DeliveryService $deliveryService): DeliveryServiceResource
    {
        $deliveryServiceModel = $this->deliveryServiceService->update(
            $deliveryService,
            UpdateDeliveryServiceDTO::fromRequest($request),
        );

        return new DeliveryServiceResource($deliveryServiceModel);
    }
}
