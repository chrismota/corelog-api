<?php

namespace App\Http\Controllers\Api\V1;

use App\DTOs\Address\CreateAddressDTO;
use App\DTOs\Address\UpdateAddressDTO;
use App\Http\Controllers\Controller;
use App\Http\Requests\Api\V1\Address\StoreAddressRequest;
use App\Http\Requests\Api\V1\Address\UpdateAddressRequest;
use App\Http\Resources\Api\V1\AddressResource;
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
        $addresses = $this->addressService->list($customer);

        return AddressResource::collection($addresses);
    }

    public function show(Customer $customer, string $address): AddressResource
    {
        $addressModel = $this->addressService->show(
            $customer,
            $address,
        );

        return new AddressResource($addressModel);
    }

    public function store(
        StoreAddressRequest $request,
        Customer $customer,
    ): AddressResource {
        $address = $this->addressService->create(
            $customer,
            CreateAddressDTO::fromRequest($request),
        );

        return new AddressResource($address);
    }

    public function update(UpdateAddressRequest $request, Customer $customer, string $address): AddressResource
    {
        $updatedAddress = $this->addressService->update(
            $customer,
            $address,
            UpdateAddressDTO::fromRequest($request),
        );

        return new AddressResource($updatedAddress);
    }

    public function destroy(Customer $customer,string $address): Response {
        $this->addressService->destroy(
            $customer,
            $address,
        );

        return response()->noContent();
    }
}
