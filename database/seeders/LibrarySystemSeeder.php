<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\UserDetail;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

/**
 * Library System Seeder
 *
 * Seeder utama untuk sistem perpustakaan yang mencakup:
 * 1. Users creation dengan berbagai role
 * 2. UserDetails creation untuk semua users
 * 3. Role assignment otomatis menggunakan field role langsung
 *
 * Digunakan untuk menggantikan multiple seeders yang sebelumnya terpisah.
 */
class LibrarySystemSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $this->command->info('🚀 Memulai Library System Seeder...');

        // Create users dengan details lengkap
        $this->createUsersWithDetails();

        $this->command->info('✅ Library System Seeder berhasil dijalankan!');
        $this->displayLoginInfo();
    }

    /**
     * Create users dengan details lengkap
     */
    private function createUsersWithDetails(): void
    {
        $this->command->info('👤 Membuat users dengan details...');

        // Super Admin
        $this->createUserWithDetails([
            'email' => 'admin@testing.com',
            'name' => 'Super Admin Perpustakaan',
            'role' => 'super_admin',
            'user_details' => [
                'nik' => '1234567890123456',
                'phone_number' => '+6281234567890',
                'birth_date' => '1985-05-15',
                'birth_place' => 'Jakarta',
                'gender' => 'male',
                'address' => 'Jl. Pendidikan No. 1, Jakarta Pusat, Indonesia',
                'religion' => 'Islam',
                'join_date' => '2020-01-15',
                'membership_status' => 'active',
            ],
        ]);

        // Ketua Perpustakaan
        $this->createUserWithDetails([
            'email' => 'ketua@testing.com',
            'name' => 'Dr. Budi Santoso, M.Pd',
            'role' => 'ketua_perpustakaan',
            'user_details' => [
                'nik' => '3201011234560001',
                'address' => 'Jl. Pendidikan No. 1, Jakarta',
                'phone_number' => '081234567890',
                'birth_date' => '1975-05-15',
                'birth_place' => 'Jakarta',
                'gender' => 'male',
                'religion' => 'islam',
                'join_date' => '2020-01-01',
                'membership_status' => 'active',
            ],
        ]);

        // Petugas
        $staffData = [
            [
                'email' => 'petugas1@testing.com',
                'name' => 'Siti Nurhaliza',
                'nik' => '3201011234560002',
                'birth_date' => '1985-08-20',
                'birth_place' => 'Bandung',
                'gender' => 'female',
                'address' => 'Jl. Perpustakaan No. 5, Jakarta',
            ],
            [
                'email' => 'petugas2@testing.com',
                'name' => 'Ahmad Fauzi',
                'nik' => '3201011234560003',
                'birth_date' => '1987-03-10',
                'birth_place' => 'Surabaya',
                'gender' => 'male',
                'address' => 'Jl. Literasi No. 10, Jakarta',
            ],
            [
                'email' => 'staff@testing.com',
                'name' => 'Michael Chen',
                'nik' => '3201011234560004',
                'birth_date' => '1990-06-25',
                'birth_place' => 'Medan',
                'gender' => 'male',
                'address' => 'Jl. Buku No. 3, Jakarta',
            ],
        ];

        foreach ($staffData as $data) {
            $this->createUserWithDetails([
                'email' => $data['email'],
                'name' => $data['name'],
                'role' => 'petugas',
                'user_details' => [
                    'nik' => $data['nik'],
                    'phone_number' => '08'.rand(100000000, 999999999),
                    'birth_date' => $data['birth_date'],
                    'birth_place' => $data['birth_place'],
                    'gender' => $data['gender'],
                    'address' => $data['address'],
                    'religion' => 'islam',
                    'join_date' => now()->subMonths(rand(1, 24)),
                    'membership_status' => 'active',
                ],
            ]);
        }

        // Siswa
        $students = [
            [
                'name' => 'Rani Permata Sari',
                'email' => 'siswa1@siswa.sch.id',
                'nis' => '2021001',
                'nisn' => '0051234567',
                'class' => '12 IPA 1',
                'major' => 'IPA',
                'semester' => 6,
                'gender' => 'female',
                'birth_date' => '2004-01-15',
                'birth_place' => 'Jakarta',
            ],
            [
                'name' => 'Muhammad Rizki',
                'email' => 'siswa2@siswa.sch.id',
                'nis' => '2021002',
                'nisn' => '0051234568',
                'class' => '12 IPS 2',
                'major' => 'IPS',
                'semester' => 6,
                'gender' => 'male',
                'birth_date' => '2004-03-22',
                'birth_place' => 'Surabaya',
            ],
            [
                'name' => 'Budi Santoso',
                'email' => 'budi.santoso@siswa.sch.id',
                'nis' => '2021003',
                'nisn' => '0051234569',
                'class' => '11 IPA 3',
                'major' => 'IPA',
                'semester' => 4,
                'gender' => 'male',
                'birth_date' => '2005-07-10',
                'birth_place' => 'Bandung',
            ],
            [
                'name' => 'Siti Aminah',
                'email' => 'siti.aminah@siswa.sch.id',
                'nis' => '2021004',
                'nisn' => '0051234570',
                'class' => '11 Bahasa 1',
                'major' => 'Bahasa',
                'semester' => 4,
                'gender' => 'female',
                'birth_date' => '2005-11-25',
                'birth_place' => 'Yogyakarta',
            ],
            [
                'name' => 'Ahmad Fadli',
                'email' => 'ahmad.fadli@siswa.sch.id',
                'nis' => '2022001',
                'nisn' => '0061234569',
                'class' => '10 IPA 2',
                'major' => 'IPA',
                'semester' => 2,
                'gender' => 'male',
                'birth_date' => '2006-09-18',
                'birth_place' => 'Semarang',
            ],
            [
                'name' => 'Dewi Lestari',
                'email' => 'dewi.lestari@siswa.sch.id',
                'nis' => '2022002',
                'nisn' => '0061234570',
                'class' => '10 IPS 1',
                'major' => 'IPS',
                'semester' => 2,
                'gender' => 'female',
                'birth_date' => '2006-04-05',
                'birth_place' => 'Medan',
            ],
        ];

        foreach ($students as $student) {
            $this->createUserWithDetails([
                'email' => $student['email'],
                'name' => $student['name'],
                'role' => 'siswa',
                'user_details' => [
                    'nik' => '320101'.str_pad(rand(1, 9999), 12, '0', STR_PAD_LEFT),
                    'nis' => $student['nis'],
                    'nisn' => $student['nisn'],
                    'class' => $student['class'],
                    'major' => $student['major'],
                    'semester' => $student['semester'],
                    'gender' => $student['gender'],
                    'birth_date' => $student['birth_date'],
                    'birth_place' => $student['birth_place'],
                    'address' => 'Alamat '.$student['name'],
                    'phone_number' => '08'.rand(100000000, 999999999),
                    'religion' => 'islam',
                    'join_date' => now(),
                    'membership_status' => 'active',
                ],
            ]);
        }

        // Create additional sample students with factory
        $additionalStudents = User::factory(5)->create(['role' => 'siswa']);
        foreach ($additionalStudents as $student) {
            UserDetail::factory()->student()->create(['user_id' => $student->id]);
        }

        // Create sample users with expired memberships
        $expiredUsers = User::factory(2)->create(['role' => 'siswa']);
        foreach ($expiredUsers as $user) {
            UserDetail::factory()->student()->expiredMembership()->create(['user_id' => $user->id]);
        }

        // Create sample users with suspended memberships
        $suspendedUsers = User::factory(1)->create(['role' => 'siswa']);
        foreach ($suspendedUsers as $user) {
            UserDetail::factory()->student()->suspendedMembership()->create(['user_id' => $user->id]);
        }

        $this->command->info('   ✅ Users dan details berhasil dibuat');
    }

    /**
     * Create user dengan details dan assign role
     */
    private function createUserWithDetails(array $data): User
    {
        $user = User::firstOrCreate(
            ['email' => $data['email']],
            [
                'name' => $data['name'],
                'role' => $data['role'],
                'password' => Hash::make('password'),
                'email_verified_at' => now(),
            ]
        );

        if (! $user->userDetail) {
            UserDetail::create(array_merge(['user_id' => $user->id], $data['user_details']));
        }

        return $user;
    }

    /**
     * Display login information
     */
    private function displayLoginInfo(): void
    {
        $this->command->info('');
        $this->command->info('🔑 LOGIN INFORMATION (Password: password):');
        $this->command->info('═══════════════════════════════════════════════════════════════');
        $this->command->info('👑 Super Admin      : admin@testing.com');
        $this->command->info('📚 Ketua Perpus    : ketua@testing.com');
        $this->command->info('👨‍💼 Petugas         : petugas1@testing.com / petugas2@testing.com');
        $this->command->info('👨‍💼 Staff           : staff@testing.com');
        $this->command->info('👨‍🎓 Siswa           : siswa1@siswa.sch.id / siswa2@siswa.sch.id');
        $this->command->info('                     : budi.santoso@siswa.sch.id / siti.aminah@siswa.sch.id');
        $this->command->info('');
        $this->command->info('🎯 Total Users Created:');
        $this->command->info('   • Super Admin: 1');
        $this->command->info('   • Ketua Perpustakaan: 1');
        $this->command->info('   • Petugas/Staff: 3');
        $this->command->info('   • Siswa: 6 (manual) + 5 (factory) + 3 (various status) = 14');
        $this->command->info('');
        $this->command->info('🎯 Available Roles:');
        $roles = User::getAvailableRoles();
        foreach ($roles as $key => $displayName) {
            $this->command->info("   • {$displayName}: {$key}");
        }
        $this->command->info('');
        $this->command->info('🚀 Sistem siap digunakan!');
    }
}
