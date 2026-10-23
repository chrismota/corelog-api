<?php

namespace Database\Factories;

use App\Models\Customer;
use App\Models\Organization;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Customer>
 */
class CustomerFactory extends Factory
{
    protected $model = Customer::class;

    public function definition(): array
    {
        return [
            'organization_id' => Organization::factory(),
            'name' => fake()->name(),
            'document' => fake()->unique()->numerify('###########'),
            'email' => fake()->unique()->safeEmail(),
            'phone' => fake()->numerify('###########'),

        ];
    }
}
