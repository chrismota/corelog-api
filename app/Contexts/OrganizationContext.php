<?php

namespace App\Contexts;

use App\Models\Organization;

class OrganizationContext
{
    private ?Organization $organization = null;

    public function set(Organization $organization): void
    {
        $this->organization = $organization;
    }

    public function get(): Organization
    {
        if (!$this->organization) {
            throw new \RuntimeException('Organization context has not been set.');
        }

        return $this->organization;
    }

    public function id(): string
    {
        return $this->get()->id;
    }

    public function clear(): void
    {
        $this->organization = null;
    }
}
