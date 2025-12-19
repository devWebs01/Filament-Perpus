<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class RoleAndPermissionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Reset cached roles and permissions
        app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();

        // Create permissions
        $permissions = [
            // User Management
            'view_user',
            'view_any_user',
            'create_user',
            'update_user',
            'delete_user',
            'delete_any_user',
            'restore_user',
            'restore_any_user',
            'force_delete_user',
            'force_delete_any_user',
            'replicate_user',
            'reorder_user',

            // Book Management
            'view_book',
            'view_any_book',
            'create_book',
            'update_book',
            'delete_book',
            'delete_any_book',
            'restore_book',
            'restore_any_book',
            'force_delete_book',
            'force_delete_any_book',
            'replicate_book',
            'reorder_book',

            // Category Management
            'view_category',
            'view_any_category',
            'create_category',
            'update_category',
            'delete_category',
            'delete_any_category',
            'restore_category',
            'restore_any_category',
            'force_delete_category',
            'force_delete_any_category',
            'replicate_category',
            'reorder_category',

            // Transaction Management
            'view_transaction',
            'view_any_transaction',
            'create_transaction',
            'update_transaction',
            'delete_transaction',
            'delete_any_transaction',
            'restore_transaction',
            'restore_any_transaction',
            'force_delete_transaction',
            'force_delete_any_transaction',
            'replicate_transaction',
            'reorder_transaction',

            // Report Management
            'view_report',
            'view_any_report',
            'create_report',
            'update_report',
            'delete_report',
            'delete_any_report',

            // Settings Management
            'view_setting',
            'view_any_setting',
            'create_setting',
            'update_setting',
            'delete_setting',
            'delete_any_setting',

            // Shelf Management
            'view_shelf',
            'view_any_shelf',
            'create_shelf',
            'update_shelf',
            'delete_shelf',
            'delete_any_shelf',

            // Bookmark Management
            'view_bookmark',
            'view_any_bookmark',
            'create_bookmark',
            'update_bookmark',
            'delete_bookmark',
            'delete_any_bookmark',

            // Status Management
            'view_status',
            'view_any_status',
            'create_status',
            'update_status',
            'delete_status',
            'delete_any_status',

            // Dashboard
            'view_dashboard',

            // Shield Resource
            'view_role',
            'view_any_role',
            'create_role',
            'update_role',
            'delete_role',
            'delete_any_role',
        ];

        foreach ($permissions as $permission) {
            Permission::firstOrCreate(['name' => $permission]);
        }

        // Create roles and assign permissions

        // Super Admin - has all permissions
        $superAdminRole = Role::firstOrCreate(['name' => 'super_admin']);
        $superAdminRole->givePermissionTo(Permission::all());

        // Ketua Perpustakaan - can manage most things except user roles
        $ketuaRole = Role::firstOrCreate(['name' => 'ketua_perpustakaan']);
        $ketuaRole->givePermissionTo([
            'view_user', 'view_any_user', 'create_user', 'update_user',
            'view_book', 'view_any_book', 'create_book', 'update_book', 'delete_book', 'delete_any_book',
            'view_category', 'view_any_category', 'create_category', 'update_category', 'delete_category', 'delete_any_category',
            'view_transaction', 'view_any_transaction', 'create_transaction', 'update_transaction', 'delete_transaction', 'delete_any_transaction',
            'view_report', 'view_any_report', 'create_report', 'update_report',
            'view_setting', 'view_any_setting', 'update_setting',
            'view_shelf', 'view_any_shelf', 'create_shelf', 'update_shelf', 'delete_shelf', 'delete_any_shelf',
            'view_status', 'view_any_status', 'create_status', 'update_status', 'delete_status', 'delete_any_status',
            'view_dashboard',
        ]);

        // Petugas - limited permissions
        $petugasRole = Role::firstOrCreate(['name' => 'petugas']);
        $petugasRole->givePermissionTo([
            'view_book', 'view_any_book',
            'view_category', 'view_any_category',
            'view_transaction', 'view_any_transaction', 'create_transaction', 'update_transaction',
            'view_shelf', 'view_any_shelf',
            'view_status', 'view_any_status',
            'view_dashboard',
        ]);

        // Siswa - only can view and manage bookmarks
        $siswaRole = Role::firstOrCreate(['name' => 'siswa']);
        $siswaRole->givePermissionTo([
            'view_book', 'view_any_book',
            'view_category', 'view_any_category',
            'view_transaction', 'view_any_transaction',
            'view_bookmark', 'view_any_bookmark', 'create_bookmark', 'update_bookmark', 'delete_bookmark',
            'view_dashboard',
        ]);

        // Assign super_admin role to first user (if exists)
        $firstUser = User::first();
        if ($firstUser) {
            $firstUser->assignRole('super_admin');
        }
    }
}
