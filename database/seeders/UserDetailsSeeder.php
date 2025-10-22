<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\UserDetail;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

/**
 * UserDetailSeeder
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
            UserDetail::create([
                'user_id' => $adminUser->id,
                'phone_number' => '+6281234567890',
                'birth_date' => '1985-05-15',
                'birth_place' => 'Jakarta',
                'gender' => 'L',
                'address' => 'Jl. Pendidikan No. 1, Jakarta Pusat, Indonesia',
                'religion' => 'Islam',
                'join_date' => '2020-01-15',
                'membership_status' => 'active',
            ]);
        }

        // Create sample student users with details
        $students = User::factory(15)->create();
        foreach ($students as $student) {
            UserDetail::factory()->student()->create([
                'user_id' => $student->id,
            ]);
        }

        // Create sample library staff
        $staffUsers = User::factory(3)->create();

        foreach ($staffUsers as $staff) {
            UserDetail::factory()->staff()->create([
                'user_id' => $staff->id,
            ]);
        }

        // Create some users with expired memberships
        $expiredUsers = User::factory(3)->create();
        foreach ($expiredUsers as $user) {
            UserDetail::factory()->student()->expiredMembership()->create([
                'user_id' => $user->id,
            ]);
        }

        // Create some users with suspended memberships
        $suspendedUsers = User::factory(2)->create();
        foreach ($suspendedUsers as $user) {
            UserDetail::factory()->student()->suspendedMembership()->create([
                'user_id' => $user->id,
            ]);
        }

        $this->command->info('✅ UserDetail seeder completed successfully!');
        $this->command->info('📊 Created user details for:');
        $this->command->info('   - 1 Library Head (admin user)');
        $this->command->info('   - 15 Students');
        $this->command->info('   - 3 Library Staff');
        $this->command->info('   - 3 Users with expired memberships');
        $this->command->info('   - 2 Users with suspended memberships');
    }
}
