<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $this->command->info('🚀 Memulai Database Seeder...');

        $this->call([
            // 1. Roles & Permissions
            RoleAndPermissionSeeder::class,    // Shield roles and permissions

            // 2. System Configuration
            LibrarySystemSeeder::class,        // Combined: Users, Roles, UserDetails
            SettingSeeder::class,              // Library settings

            // 3. Reference Data
            StatusSeeder::class,               // Transaction statuses

            // 4. Book Data
            // BookSeeder::class,                 // Books (smart online/offline mode)
            BookSeeder::class,           // Books (smart online/offline mode)

            // 5. Transaction Data
            TransactionSeeder::class,          // Sample transactions
        ]);

        $this->command->info('✅ Database Seeder selesai dijalankan!');
    }
}
