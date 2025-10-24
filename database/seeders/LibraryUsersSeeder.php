<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\UserDetail;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class LibraryUsersSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Library Head
        $libraryHead = User::firstOrCreate(
            ['email' => ' kepala.perpus@perpus.sch.id'],
            [
                'name' => 'Ahmad Wijaya',
                'password' => Hash::make('password123'),
                'email_verified_at' => now(),
            ]
        );

        UserDetail::firstOrCreate(
            ['user_id' => $libraryHead->id],
            [
                'nik' => '3201011234560001',
                'address' => 'Jl. Pendidikan No. 1, Jakarta',
                'phone_number' => '081234567890',
                'birth_date' => '1975-05-15',
                'birth_place' => 'Jakarta',
                'gender' => 'male',
                'religion' => 'islam',
                'join_date' => '2020-01-01',
                'membership_status' => 'active',
                'profile_photo' => null,
            ]
        );

        // Library Staff
        $staff = User::firstOrCreate(
            ['email' => 'staff.perpus@perpus.sch.id'],
            [
                'name' => 'Siti Nurhaliza',
                'password' => Hash::make('password123'),
                'email_verified_at' => now(),
            ]
        );

        UserDetail::firstOrCreate(
            ['user_id' => $staff->id],
            [
                'nik' => '3201011234560002',
                'address' => 'Jl. Perpustakaan No. 5, Jakarta',
                'phone_number' => '082345678901',
                'birth_date' => '1985-08-20',
                'birth_place' => 'Bandung',
                'gender' => 'female',
                'religion' => 'islam',
                'join_date' => '2021-03-15',
                'membership_status' => 'active',
                'profile_photo' => null,
            ]
        );

        // Create Students
        $students = [
            [
                'name' => 'Budi Santoso',
                'email' => 'budi.santoso@siswa.sch.id',
                'nis' => '2021001',
                'nisn' => '0051234567',
                'class' => '12 IPA 1',
                'major' => 'IPA',
                'semester' => 6,
                'gender' => 'male',
                'birth_date' => '2004-01-15',
                'birth_place' => 'Jakarta',
            ],
            [
                'name' => 'Siti Aminah',
                'email' => 'siti.aminah@siswa.sch.id',
                'nis' => '2021002',
                'nisn' => '0051234568',
                'class' => '12 IPS 2',
                'major' => 'IPS',
                'semester' => 6,
                'gender' => 'female',
                'birth_date' => '2004-03-22',
                'birth_place' => 'Surabaya',
            ],
            [
                'name' => 'Ahmad Fadli',
                'email' => 'ahmad.fadli@siswa.sch.id',
                'nis' => '2022001',
                'nisn' => '0061234569',
                'class' => '11 IPA 3',
                'major' => 'IPA',
                'semester' => 4,
                'gender' => 'male',
                'birth_date' => '2005-07-10',
                'birth_place' => 'Bandung',
            ],
            [
                'name' => 'Dewi Lestari',
                'email' => 'dewi.lestari@siswa.sch.id',
                'nis' => '2022002',
                'nisn' => '0061234570',
                'class' => '11 Bahasa 1',
                'major' => 'Bahasa',
                'semester' => 4,
                'gender' => 'female',
                'birth_date' => '2005-11-25',
                'birth_place' => 'Yogyakarta',
            ],
            [
                'name' => 'Rizki Pratama',
                'email' => 'rizki.pratama@siswa.sch.id',
                'nis' => '2023001',
                'nisn' => '0071234571',
                'class' => '10 IPA 2',
                'major' => 'IPA',
                'semester' => 2,
                'gender' => 'male',
                'birth_date' => '2006-09-18',
                'birth_place' => 'Semarang',
            ],
            [
                'name' => 'Nur Azizah',
                'email' => 'nur.azizah@siswa.sch.id',
                'nis' => '2023002',
                'nisn' => '0071234572',
                'class' => '10 IPS 1',
                'major' => 'IPS',
                'semester' => 2,
                'gender' => 'female',
                'birth_date' => '2006-04-05',
                'birth_place' => 'Medan',
            ],
        ];

        foreach ($students as $studentData) {
            $student = User::firstOrCreate(
                ['email' => $studentData['email']],
                [
                    'name' => $studentData['name'],
                    'password' => Hash::make('password123'),
                    'email_verified_at' => now(),
                ]
            );

            UserDetail::firstOrCreate(
                ['user_id' => $student->id],
                [
                    'nis' => $studentData['nis'],
                    'nisn' => $studentData['nisn'],
                    'class' => $studentData['class'],
                    'major' => $studentData['major'],
                    'semester' => $studentData['semester'],
                    'gender' => $studentData['gender'],
                    'birth_date' => $studentData['birth_date'],
                    'birth_place' => $studentData['birth_place'],
                    'address' => 'Alamat '.$studentData['name'],
                    'phone_number' => '08'.rand(100000000, 999999999),
                    'religion' => 'islam',
                    'join_date' => now(),
                    'membership_status' => 'active',
                ]
            );

            $this->command->info('Menambahkan siswa: '.$studentData['name']);
        }

        // Create additional staff
        $additionalStaff = [
            [
                'name' => 'Muhammad Rizki',
                'email' => 'muhammad.rizki@perpus.sch.id',
            ],
            [
                'name' => 'Fitri Handayani',
                'email' => 'fitri.handayani@perpus.sch.id',
            ],
        ];

        foreach ($additionalStaff as $staffData) {
            $user = User::firstOrCreate(
                ['email' => $staffData['email']],
                [
                    'name' => $staffData['name'],
                    'password' => Hash::make('password123'),
                    'email_verified_at' => now(),
                ]
            );

            UserDetail::create([
                'user_id' => $user->id,
                'address' => 'Alamat '.$staffData['name'],
                'phone_number' => '08'.rand(100000000, 999999999),
                'birth_date' => '198'.rand(0, 9).'-'.str_pad(rand(1, 12), 2, '0', STR_PAD_LEFT).'-'.str_pad(rand(1, 28), 2, '0', STR_PAD_LEFT),
                'birth_place' => 'Jakarta',
                'gender' => 'male',
                'religion' => 'islam',
                'join_date' => now()->subMonths(rand(1, 24)),
                'membership_status' => 'active',
            ]);

            $this->command->info('Menambahkan staf: '.$staffData['name']);
        }

        $this->command->info('Seeder pengguna perpustakaan selesai dijalankan.');
    }
}
