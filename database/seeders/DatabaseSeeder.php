<?php

namespace Database\Seeders;

use App\Models\User;
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
        // User::factory(10)->create();

        User::factory()->create([
            'name' => 'Test User',
            'email' => 'admin@testing.com',
        ]);

        $this->call([
            CategorySeeder::class,
            BookSeeder::class,
            SettingSeeder::class,
            StatusSeeder::class,
            TransactionSeeder::class,
        ]);
    }
}
