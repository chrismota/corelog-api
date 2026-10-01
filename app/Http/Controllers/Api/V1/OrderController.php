<?php

namespace App\Http\Controllers\Api\V1;

use App\DTOs\Order\CreateOrderDTO;
use App\Http\Controllers\Controller;
use App\Http\Requests\Api\V1\Order\StoreOrderRequest;
use App\Http\Resources\Api\V1\OrderResource;
use Illuminate\Http\Resources\Json\ResourceCollection as OrderResourceCollection;
use App\Services\OrderService;

class OrderController extends Controller
{
    public function __construct(
        private OrderService $orderService
    ) {}

    public function index(): OrderResourceCollection
    {
        $orders = $this->orderService->list();

        return OrderResource::collection($orders);
    }

    public function show(string $order): OrderResource
    {
        $orderModel = $this->orderService->show($order);

        return new OrderResource($orderModel);
    }

    public function store(StoreOrderRequest $request)
    {
        $order = $this->orderService->create(
            CreateOrderDTO::fromRequest($request)
        );

        return new OrderResource($order);
    }
}
