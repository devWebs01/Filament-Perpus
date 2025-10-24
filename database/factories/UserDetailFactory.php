<?php

namespace Database\Factories;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\UserDetail>
 */
class UserDetailFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'user_id' => User::factory(),
            'nik' => $this->faker->optional(0.7, null)->numerify('##################'), // 16-digit national ID (optional)
            'nis' => $this->faker->optional(0.8, null)->numerify('STU########'), // Student ID
            'nisn' => $this->faker->optional(0.8, null)->numerify('##########'), // National Student ID (10 digits)
            'class' => $this->faker->optional(0.7, null)->randomElement(['12A', '12B', '11A', '11B', '10A', '10B']),
            'major' => $this->faker->optional(0.7, null)->randomElement(['Science', 'Social', 'Language', 'Computer', 'Arts']),
            'semester' => $this->faker->optional(0.7, null)->numberBetween(1, 6),
            'address' => $this->faker->address(),
            'phone_number' => $this->faker->phoneNumber(),
            'birth_date' => $this->faker->date('Y-m-d', '-16 years'), // At least 16 years old
            'birth_place' => $this->faker->city(),
            'gender' => $this->faker->randomElement(['male', 'female']), // Use English values
            'religion' => $this->faker->randomElement(['islam', 'christian', 'catholic', 'hindu', 'buddhist', 'other']),
            'join_date' => $this->faker->optional(0.6, null)->date('Y-m-d', '-5 years'), // For staff
            'membership_status' => 'active',
            'profile_photo' => $this->faker->optional(0.8, null)->randomElement([
                'default-user-1.jpg',
                'default-user-2.jpg',
                'default-user-3.jpg',
                'default-user-4.jpg',
                'default-user-5.jpg',
            ]),
        ];
    }

    /**
     * Create student user details
     */
    public function student(): static
    {
        return $this->state(fn (array $attributes) => [
            'nis' => 'STU'.$this->faker->unique()->numerify('########'),
            'nisn' => $this->faker->unique()->numerify('##########'),
            'class' => $this->faker->randomElement(['10A', '10B', '11A', '11B', '12A', '12B']),
            'major' => $this->faker->randomElement(['Science', 'Social', 'Language', 'Computer']),
            'semester' => $this->faker->numberBetween(1, 6),
        ]);
    }

    /**
     * Create library head user details
     */
    public function libraryHead(): static
    {
        return $this->state(fn (array $attributes) => [
            'join_date' => $this->faker->date('Y-m-d', '-5 years'),
            'membership_status' => 'active',
        ]);
    }

    /**
     * Create staff user details
     */
    public function staff(): static
    {
        return $this->state(fn (array $attributes) => [
            'join_date' => $this->faker->date('Y-m-d', '-3 years'),
            'membership_status' => 'active',
        ]);
    }

    /**
     * Create user details with expired membership
     */
    public function expiredMembership(): static
    {
        return $this->state(fn (array $attributes) => [
            'membership_status' => 'expired',
        ]);
    }

    /**
     * Create user details with suspended membership
     */
    public function suspendedMembership(): static
    {
        return $this->state(fn (array $attributes) => [
            'membership_status' => 'suspended',
        ]);
    }
}
