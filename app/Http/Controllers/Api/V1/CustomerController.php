<?php

namespace App\Http\Controllers\Api\V1;

use App\DTOs\Customer\CreateCustomerDTO;
use App\DTOs\Customer\UpdateCustomerDTO;
use App\Http\Controllers\Controller;
use App\Http\Requests\Api\V1\Customer\StoreCustomerRequest;
use App\Http\Requests\Api\V1\Customer\UpdateCustomerRequest;
use App\Http\Resources\CustomerResource;
use App\Services\CustomerService;
use Illuminate\Http\Request;

class CustomerController extends Controller
{
    public function __construct(
        private CustomerService $customerService
    ) {}

    public function index(Request $request)
    {
        return CustomerResource::collection(
            $this->customerService->index()
        );
    }

    public function show(string $customerId)
    {
        $customer = $this->customerService->show($customerId);
        return new CustomerResource($customer);
    }

    public function store(StoreCustomerRequest $request)
    {
        $customer = $this->customerService->create(
            CreateCustomerDTO::fromRequest($request)
        );

        return new CustomerResource($customer);
    }

    public function update(UpdateCustomerRequest $request, string $customerId)
    {
        $customer = $this->customerService->update($customerId, UpdateCustomerDTO::fromRequest($request));

        return new CustomerResource($customer);
    }

    public function destroy(string $customerId)
    {
        $this->customerService->delete($customerId);

        return response()->json([
            'message' => 'Customer deleted successfully'
        ]);
    }
}
