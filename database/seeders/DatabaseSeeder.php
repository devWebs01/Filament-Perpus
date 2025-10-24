<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // User::factory(10)->create();

        User::firstOrCreate(
            ['email' => 'admin@testing.com'],
            [
                'name' => 'Test User',
                'password' => Hash::make('password123'),
                'email_verified_at' => now(),
            ]
        );

        $this->call([
            // 1. System Configuration
            LibraryRolePermissionSeeder::class,  // Roles and permissions
            SettingSeeder::class,              // Library settings

            // 2. Reference Data
            CategorySeeder::class,             // Book categories
            StatusSeeder::class,               // Transaction statuses

            // 3. User Data
            UserDetailsSeeder::class,          // Admin user details
            LibraryUsersSeeder::class,         // Additional users (students, staff)
            AssignUserRolesSeeder::class,      // Assign roles to users

            // 4. Library Data
            BookSeeder::class,                 // Books
            TransactionSeeder::class,          // Sample transactions
        ]);
    }
}
