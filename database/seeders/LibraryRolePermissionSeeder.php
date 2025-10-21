<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Schema;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use Spatie\Permission\PermissionRegistrar;

/**
 * Library Role Permission Seeder
 *
 * This seeder creates roles and permissions specifically designed for
 * the library information system with three main actors:
 * 1. Ketua Perpustakaan (Library Head) - Administrator with full control
 * 2. Petugas (Library Staff) - Operational staff with daily management permissions
 * 3. Siswa (Student) - Regular users with access to library resources
 *
 * Permission structure follows the naming convention: resource_action
 * Example: book_create, user_read, transaction_update
 */
class LibraryRolePermissionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Clear existing permissions and roles
        app()[PermissionRegistrar::class]->forgetCachedPermissions();

        Schema::disableForeignKeyConstraints();
        Permission::query()->delete();
        Role::query()->delete();
        Schema::enableForeignKeyConstraints();

        $this->command->info('🏛️  Membuat Role & Permission untuk Sistem Perpustakaan...');

        // Step 1: Create all permissions
        $this->createPermissions();

        // Step 2: Create roles for library actors
        $roles = $this->createLibraryRoles();

        // Step 3: Assign permissions to roles based on actor requirements
        $this->assignPermissionsToRoles($roles);

        // Step 4: Create sample users for each role
        $this->createSampleUsers();

        $this->command->info('✅ Sistem Role & Permission Perpustakaan Berhasil Dibuat!');
        $this->displayRoleSummary();
    }

    /**
     * Create all necessary permissions for the library system
     */
    private function createPermissions(): void
    {
        $this->command->info('📝 Membuat permissions...');

        $permissions = [
            // Panel & Navigation
            'panel_access' => 'Akses ke panel admin',
            'dashboard_access' => 'Akses ke dashboard',

            // User Management Permissions
            'user_access' => 'Lihat daftar pengguna',
            'user_create' => 'Tambah pengguna baru',
            'user_read' => 'Lihat detail pengguna',
            'user_update' => 'Edit data pengguna',
            'user_delete' => 'Hapus pengguna',

            // Role & Permission Management
            'role_access' => 'Lihat daftar role',
            'role_create' => 'Buat role baru',
            'role_read' => 'Lihat detail role',
            'role_update' => 'Edit role',
            'role_delete' => 'Hapus role',
            'permission_access' => 'Lihat daftar permission',
            'permission_read' => 'Lihat detail permission',

            // Book Management (Manajemen Buku)
            'book_access' => 'Lihat daftar buku',
            'book_create' => 'Tambah buku baru',
            'book_read' => 'Lihat detail buku',
            'book_update' => 'Edit data buku',
            'book_delete' => 'Hapus buku',

            // Category Management (Manajemen Kategori)
            'category_access' => 'Lihat daftar kategori',
            'category_create' => 'Tambah kategori baru',
            'category_read' => 'Lihat detail kategori',
            'category_update' => 'Edit kategori',
            'category_delete' => 'Hapus kategori',

            // Transaction Management (Manajemen Transaksi)
            'transaction_access' => 'Lihat daftar transaksi',
            'transaction_create' => 'Buat transaksi baru',
            'transaction_read' => 'Lihat detail transaksi',
            'transaction_update' => 'Edit transaksi',
            'transaction_delete' => 'Hapus transaksi',
            'transaction_checkout' => 'Proses peminjaman buku',
            'transaction_checkin' => 'Proses pengembalian buku',

            // Status Management (Manajemen Status)
            'status_access' => 'Lihat daftar status',
            'status_read' => 'Lihat detail status',

            // Penalty Management (Manajemen Denda)
            'penalty_access' => 'Lihat daftar denda',
            'penalty_create' => 'Buat denda baru',
            'penalty_read' => 'Lihat detail denda',
            'penalty_update' => 'Edit denda',
            'penalty_delete' => 'Hapus denda',
            'penalty_payment' => 'Proses pembayaran denda',

            // Settings Management (Pengaturan Sistem)
            'setting_access' => 'Lihat pengaturan sistem',
            'setting_update' => 'Edit pengaturan sistem',

            // Report & Analytics
            'report_access' => 'Akses laporan',
            'report_view' => 'Lihat laporan',
            'report_export' => 'Export laporan',

            // User Details Management
            'user_details_access' => 'Lihat detail pengguna perpustakaan',
            'user_details_create' => 'Buat detail pengguna',
            'user_details_read' => 'Lihat detail pengguna',
            'user_details_update' => 'Edit detail pengguna',
            'user_details_delete' => 'Hapus detail pengguna',
        ];

        foreach ($permissions as $name => $description) {
            Permission::create([
                'name' => $name,
                'guard_name' => 'web',
            ]);
        }

        $this->command->info('   ✅ '.count($permissions).' permissions dibuat');
    }

    /**
     * Create roles for library actors
     */
    private function createLibraryRoles(): array
    {
        $this->command->info('👥 Membuat role untuk aktor perpustakaan...');

        $roles = [
            'super_admin' => [
                'name' => 'super_admin',
                'display_name' => 'Super Admin',
                'description' => 'Administrator sistem dengan akses penuh',
            ],
            'ketua_perpustakaan' => [
                'name' => 'ketua_perpustakaan',
                'display_name' => 'Ketua Perpustakaan',
                'description' => 'Pemimpin perpustakaan dengan kontrol administratif penuh',
            ],
            'petugas' => [
                'name' => 'petugas',
                'display_name' => 'Petugas Perpustakaan',
                'description' => 'Staf operasional perpustakaan untuk pengelolaan harian',
            ],
            'siswa' => [
                'name' => 'siswa',
                'display_name' => 'Siswa',
                'description' => 'Pengguna perpustakaan untuk akses sumber daya',
            ],
        ];

        $createdRoles = [];
        foreach ($roles as $key => $roleData) {
            $role = Role::create([
                'name' => $roleData['name'],
                'guard_name' => 'web',
            ]);
            $createdRoles[$key] = $role;
        }

        $this->command->info('   ✅ '.count($roles).' role dibuat');

        return $createdRoles;
    }

    /**
     * Assign permissions to roles based on actor requirements
     */
    private function assignPermissionsToRoles(array $roles): void
    {
        $this->command->info('🔐 Mengassign permissions ke role...');

        $allPermissions = Permission::all();

        // Super Admin - Semua permissions
        $roles['super_admin']->givePermissionTo($allPermissions);
        $this->command->info('   ✅ Super Admin: Semua permissions ('.$allPermissions->count().')');

        // Ketua Perpustakaan - Hampir semua permissions kecuali role management
        $ketuaPermissions = $allPermissions->filter(function ($permission) {
            // Ketua tidak bisa menghapus role atau permission
            return ! in_array($permission->name, [
                'role_delete',
                'permission_delete',
            ]);
        });
        $roles['ketua_perpustakaan']->givePermissionTo($ketuaPermissions);
        $this->command->info('   ✅ Ketua Perpustakaan: '.$ketuaPermissions->count().' permissions');

        // Petugas - Permissions operasional
        $petugasPermissions = Permission::whereIn('name', [
            'panel_access',
            'dashboard_access',
            'book_access',
            'book_create',
            'book_read',
            'book_update',
            'category_access',
            'category_read',
            'transaction_access',
            'transaction_create',
            'transaction_read',
            'transaction_update',
            'transaction_checkout',
            'transaction_checkin',
            'status_access',
            'status_read',
            'penalty_access',
            'penalty_create',
            'penalty_read',
            'penalty_update',
            'penalty_payment',
            'user_details_access',
            'user_details_read',
            'user_details_update',
            'report_access',
            'report_view',
        ])->get();
        $roles['petugas']->givePermissionTo($petugasPermissions);
        $this->command->info('   ✅ Petugas: '.$petugasPermissions->count().' permissions');

        // Siswa - Permissions terbatas untuk akses sumber daya
        $siswaPermissions = Permission::whereIn('name', [
            'panel_access',
            'dashboard_access',
            'book_access',
            'book_read',
            'category_access',
            'category_read',
            'transaction_access',
            'transaction_read',
            'status_access',
            'status_read',
            'penalty_access',
            'penalty_read',
            'user_details_access',
            'user_details_read',
        ])->get();
        $roles['siswa']->givePermissionTo($siswaPermissions);
        $this->command->info('   ✅ Siswa: '.$siswaPermissions->count().' permissions');
    }

    /**
     * Create sample users for each role
     */
    private function createSampleUsers(): void
    {
        $this->command->info('👤 Membuat sample users untuk setiap role...');

        // Super Admin
        $superAdmin = User::firstOrCreate(
            ['email' => 'admin@perpustakaan.sch.id'],
            [
                'name' => 'Super Admin Perpustakaan',
                'password' => bcrypt('password123'),
                'email_verified_at' => now(),
            ]
        );
        $superAdmin->syncRoles(['super_admin']);
        $this->command->info('   ✅ Super Admin: admin@perpustakaan.sch.id');

        // Ketua Perpustakaan
        $ketua = User::firstOrCreate(
            ['email' => 'ketua@perpustakaan.sch.id'],
            [
                'name' => 'Dr. Budi Santoso, M.Pd',
                'password' => bcrypt('password123'),
                'email_verified_at' => now(),
            ]
        );
        $ketua->syncRoles(['ketua_perpustakaan']);
        $this->command->info('   ✅ Ketua Perpustakaan: ketua@perpustakaan.sch.id');

        // Petugas 1
        $petugas1 = User::firstOrCreate(
            ['email' => 'petugas1@perpustakaan.sch.id'],
            [
                'name' => 'Siti Nurhaliza',
                'password' => bcrypt('password123'),
                'email_verified_at' => now(),
            ]
        );
        $petugas1->syncRoles(['petugas']);
        $this->command->info('   ✅ Petugas 1: petugas1@perpustakaan.sch.id');

        // Petugas 2
        $petugas2 = User::firstOrCreate(
            ['email' => 'petugas2@perpustakaan.sch.id'],
            [
                'name' => 'Ahmad Fauzi',
                'password' => bcrypt('password123'),
                'email_verified_at' => now(),
            ]
        );
        $petugas2->syncRoles(['petugas']);
        $this->command->info('   ✅ Petugas 2: petugas2@perpustakaan.sch.id');

        // Siswa 1
        $siswa1 = User::firstOrCreate(
            ['email' => 'siswa1@siswa.sch.id'],
            [
                'name' => 'Rani Permata Sari',
                'password' => bcrypt('password123'),
                'email_verified_at' => now(),
            ]
        );
        $siswa1->syncRoles(['siswa']);
        $this->command->info('   ✅ Siswa 1: siswa1@siswa.sch.id');

        // Siswa 2
        $siswa2 = User::firstOrCreate(
            ['email' => 'siswa2@siswa.sch.id'],
            [
                'name' => 'Muhammad Rizki',
                'password' => bcrypt('password123'),
                'email_verified_at' => now(),
            ]
        );
        $siswa2->syncRoles(['siswa']);
        $this->command->info('   ✅ Siswa 2: siswa2@siswa.sch.id');
    }

    /**
     * Display summary of created roles and their permissions
     */
    private function displayRoleSummary(): void
    {
        $this->command->info('');
        $this->command->info('📊 RINGKASAN ROLE & PERMISSION SISTEM PERPUSTAKAAN');
        $this->command->info(str_repeat('=', 60));
        $this->command->info('');

        $roles = Role::with('permissions')->get();

        foreach ($roles as $role) {
            $this->command->info('🎭 '.strtoupper($role->name));
            $this->command->info('   📋 Total Permissions: '.$role->permissions->count());

            // Group permissions by resource
            $groupedPermissions = $role->permissions->groupBy(function ($permission) {
                return explode('_', $permission->name)[0];
            });

            foreach ($groupedPermissions as $resource => $permissions) {
                $actions = $permissions->map(function ($permission) {
                    return explode('_', $permission->name)[1];
                })->implode(', ');
                $this->command->info('   📚 '.ucfirst($resource).': '.$actions);
            }
            $this->command->info('');
        }

        $this->command->info('🔑 LOGIN INFO (Password: password123):');
        $this->command->info('   Super Admin: admin@perpustakaan.sch.id');
        $this->command->info('   Ketua Perpus: ketua@perpustakaan.sch.id');
        $this->command->info('   Petugas: petugas1@perpustakaan.sch.id / petugas2@perpustakaan.sch.id');
        $this->command->info('   Siswa: siswa1@siswa.sch.id / siswa2@siswa.sch.id');
        $this->command->info('');
    }
}
