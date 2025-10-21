<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\UserDetails;
use Illuminate\Database\Seeder;

/**
 * Library UserDetails Seeder
 *
 * This seeder creates sample user details for the library information system.
 * It works with the existing user_details table structure and creates realistic
 * data for students, library head, and staff members.
 */
class LibraryUserDetailsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Create library head user details
        $libraryHead = User::where('email', 'admin@testing.com')->first();
        if ($libraryHead && ! $libraryHead->userDetails) {
            UserDetails::create([
                'user_id' => $libraryHead->id,
                'nik' => '1234567890123456',
                'address' => '123 Library Street, Book City, BC 12345',
                'phone_number' => '+1-555-010-0001',
                'birth_date' => '1980-03-15',
                'birth_place' => 'Jakarta',
                'gender' => 'L', // Male (Laki-laki)
                'religion' => 'Islam',
                'join_date' => '2020-01-01',
                'membership_status' => 'active',
            ]);
            $this->command->info('✅ Library Head user details created');
        }

        // Create sample students with NIS and NISN
        $studentEmails = [
            'j.anderson@student.com',
            'm.garcia@student.com',
            'd.kim@student.com',
            's.turner@student.com',
            'a.hassan@student.com',
        ];

        $studentData = [
            ['name' => 'James Anderson', 'nis' => '2024001', 'nisn' => '0061234567', 'class' => '12A', 'major' => 'Science'],
            ['name' => 'Maria Garcia', 'nis' => '2024002', 'nisn' => '0061234568', 'class' => '11B', 'major' => 'Social'],
            ['name' => 'David Kim', 'nis' => '2024003', 'nisn' => '0061234569', 'class' => '10A', 'major' => 'Computer'],
            ['name' => 'Sophie Turner', 'nis' => '2024004', 'nisn' => '0061234570', 'class' => '12B', 'major' => 'Language'],
            ['name' => 'Ahmed Hassan', 'nis' => '2024005', 'nisn' => '0061234571', 'class' => '11A', 'major' => 'Science'],
        ];

        foreach ($studentData as $index => $data) {
            $user = User::firstOrCreate(
                ['email' => $studentEmails[$index]],
                [
                    'name' => $data['name'],
                    'password' => bcrypt('password'),
                ]
            );

            if (! $user->userDetails) {
                UserDetails::create([
                    'user_id' => $user->id,
                    'nik' => '320101'.str_pad($index + 1, 12, '0', STR_PAD_LEFT),
                    'nis' => $data['nis'],
                    'nisn' => $data['nisn'],
                    'class' => $data['class'],
                    'major' => $data['major'],
                    'semester' => rand(1, 6),
                    'address' => fake()->address(),
                    'phone_number' => fake()->phoneNumber(),
                    'birth_date' => fake()->date('Y-m-d', '-16 years'),
                    'birth_place' => fake()->city(),
                    'gender' => fake()->randomElement(['L', 'P']), // L=Laki-laki (Male), P=Perempuan (Female)
                    'religion' => fake()->randomElement(['Islam', 'Christian', 'Catholic', 'Hindu', 'Buddhist']),
                    'join_date' => fake()->date('Y-m-d', '-2 years'),
                    'membership_status' => 'active',
                ]);
            }
        }

        // Create library staff
        $staffData = [
            ['name' => 'Michael Chen', 'email' => 'm.chen@library.com', 'position' => 'Library Assistant'],
            ['name' => 'Emily Rodriguez', 'email' => 'e.rodriguez@library.com', 'position' => 'Circulation Staff'],
        ];

        foreach ($staffData as $index => $data) {
            $user = User::firstOrCreate(
                ['email' => $data['email']],
                [
                    'name' => $data['name'],
                    'password' => bcrypt('password'),
                ]
            );

            if (! $user->userDetails) {
                UserDetails::create([
                    'user_id' => $user->id,
                    'nik' => '320102'.str_pad($index + 100, 12, '0', STR_PAD_LEFT),
                    'address' => fake()->address(),
                    'phone_number' => fake()->phoneNumber(),
                    'birth_date' => fake()->date('Y-m-d', '-25 years'),
                    'birth_place' => fake()->city(),
                    'gender' => fake()->randomElement(['L', 'P']), // L=Laki-laki (Male), P=Perempuan (Female)
                    'religion' => fake()->randomElement(['Islam', 'Christian', 'Catholic', 'Hindu', 'Buddhist']),
                    'join_date' => fake()->date('Y-m-d', '-3 years'),
                    'membership_status' => 'active',
                ]);
            }
        }

        $this->command->info('✅ Library user details seeded successfully!');
        $this->command->info('📊 Summary:');
        $this->command->info('   - Library Head: admin@testing.com');
        $this->command->info('   - Students: '.count($studentData).' accounts');
        $this->command->info('   - Staff: '.count($staffData).' accounts');
        $this->command->info('   All accounts use password: "password"');
    }
}
