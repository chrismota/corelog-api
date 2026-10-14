<?php

namespace App\Http\Controllers\Api\V1;

use App\DTOs\Customer\CreateCustomerDTO;
use App\DTOs\Customer\IndexCustomerDTO;
use App\DTOs\Customer\UpdateCustomerDTO;
use App\Http\Controllers\Controller;
use App\Http\Requests\Api\V1\Customer\IndexCustomerRequest;
use App\Http\Requests\Api\V1\Customer\StoreCustomerRequest;
use App\Http\Requests\Api\V1\Customer\UpdateCustomerRequest;
use App\Http\Resources\Api\V1\CustomerResource;
use App\Models\Customer;
use App\Services\CustomerService;

class CustomerController extends Controller
{
    public function __construct(
        private CustomerService $customerService
    ) {}

    public function index(IndexCustomerRequest $request)
    {
        $this->authorize('viewAny', Customer::class);

        $customers = $this->customerService->index(
            IndexCustomerDTO::fromRequest($request)
        );

        return CustomerResource::collection($customers);
    }

    public function show(Customer $customer): CustomerResource
    {
        $this->authorize('view', $customer);

        return new CustomerResource($customer);
    }

    public function store(StoreCustomerRequest $request)
    {
        $this->authorize('create', Customer::class);

        $customer = $this->customerService->create(
            CreateCustomerDTO::fromRequest($request)
        );

        return new CustomerResource($customer);
    }

    public function update(UpdateCustomerRequest $request, Customer $customer)
    {
        $this->authorize('update', $customer);

        $customer = $this->customerService->update($customer, UpdateCustomerDTO::fromRequest($request));

        return new CustomerResource($customer);
    }

    public function destroy(Customer $customer)
    {
        $this->authorize('delete', $customer);

        $this->customerService->delete($customer);

        return response()->noContent();
    }
}
