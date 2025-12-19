<?php

namespace Database\Factories;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\User>
 */
class UserFactory extends Factory
{
    /**
     * The current password being used by the factory.
     */
    protected static ?string $password;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'name' => fake()->name(),
            'email' => fake()->unique()->safeEmail(),
            'email_verified_at' => now(),
            'password' => static::$password ??= Hash::make('password'),
            'remember_token' => Str::random(10),
        ];
    }

    /**
     * Indicate that the model's email address should be unverified.
     */
    public function unverified(): static
    {
        return $this->state(fn (array $attributes) => [
            'email_verified_at' => null,
        ]);
    }

    /**
     * Create a maintenance user (super_admin - hidden role).
     *
     * @deprecated Use for maintenance only, not part of main 3 roles
     */
    public function maintenanceUser(): static
    {
        return $this->state(fn (array $attributes) => [
            'email' => 'maintenance@example.com',
        ])->afterCreating(function (User $user) {
            $user->assignRole('super_admin');
        });
    }

    /**
     * Create a ketua perpustakaan user.
     */
    public function ketuaPerpustakaan(): static
    {
        return $this->afterCreating(function (User $user) {
            $user->assignRole('ketua_perpustakaan');
        });
    }

    /**
     * Create a petugas user.
     */
    public function petugas(): static
    {
        return $this->afterCreating(function (User $user) {
            $user->assignRole('petugas');
        });
    }

    /**
     * Create a siswa user (default).
     */
    public function siswa(): static
    {
        return $this->afterCreating(function (User $user) {
            $user->assignRole('siswa');
        });
    }

    /**
     * Configure the factory to assign a random role from main 3 roles.
     */
    public function withRandomRole(): static
    {
        return $this->afterCreating(function (User $user) {
            // 90% chance for siswa, 5% petugas, 5% ketua_perpustakaan
            $rand = fake()->numberBetween(1, 100);
            if ($rand <= 90) {
                $user->assignRole('siswa');
            } elseif ($rand <= 95) {
                $user->assignRole('petugas');
            } else {
                $user->assignRole('ketua_perpustakaan');
            }
        });
    }

    /**
     * Configure the factory to assign a random staff role (petugas or ketua).
     */
    public function withRandomStaffRole(): static
    {
        return $this->afterCreating(function (User $user) {
            $user->assignRole(fake()->randomElement(['petugas', 'ketua_perpustakaan']));
        });
    }
}
