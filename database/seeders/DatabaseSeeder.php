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
            // 1. System Configuration
            LibrarySystemSeeder::class,        // Combined: Users, Roles, Permissions, UserDetails
            LibraryRolePermissionSeeder::class, // Additional roles/permissions if needed
            SettingSeeder::class,              // Library settings

            // 2. Reference Data
            StatusSeeder::class,               // Transaction statuses

            // 3. Library Data
            BookSeeder::class,                 // Books (smart online/offline mode)
            TransactionSeeder::class,          // Sample transactions
        ]);

        $this->command->info('✅ Database Seeder selesai dijalankan!');
    }
}
