<?php

namespace App\DTOs\Customer;

use App\Http\Requests\Api\V1\Customer\UpdateCustomerRequest;

final class UpdateCustomerDTO
{
    public function __construct(
        public readonly string $name,
        public readonly string $document,
        public readonly string $email,
        public readonly string $phone,
    ) {}

    public static function fromRequest(
        UpdateCustomerRequest $request
    ): self {
        return new self(
            name: $request->validated('name'),
            document: $request->validated('document'),
            email: $request->validated('email'),
            phone: $request->validated('phone'),
        );
    }
}
