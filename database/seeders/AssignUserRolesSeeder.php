<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\UserDetail;
use Illuminate\Database\Seeder;

/**
 * Assign User Roles Seeder
 *
 * This seeder assigns appropriate roles to existing users based on their
 * user details information in the library system.
 */
class AssignUserRolesSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $this->command->info('🔐 Assigning roles to users based on their details...');

        $assignedCount = 0;

        // Get all users with their details
        $usersWithDetails = User::with('UserDetail')->get();

        foreach ($usersWithDetails as $user) {
            if (! $user->UserDetail) {
                continue;
            }

            $role = $this->determineUserRole($user->UserDetail);

            if ($role && ! $user->hasRole($role)) {
                $user->assignRole($role);
                $assignedCount++;

                $this->command->info("   ✅ Assigned '{$role}' role to: {$user->name} ({$user->email})");
            }
        }

        $this->command->info("🎉 Successfully assigned roles to {$assignedCount} users!");
    }

    /**
     * Determine the appropriate role for a user based on their details
     */
    private function determineUserRole(UserDetail $UserDetail): ?string
    {
        // Library Head - Admin email or special conditions
        if ($UserDetail->isLibraryHead()) {
            return 'library_head';
        }

        // Staff - Non-students with join dates
        if ($UserDetail->isStaff()) {
            return 'staff';
        }

        // Students - Users with student IDs
        if ($UserDetail->isStudent()) {
            return 'student';
        }

        // Default to student role if no specific criteria met
        return 'student';
    }
}
