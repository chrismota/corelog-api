<?php

namespace App\Http\Controllers\Api\V1;

use App\DTOs\DeliveryService\DeliveryServiceDTO;
use App\Http\Controllers\Controller;
use App\Http\Requests\Api\V1\DeliveryService\StoreDeliveryServiceRequest;
use App\Http\Resources\Api\V1\DeliveryServiceResource;
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

    public function store(StoreDeliveryServiceRequest $request): DeliveryServiceResource
    {
        $deliveryService = $this->deliveryServiceService->create(
            DeliveryServiceDTO::fromRequest($request),
        );

        return new DeliveryServiceResource($deliveryService);
    }
}
