<?php

namespace App\DTOs\Address;

use App\Http\Requests\Api\V1\Address\UpdateAddressRequest;

class UpdateAddressDTO
{
    public function __construct(
        public readonly string $zipCode,
        public readonly string $street,
        public readonly string $number,
        public readonly ?string $complement,
        public readonly string $neighborhood,
        public readonly string $city,
        public readonly string $state,
    ) {
    }

    public static function fromRequest(UpdateAddressRequest $request): self
    {
        $data = $request->validated();

        return new self(
            zipCode: $data['zip_code'],
            street: $data['street'],
            number: $data['number'],
            complement: $data['complement'] ?? null,
            neighborhood: $data['neighborhood'],
            city: $data['city'],
            state: strtoupper($data['state']),
        );
    }
}
