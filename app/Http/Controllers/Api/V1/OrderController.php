<?php

namespace App\Http\Controllers\Api\V1;

use App\DTOs\Order\CreateOrderDTO;
use App\Http\Controllers\Controller;
use App\Http\Requests\Api\V1\Order\StoreOrderRequest;
use App\Http\Resources\Api\V1\OrderResource;
use App\Services\OrderService;

class OrderController extends Controller
{
    public function __construct(
        private OrderService $orderService
    ) {}

    public function store(StoreOrderRequest $request)
    {
        $order = $this->orderService->create(
            CreateOrderDTO::fromRequest($request)
        );

        return new OrderResource($order);
    }
}
