<?php

namespace Database\Factories;

use App\Models\JournalEntry;
use App\Models\Order;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<JournalEntry>
 */
class JournalEntryFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'order_id' => Order::factory(),
            'entry_date' => fake()->dateTimeBetween('-1 month', 'now'),
            'description' => fake()->sentence(6),
            'reference' => fake()->unique()->bothify('JE-########'),
        ];
    }
}
