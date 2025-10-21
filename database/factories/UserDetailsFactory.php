<?php

namespace Database\Factories;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\UserDetails>
 */
class UserDetailsFactory extends Factory
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
            'user_type' => $this->faker->randomElement(['student', 'library_head', 'staff']),
            'phone_number' => $this->faker->phoneNumber(),
            'birth_date' => $this->faker->date('Y-m-d', '-16 years'), // At least 16 years old
            'gender' => $this->faker->randomElement(['male', 'female', 'other']),
            'address' => $this->faker->address(),
            'membership_number' => 'LIB'.$this->faker->unique()->numerify('######'),
            'membership_date' => $this->faker->date('Y-m-d', '-2 years'),
            'membership_status' => 'active',
            'membership_expiry' => $this->faker->date('Y-m-d', '+1 year'),
            'notes' => $this->faker->optional(0.3)->sentence(),
            'preferences' => [
                'email_notifications' => $this->faker->boolean(80),
                'sms_notifications' => $this->faker->boolean(30),
                'language' => $this->faker->randomElement(['en', 'id']),
            ],
        ];
    }

    /**
     * Create student user details
     */
    public function student(): static
    {
        return $this->state(fn (array $attributes) => [
            'user_type' => 'student',
            'student_id' => 'STU'.$this->faker->unique()->numerify('######'),
            'class' => $this->faker->randomElement(['10A', '10B', '11A', '11B', '12A', '12B']),
            'major' => $this->faker->randomElement(['Science', 'Social', 'Language', 'Computer']),
        ]);
    }

    /**
     * Create library head user details
     */
    public function libraryHead(): static
    {
        return $this->state(fn (array $attributes) => [
            'user_type' => 'library_head',
            'employee_id' => 'EMP'.$this->faker->unique()->numerify('######'),
            'position' => 'Head Librarian',
            'hire_date' => $this->faker->date('Y-m-d', '-5 years'),
        ]);
    }

    /**
     * Create staff user details
     */
    public function staff(): static
    {
        return $this->state(fn (array $attributes) => [
            'user_type' => 'staff',
            'employee_id' => 'EMP'.$this->faker->unique()->numerify('######'),
            'position' => $this->faker->randomElement(['Library Assistant', 'Circulation Staff', 'Cataloging Staff']),
            'hire_date' => $this->faker->date('Y-m-d', '-3 years'),
        ]);
    }

    /**
     * Create user details with expired membership
     */
    public function expiredMembership(): static
    {
        return $this->state(fn (array $attributes) => [
            'membership_status' => 'expired',
            'membership_expiry' => $this->faker->date('Y-m-d', '-1 month'),
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
