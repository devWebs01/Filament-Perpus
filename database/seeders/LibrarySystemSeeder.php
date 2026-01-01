<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\UserDetail;
use App\Services\BarcodeService;
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
    private BarcodeService $barcodeService;

    public function __construct(BarcodeService $barcodeService)
    {
        $this->barcodeService = $barcodeService;
    }

    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Force refresh permission cache to prevent race conditions
        app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();

        $this->command->info('🚀 Memulai Library System Seeder...');
        $this->command->info('🔄 Permission cache cleared');

        // Verify roles exist before proceeding
        $this->verifyRolesExist();

        // Create users dengan details lengkap
        $this->createUsersWithDetails();

        // Generate QR codes untuk user details yang baru dibuat
        $this->command->info('📱 Generating QR codes for users...');

        // Generate additional QR codes for existing users without codes
        $this->generateMissingQrCodes();

        // Tampilkan informasi QR codes yang telah digenerate
        $this->displayQrCodeInfo();

        $this->command->info('✅ Library System Seeder berhasil dijalankan!');
        $this->displayLoginInfo();
    }

    /**
     * Create users dengan details lengkap
     */
    private function createUsersWithDetails(): void
    {
        $this->command->info('👤 Membuat users dengan details...');

        // Super Admin (Hidden - untuk system maintenance)
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
                'email' => 'siswa@testing.com',
                'nis' => '2021001',
                'nisn' => '0051234567',
                'class' => '12 IPA 1',
                'gender' => 'female',
                'birth_date' => '2004-01-15',
                'birth_place' => 'Jakarta',
            ],
            [
                'name' => 'Muhammad Rizki',
                'email' => 'siswa2@testing.com',
                'nis' => '2021002',
                'nisn' => '0051234568',
                'class' => '12 IPS 2',
                'gender' => 'male',
                'birth_date' => '2004-03-22',
                'birth_place' => 'Surabaya',
            ],
            [
                'name' => 'Udin Testing',
                'email' => 'anggota@testing.com',
                'nis' => '2021003',
                'nisn' => '0051234569',
                'class' => '11 IPA 3',
                'gender' => 'male',
                'birth_date' => '2005-07-10',
                'birth_place' => 'Bandung',
            ],
            [
                'name' => 'Siti Aminah',
                'email' => 'siti.aminah@testing.com',
                'nis' => '2021004',
                'nisn' => '0051234570',
                'class' => '11 Bahasa 1',
                'gender' => 'female',
                'birth_date' => '2005-11-25',
                'birth_place' => 'Yogyakarta',
            ],
            [
                'name' => 'Ahmad Fadli',
                'email' => 'ahmad.fadli@testing.com',
                'nis' => '2022001',
                'nisn' => '0061234569',
                'class' => '10 IPA 2',
                'gender' => 'male',
                'birth_date' => '2006-09-18',
                'birth_place' => 'Semarang',
            ],
            [
                'name' => 'Dewi Lestari',
                'email' => 'dewi.lestari@testing.com',
                'nis' => '2022002',
                'nisn' => '0061234570',
                'class' => '10 IPS 1',
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
        $additionalStudents = User::factory(5)->siswa()->create();
        foreach ($additionalStudents as $student) {
            $userDetail = UserDetail::factory()->student()->create(['user_id' => $student->id]);
            // Generate QR code for factory-created user details
            if (empty($userDetail->qr_code)) {
                $uniqueQrCode = 'LIB_USER_'.strtoupper(substr(md5($userDetail->user_id.$userDetail->id.$student->email.now()->timestamp.microtime(true)), 0, 12));
                $qrCodePath = $this->barcodeService->generateUserQRCode($userDetail->user_id, $uniqueQrCode);
                $userDetail->update(['qr_code' => $qrCodePath]);
            }
        }

        // Create sample users with expired memberships
        $expiredUsers = User::factory(2)->siswa()->create();
        foreach ($expiredUsers as $user) {
            $userDetail = UserDetail::factory()->student()->expiredMembership()->create(['user_id' => $user->id]);
            // Generate QR code for factory-created user details
            if (empty($userDetail->qr_code)) {
                $uniqueQrCode = 'LIB_USER_'.strtoupper(substr(md5($userDetail->user_id.$userDetail->id.$user->email.now()->timestamp.microtime(true)), 0, 12));
                $qrCodePath = $this->barcodeService->generateUserQRCode($userDetail->user_id, $uniqueQrCode);
                $userDetail->update(['qr_code' => $qrCodePath]);
            }
        }

        // Create sample users with suspended memberships
        $suspendedUsers = User::factory(1)->siswa()->create();
        foreach ($suspendedUsers as $user) {
            $userDetail = UserDetail::factory()->student()->suspendedMembership()->create(['user_id' => $user->id]);
            // Generate QR code for factory-created user details
            if (empty($userDetail->qr_code)) {
                $uniqueQrCode = 'LIB_USER_'.strtoupper(substr(md5($userDetail->user_id.$userDetail->id.$user->email.now()->timestamp.microtime(true)), 0, 12));
                $qrCodePath = $this->barcodeService->generateUserQRCode($userDetail->user_id, $uniqueQrCode);
                $userDetail->update(['qr_code' => $qrCodePath]);
            }
        }

        $this->command->info('   ✅ Users dan details berhasil dibuat');
    }

    /**
     * Create user dengan details dan assign role
     */
    private function createUserWithDetails(array $data): User
    {
        // Validate role exists before creating user
        $roleExists = \Spatie\Permission\Models\Role::where('name', $data['role'])
            ->where('guard_name', 'web')
            ->exists();

        if (! $roleExists) {
            $this->command->error("❌ Role '{$data['role']}' tidak ditemukan!");
            throw new \Exception("Role '{$data['role']}' not found. Pastikan RoleAndPermissionSeeder sudah dijalankan terlebih dahulu.");
        }

        $user = User::firstOrCreate(
            ['email' => $data['email']],
            [
                'name' => $data['name'],
                'password' => Hash::make('password'),
                'email_verified_at' => now(),
            ]
        );

        // Assign role using Spatie Permission with error handling
        try {
            if (! $user->hasRole($data['role'])) {
                $user->assignRole($data['role']);
                $this->command->info("   ✅ Role '{$data['role']}' assigned to {$user->email}");
            } else {
                $this->command->info("   ℹ️  User {$user->email} already has role '{$data['role']}'");
            }
        } catch (\Exception $e) {
            $this->command->error("   ❌ Gagal assign role '{$data['role']}' to {$user->email}: {$e->getMessage()}");
            throw $e;
        }

        if (! $user->userDetail) {
            $userDetail = UserDetail::create(array_merge(['user_id' => $user->id], $data['user_details']));

            // Generate QR code for the new user detail
            if (empty($userDetail->qr_code)) {
                $uniqueQrCode = 'LIB_USER_'.strtoupper(substr(md5($userDetail->user_id.$userDetail->id.$user->email.now()->timestamp.microtime(true)), 0, 12));
                $qrCodePath = $this->barcodeService->generateUserQRCode($userDetail->user_id, $uniqueQrCode);
                $userDetail->update(['qr_code' => $qrCodePath]);
            }
        }

        return $user;
    }

    /**
     * Display QR code information untuk semua users
     */
    private function displayQrCodeInfo(): void
    {
        $this->command->info('');
        $this->command->info('📱 INFORMASI QR CODE YANG TELAH DIGENERATE:');
        $this->command->info('═══════════════════════════════════════════════════════════════');

        // Tampilkan QR codes untuk users yang telah dibuat
        $userDetails = \App\Models\UserDetail::with('user')->whereNotNull('qr_code')->get();

        if ($userDetails->isNotEmpty()) {
            $this->command->info('🎯 QR Code Users:');
            foreach ($userDetails as $detail) {
                $userType = match (true) {
                    ! empty($detail->nis) || ! empty($detail->nisn) => '👨‍🎓 Siswa',
                    $detail->user?->email === 'admin@testing.com' => '👔‍💼 Admin',
                    $detail->user?->hasRole(['ketua_perpustakaan', 'petugas']) => '👨‍💼 Staff',
                    default => '👤 Unknown'
                };

                $statusBadge = match ($detail->membership_status) {
                    'active' => '✅',
                    'suspended' => '⚠️',
                    'expired' => '❌',
                    default => '❓'
                };

                $qrCodeData = BarcodeService::parseBarcode($detail->qr_code);
                $qrCodeDisplay = $detail->qr_code;

                $this->command->info("  {$userType} {$detail->user?->name}");
                $this->command->info("    📧 Email: {$detail->user?->email}");
                $this->command->info("    🪪 QR Code: {$qrCodeDisplay}");
                $this->command->info("    📋 Status: {$statusBadge} {$detail->membership_status}");

                if (! empty($detail->nis)) {
                    $this->command->info("    🎓 NIS: {$detail->nis}");
                }
                if (! empty($detail->nisn)) {
                    $this->command->info("    📜 NISN: {$detail->nisn}");
                }
                if (! empty($detail->class)) {
                    $this->command->info("    📚 Kelas: {$detail->class}");
                }
                $this->command->info('');
            }
        } else {
            $this->command->info('⚠️  Belum ada QR code yang digenerate.');
        }

        $this->command->info('═══════════════════════════════════════════════════════════');
        $this->command->info('');
    }

    /**
     * Generate additional QR codes for users without codes
     */
    private function generateMissingQrCodes(): void
    {
        $this->command->info('🔄 Generating additional QR codes for existing users...');

        // Find users without QR codes
        $usersWithoutQr = UserDetail::whereNull('qr_code')->with('user')->get();

        foreach ($usersWithoutQr as $userDetail) {
            // Generate unique QR code based on user information
            $uniqueQrCode = 'LIB_USER_'.strtoupper(substr(md5($userDetail->user_id.$userDetail->id.$userDetail->user?->email.now()->timestamp.microtime(true)), 0, 12));
            $qrCodeData = $this->barcodeService->generateUserQRCode($userDetail->user_id, $uniqueQrCode);

            $userDetail->update([
                'qr_code' => $qrCodeData,
                'membership_status' => $userDetail->membership_status ?? 'active', // Keep existing status or set to active
            ]);

            $this->command->info("   📱 QR Code for {$userDetail->user?->name}: {$uniqueQrCode}");
        }

        $this->command->info('   ✅ Additional QR codes generated successfully!');
    }

    /**
     * Display login information
     */
    private function displayLoginInfo(): void
    {
        $this->command->info('');
        $this->command->info('🔑 LOGIN INFORMATION (Password: password):');
        $this->command->info('═══════════════════════════════════════════════════════════════');
        $this->command->info('👑 Super Admin      : admin@testing.com (System Maintenance Only)');
        $this->command->info('📚 Ketua Perpus    : ketua@testing.com');
        $this->command->info('👨‍💼 Petugas         : petugas1@testing.com / petugas2@testing.com');
        $this->command->info('👨‍🎓 Siswa           : siswa@testing.com / siswa2@testing.com');
        $this->command->info('                     : anggota@testing.com / siti.aminah@testing.com');
        $this->command->info('                     : ahmad.fadli@testing.com / dewi.lestari@testing.com');
        $this->command->info('');
        $this->command->info('🎯 3 ROLE UTAMA SISTEM:');
        $this->command->info('═══════════════════════════════════════════════════════════════');
        $this->command->info('📚 Ketua Perpustakaan: Manajerial level, mengatur seluruh sistem');
        $this->command->info('👨‍💼 Petugas         : Operasional level, melayani siswa & kelola buku');
        $this->command->info('👨‍🎓 Siswa           : User level, pinjam & akses buku');
        $this->command->info('');
        $this->command->info('🎯 Total Users Created:');
        $this->command->info('   • Super Admin: 1 (Hidden - System Only)');
        $this->command->info('   • Ketua Perpustakaan: 1');
        $this->command->info('   • Petugas: 3');
        $this->command->info('   • Siswa: 6 (manual) + 5 (factory) + 3 (various status) = 14');
        $this->command->info('');
        $this->command->info('📱 QR Code Status:');
        $totalUsers = UserDetail::count();
        $usersWithQr = UserDetail::whereNotNull('qr_code')->count();
        $this->command->info("   • Total users with QR codes: {$usersWithQr}/{$totalUsers}");
        if ($totalUsers === $usersWithQr) {
            $this->command->info('   • All users have QR codes generated! ✅');
        } else {
            $this->command->info('   • Some users may need QR codes generated');
        }
        $this->command->info('');
        $this->command->info('🚀 Sistem siap digunakan!');
    }

    /**
     * Verify that all required roles exist before creating users
     */
    private function verifyRolesExist(): void
    {
        $requiredRoles = ['super_admin', 'ketua_perpustakaan', 'petugas', 'siswa'];
        $missingRoles = [];

        foreach ($requiredRoles as $roleName) {
            $exists = \Spatie\Permission\Models\Role::where('name', $roleName)
                ->where('guard_name', 'web')
                ->exists();

            if (! $exists) {
                $missingRoles[] = $roleName;
            }
        }

        if (! empty($missingRoles)) {
            $this->command->error('❌ The following required roles are missing: '.implode(', ', $missingRoles));
            $this->command->error('⚠️  Please run RoleAndPermissionSeeder first!');
            throw new \Exception('Required roles not found. Run RoleAndPermissionSeeder before LibrarySystemSeeder.');
        }

        $this->command->info('✅ All required roles verified');
    }
}
