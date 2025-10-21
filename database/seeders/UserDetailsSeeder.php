<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\UserDetails;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

/**
 * UserDetailsSeeder
 *
 * This seeder creates sample user details for different types of library users:
 * - Students: Regular library users with student IDs and class information
 * - Library Head: Administrator with highest privileges
 * - Staff: Library employees with various positions
 */
class UserDetailsSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Run the database seeds for user details.
     */
    public function run(): void
    {
        // Create user details for the admin user
        $adminUser = User::where('email', 'admin@testing.com')->first();
        if ($adminUser) {
            UserDetails::create([
                'user_id' => $adminUser->id,
                'user_type' => 'library_head',
                'phone_number' => '+6281234567890',
                'birth_date' => '1985-05-15',
                'gender' => 'male',
                'address' => 'Jl. Pendidikan No. 1, Jakarta Pusat, Indonesia',
                'employee_id' => 'EMP000001',
                'position' => 'Head Librarian',
                'hire_date' => '2020-01-15',
                'membership_number' => 'LIB000001',
                'membership_date' => '2020-01-15',
                'membership_status' => 'active',
                'membership_expiry' => '2030-01-15',
                'notes' => 'Head Librarian with full system access',
                'preferences' => [
                    'email_notifications' => true,
                    'sms_notifications' => true,
                    'language' => 'en',
                ],
            ]);
        }

        // Create sample student users with details
        $students = User::factory(15)->create();
        foreach ($students as $index => $student) {
            UserDetails::factory()->student()->create([
                'user_id' => $student->id,
                'membership_number' => 'LIB'.str_pad($index + 1001, 6, '0', STR_PAD_LEFT),
            ]);
        }

        // Create sample library staff
        $staffUsers = User::factory(3)->create();
        $positions = ['Library Assistant', 'Circulation Staff', 'Cataloging Staff'];

        foreach ($staffUsers as $index => $staff) {
            UserDetails::factory()->staff()->create([
                'user_id' => $staff->id,
                'position' => $positions[$index],
                'employee_id' => 'EMP'.str_pad($index + 2, 6, '0', STR_PAD_LEFT),
                'membership_number' => 'LIB'.str_pad($index + 2001, 6, '0', STR_PAD_LEFT),
            ]);
        }

        // Create some users with expired memberships
        $expiredUsers = User::factory(3)->create();
        foreach ($expiredUsers as $index => $user) {
            UserDetails::factory()->student()->expiredMembership()->create([
                'user_id' => $user->id,
                'membership_number' => 'LIB'.str_pad($index + 3001, 6, '0', STR_PAD_LEFT),
            ]);
        }

        // Create some users with suspended memberships
        $suspendedUsers = User::factory(2)->create();
        foreach ($suspendedUsers as $index => $user) {
            UserDetails::factory()->student()->suspendedMembership()->create([
                'user_id' => $user->id,
                'membership_number' => 'LIB'.str_pad($index + 4001, 6, '0', STR_PAD_LEFT),
            ]);
        }

        $this->command->info('✅ UserDetails seeder completed successfully!');
        $this->command->info('📊 Created user details for:');
        $this->command->info('   - 1 Library Head (admin user)');
        $this->command->info('   - 15 Students');
        $this->command->info('   - 3 Library Staff');
        $this->command->info('   - 3 Users with expired memberships');
        $this->command->info('   - 2 Users with suspended memberships');
    }
}
