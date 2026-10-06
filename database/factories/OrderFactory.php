<?php

namespace Database\Factories;

use App\Enums\OrderStatus;
use App\Models\Customer;
use App\Models\Order;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Order>
 */
class OrderFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'customer_id' => Customer::factory(),
            'order_date' => fake()->dateTimeBetween('-1 month', 'now'),
            'status' => OrderStatus::Pending,
            'subtotal' => 100,
            'discount' => 0,
            'tax' => 5,
            'grand_total' => 105,
        ];
    }
}
