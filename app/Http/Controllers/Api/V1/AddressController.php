<?php

namespace App\Http\Controllers\Api\V1;

use App\DTOs\Address\CreateAddressDTO;
use App\DTOs\Address\UpdateAddressDTO;
use App\Http\Controllers\Controller;
use App\Http\Requests\Api\V1\Address\StoreAddressRequest;
use App\Http\Requests\Api\V1\Address\UpdateAddressRequest;
use App\Http\Resources\Api\V1\AddressResource;
use App\Models\Address;
use App\Models\Customer;
use App\Services\AddressService;
use Illuminate\Http\Response;

class AddressController extends Controller
{
    public function __construct(
        private readonly AddressService $addressService,
    ) {
    }

    public function index(Customer $customer)
    {
        $this->authorize('viewAny', $customer);

        $addresses = $this->addressService->list($customer);

        return AddressResource::collection($addresses);
    }

    public function show(Customer $customer, Address $address): AddressResource
    {
        $this->authorize('view', $address);

        $addressModel = $this->addressService->show(
            $address,
        );

        return new AddressResource($addressModel);
    }

    public function store(StoreAddressRequest $request, Customer $customer): AddressResource
    {
        $this->authorize('create', $customer);

        $address = $this->addressService->create(
            $customer,
            CreateAddressDTO::fromRequest($request),
        );

        return new AddressResource($address);
    }

    public function update(UpdateAddressRequest $request, Customer $customer, Address $address): AddressResource
    {
        $this->authorize('update', $address);

        $updatedAddress = $this->addressService->update($address,
            UpdateAddressDTO::fromRequest($request),
        );

        return new AddressResource($updatedAddress);
    }

    public function destroy(Customer $customer, Address $address): Response
    {
        $this->authorize('delete', $address);

        $this->addressService->destroy(
            $address
        );

        return response()->noContent();
    }
}
