<?php

namespace App\DTOs\Customer;

use App\Http\Requests\Api\V1\Customer\IndexCustomerRequest;

class IndexCustomerDTO
{
    public function __construct(
        public readonly ?string $name,
        public readonly ?string $document,
        public readonly ?string $email,
        public readonly ?string $phone,
    ) {}

    public static function fromRequest(
        IndexCustomerRequest $request
    ): self {
        $data = $request->validated();

        return new self(
            name: $data['name'] ?? null,
            document: $data['document'] ?? null,
            email: $data['email'] ?? null,
            phone: $data['phone'] ?? null,
        );
    }
}
