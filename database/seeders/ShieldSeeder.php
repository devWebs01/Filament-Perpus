<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Schema;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use Spatie\Permission\PermissionRegistrar;

/**
 * Shield Seeder
 *
 * This seeder creates the default roles and permissions for Filament Shield
 * in the library information system. It sets up:
 * - Super Admin role with all permissions
 * - Library Head role with management permissions
 * - Staff role with operational permissions
 * - Student role with basic access permissions
 */
class ShieldSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Clear existing permissions and roles
        app()[PermissionRegistrar::class]->forgetCachedPermissions();

        // Reset tables to avoid conflicts
        Schema::disableForeignKeyConstraints();

        // Delete existing permissions and roles
        Permission::query()->delete();
        Role::query()->delete();

        Schema::enableForeignKeyConstraints();

        $this->command->info('🛡️  Creating Filament Shield permissions and roles...');

        // Create default permissions for Filament resources
        $defaultPermissions = $this->getDefaultPermissions();
        foreach ($defaultPermissions as $permission) {
            Permission::create(['name' => $permission]);
        }

        $this->command->info('✅ Created '.count($defaultPermissions).' default permissions');

        // Create roles
        $roles = $this->createRoles();

        // Assign permissions to roles
        $this->assignPermissionsToRoles($roles);

        $this->command->info('✅ Created roles and assigned permissions');

        // Assign super admin role to admin user
        $adminUser = User::where('email', 'admin@testing.com')->first();
        if ($adminUser) {
            $adminUser->assignRole('super_admin');
            $this->command->info('✅ Assigned super_admin role to admin user');
        }

        $this->command->info('🎉 Filament Shield setup completed successfully!');
        $this->command->info('');
        $this->command->info('📋 Created Roles:');
        $this->command->info('   • super_admin - Full system access');
        $this->command->info('   • library_head - Library management permissions');
        $this->command->info('   • staff - Basic operational permissions');
        $this->command->info('   • student - Read-only access');
    }

    /**
     * Get default permissions for Filament Shield
     *
     * @return array<string>
     */
    private function getDefaultPermissions(): array
    {
        return [
            // Panel permissions
            'panel_access',

            // User management
            'user_access',
            'user_create',
            'user_read',
            'user_update',
            'user_delete',

            // Role management
            'role_access',
            'role_create',
            'role_read',
            'role_update',
            'role_delete',

            // Permission management
            'permission_access',
            'permission_create',
            'permission_read',
            'permission_update',
            'permission_delete',

            // Category management (Library)
            'category_access',
            'category_create',
            'category_read',
            'category_update',
            'category_delete',

            // Book management
            'book_access',
            'book_create',
            'book_read',
            'book_update',
            'book_delete',

            // Transaction management
            'transaction_access',
            'transaction_create',
            'transaction_read',
            'transaction_update',
            'transaction_delete',

            // Status management
            'status_access',
            'status_create',
            'status_read',
            'status_update',
            'status_delete',

            // Penalty management
            'penalty_access',
            'penalty_create',
            'penalty_read',
            'penalty_update',
            'penalty_delete',

            // Settings management
            'setting_access',
            'setting_create',
            'setting_read',
            'setting_update',
            'setting_delete',

            // User Details management
            'user_details_access',
            'user_details_create',
            'user_details_read',
            'user_details_update',
            'user_details_delete',

            // Dashboard widgets
            'dashboard_access',
        ];
    }

    /**
     * Create roles for the library system
     *
     * @return array<string, Role>
     */
    private function createRoles(): array
    {
        return [
            'super_admin' => Role::create(['name' => 'super_admin']),
            'library_head' => Role::create(['name' => 'library_head']),
            'staff' => Role::create(['name' => 'staff']),
            'student' => Role::create(['name' => 'student']),
        ];
    }

    /**
     * Assign permissions to roles based on library system requirements
     *
     * @param  array<string, Role>  $roles
     */
    private function assignPermissionsToRoles(array $roles): void
    {
        $allPermissions = Permission::all();

        // Super Admin - All permissions
        $roles['super_admin']->givePermissionTo($allPermissions);

        // Library Head - Most management permissions except user/role management
        $libraryHeadPermissions = $allPermissions->filter(function ($permission) {
            return ! in_array($permission->name, [
                'role_create',
                'role_update',
                'role_delete',
                'permission_create',
                'permission_update',
                'permission_delete',
            ]);
        });
        $roles['library_head']->givePermissionTo($libraryHeadPermissions);

        // Staff - Operational permissions for books, transactions, and basic management
        $staffPermissions = Permission::whereIn('name', [
            'panel_access',
            'book_access',
            'book_create',
            'book_read',
            'book_update',
            'transaction_access',
            'transaction_create',
            'transaction_read',
            'transaction_update',
            'category_read',
            'status_read',
            'penalty_access',
            'penalty_create',
            'penalty_read',
            'penalty_update',
            'user_details_read',
            'dashboard_access',
        ])->get();
        $roles['staff']->givePermissionTo($staffPermissions);

        // Student - Read-only access
        $studentPermissions = Permission::whereIn('name', [
            'panel_access',
            'book_read',
            'category_read',
            'status_read',
            'transaction_read',
            'user_details_read',
            'dashboard_access',
        ])->get();
        $roles['student']->givePermissionTo($studentPermissions);
    }
}
