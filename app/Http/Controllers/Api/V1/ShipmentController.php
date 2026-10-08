<?php

namespace App\Http\Controllers\Api\V1;

use App\DTOs\Shipment\CreateShipmentDTO;
use App\DTOs\Shipment\IndexShipmentDTO;
use App\DTOs\Shipment\UpdateShipmentStatusDTO;
use App\Http\Controllers\Controller;
use App\Http\Requests\Api\V1\Shipment\IndexShipmentRequest;
use App\Http\Requests\Api\V1\Shipment\StoreShipmentRequest;
use App\Http\Requests\Api\V1\Shipment\UpdateShipmentStatusRequest;
use App\Http\Resources\Api\V1\ShipmentResource;
use Illuminate\Http\Resources\Json\ResourceCollection as ShipmentResourceCollection;
use App\Services\ShipmentService;

class ShipmentController extends Controller
{
    public function __construct(
        private readonly ShipmentService $shipmentService,
    ) {
    }

    public function index(IndexShipmentRequest $request): ShipmentResourceCollection
    {
        $shipments = $this->shipmentService->list(
            IndexShipmentDTO::fromRequest($request),
        );

        return ShipmentResource::collection($shipments);
    }

    public function show(Shipment $shipment): ShipmentResource
    {
        $this->authorize('view', $shipment);

        $shipment = $this->shipmentService->show($shipment);

        return new ShipmentResource($shipment);
    }

    public function store(StoreShipmentRequest $request): ShipmentResource
    {
        $shipment = $this->shipmentService->create(
            CreateShipmentDTO::fromRequest($request),
        );

        return new ShipmentResource($shipment);
    }

    public function updateStatus(UpdateShipmentStatusRequest $request, Shipment $shipment): ShipmentResource
    {
        $this->authorize('updateStatus', $shipment);

        $shipment = $this->shipmentService->updateStatus(
            $shipment,
            UpdateShipmentStatusDTO::fromRequest($request),
        );

        return new ShipmentResource($shipment);
    }
}
