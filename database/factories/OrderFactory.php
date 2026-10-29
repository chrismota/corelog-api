<?php

namespace Database\Factories;

use App\Enums\OrderStatus;
use App\Models\Customer;
use App\Models\Organization;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<\App\Models\Order>
 */
class OrderFactory extends Factory
{
    public function definition(): array
    {
        return [
            'organization_id' => Organization::factory(),
            'customer_id' => Customer::factory(),

            'order_number' => now()->format('Y')
                . fake()->unique()->numerify('######'),

            'status' => OrderStatus::PENDING->value,

            'shipping_name' => fake()->name(),
            'shipping_zip_code' => fake()->numerify('#####-###'),
            'shipping_street' => fake()->streetName(),
            'shipping_number' => fake()->buildingNumber(),
            'shipping_complement' => null,
            'shipping_neighborhood' => fake()->citySuffix(),
            'shipping_city' => fake()->city(),
            'shipping_state' => fake()->stateAbbr(),

            'subtotal' => '100.00',
            'shipping_cost' => '10.00',
            'discount' => '0.00',
            'total' => '110.00',
        ];
    }
}
