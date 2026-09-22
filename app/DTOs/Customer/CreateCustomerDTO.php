<?php

namespace App\DTOs\Customer;

use App\Http\Requests\Api\V1\Customer\StoreCustomerRequest;

final class CreateCustomerDTO
{
    public function __construct(
        public readonly string $name,
        public readonly string $document,
        public readonly string $email,
        public readonly string $phone,
    ) {}

    public static function fromRequest(
        StoreCustomerRequest $request
    ): self {
        return new self(
            name: $request->validated('name'),
            document: $request->validated('document'),
            email: $request->validated('email'),
            phone: $request->validated('phone'),
        );
    }
}
