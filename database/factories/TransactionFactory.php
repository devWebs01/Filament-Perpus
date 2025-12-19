<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Transaction>
 */
class TransactionFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'code' => 'TRX-'.fake()->unique()->numberBetween(1000, 9999),
            'borrow_date' => fake()->date(),
            'return_date' => fake()->optional()->date(),
            'book_id' => \App\Models\Book::factory(),
            'user_id' => \App\Models\User::factory(),
            'status_id' => \App\Models\Status::factory(),
            'penalty_total' => fake()->numberBetween(0, 10000),
        ];
    }
}
