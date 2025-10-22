<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;

/**
 * Admin Super Admin Seeder
 *
 * This seeder ensures that admin@testing.com always has super_admin role
 * with full access to all system features and pages.
 */
class AdminSuperAdminSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $this->command->info('🔐 Setting up admin@testing.com as Super Admin...');

        // Find or create the admin user
        $admin = User::firstOrCreate(
            ['email' => 'admin@testing.com'],
            [
                'name' => 'Test User',
                'password' => bcrypt('password'),
                'email_verified_at' => now(),
            ]
        );

        // Find the super_admin role
        $superAdminRole = Role::where('name', 'super_admin')->first();

        if (! $superAdminRole) {
            $this->command->error('❌ Super Admin role not found! Please run LibraryRolePermissionSeeder first.');

            return;
        }

        // Assign super_admin role to admin user
        $admin->syncRoles(['super_admin']);

        $this->command->info('✅ admin@testing.com has been configured as Super Admin');
        $this->command->info('   👤 User: '.$admin->name.' ('.$admin->email.')');
        $this->command->info('   🎭 Role: '.$superAdminRole->name);
        $this->command->info('   🔑 Total Permissions: '.$admin->getAllPermissions()->count());
        $this->command->info('   🔑 Password: password');

        // Verify critical permissions
        $criticalPermissions = [
            'admin_access',  // Single permission for full access
        ];

        $allPermissionsGranted = true;
        foreach ($criticalPermissions as $permission) {
            if (! $admin->can($permission)) {
                $allPermissionsGranted = false;
                break;
            }
        }

        if ($allPermissionsGranted) {
            $this->command->info('🎉 Admin access permission verified and working!');
        } else {
            $this->command->warning('⚠️  Admin access permission may be missing');
        }

        $this->command->info('');
        $this->command->info('🚀 admin@testing.com is now ready with full system access!');
        $this->command->info('   URL: /admin');
        $this->command->info('   Email: admin@testing.com');
        $this->command->info('   Password: password');
    }
}
