<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    { // 1. Pastikan role super_admin ada
        $superAdminRole = Role::firstOrCreate([
            'name' => 'super_admin',
            'guard_name' => 'web',
        ]);

        // 2. Buat / ambil user admin
        $user = User::firstOrCreate(
            ['email' => 'admin@testing.com'],
            [
                'name' => 'Super Admin',
                'password' => Hash::make('password'), // ganti di production
            ]
        );

        // 3. Assign role jika belum
        $permissions = Permission::pluck('name')->toArray();
        $user->syncPermissions($permissions);
    }
}
