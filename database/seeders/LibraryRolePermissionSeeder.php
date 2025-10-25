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

        // Clear cache again after deletion
        app()[PermissionRegistrar::class]->forgetCachedPermissions();

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
            // Simple permission structure
            'admin_access' => 'Akses admin penuh - semua fitur',
            'staff_access' => 'Akses staff - kelola buku dan transaksi',
            'member_access' => 'Akses member - lihat katalog dan pinjam buku',
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

        // Get permission objects
        $adminPermission = Permission::where('name', 'admin_access')->first();
        $staffPermission = Permission::where('name', 'staff_access')->first();
        $memberPermission = Permission::where('name', 'member_access')->first();

        // Super Admin - Akses admin penuh
        $roles['super_admin']->givePermissionTo($adminPermission);
        $this->command->info('   ✅ Super Admin: admin_access');

        // Ketua Perpustakaan - Akses admin penuh
        $roles['ketua_perpustakaan']->givePermissionTo($adminPermission);
        $this->command->info('   ✅ Ketua Perpustakaan: admin_access');

        // Petugas - Akses staff
        $roles['petugas']->givePermissionTo($staffPermission);
        $this->command->info('   ✅ Petugas: staff_access');

        // Siswa - Akses member
        $roles['siswa']->givePermissionTo($memberPermission);
        $this->command->info('   ✅ Siswa: member_access');
    }

    /**
     * Create sample users for each role
     */
    private function createSampleUsers(): void
    {
        $this->command->info('👤 Membuat sample users untuk setiap role...');

        // Super Admin
        $superAdmin = User::firstOrCreate(
            ['email' => 'admin@testing.com'],
            [
                'name' => 'Super Admin Perpustakaan',
                'password' => bcrypt('password'),
                'email_verified_at' => now(),
            ]
        );
        $superAdmin->syncRoles(['super_admin']);
        $this->command->info('   ✅ Super Admin: admin@testing.com');

        // Ketua Perpustakaan
        $ketua = User::firstOrCreate(
            ['email' => 'ketua@testing.com'],
            [
                'name' => 'Dr. Budi Santoso, M.Pd',
                'password' => bcrypt('password'),
                'email_verified_at' => now(),
            ]
        );
        $ketua->syncRoles(['ketua_perpustakaan']);
        $this->command->info('   ✅ Ketua Perpustakaan: ketua@testing.com');

        // Petugas 1
        $petugas1 = User::firstOrCreate(
            ['email' => 'petugas1@testing.com'],
            [
                'name' => 'Siti Nurhaliza',
                'password' => bcrypt('password'),
                'email_verified_at' => now(),
            ]
        );
        $petugas1->syncRoles(['petugas']);
        $this->command->info('   ✅ Petugas 1: petugas1@testing.com');

        // Petugas 2
        $petugas2 = User::firstOrCreate(
            ['email' => 'petugas2@testing.com'],
            [
                'name' => 'Ahmad Fauzi',
                'password' => bcrypt('password'),
                'email_verified_at' => now(),
            ]
        );
        $petugas2->syncRoles(['petugas']);
        $this->command->info('   ✅ Petugas 2: petugas2@testing.com');

        // Siswa 1
        $siswa1 = User::firstOrCreate(
            ['email' => 'siswa1@siswa.sch.id'],
            [
                'name' => 'Rani Permata Sari',
                'password' => bcrypt('password'),
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
                'password' => bcrypt('password'),
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
        $this->command->info('📊 RINGKASAN ROLE & PERMISSION SISTEM PERPUSTAKAAN (SIMPLIFIED)');
        $this->command->info(str_repeat('=', 70));
        $this->command->info('');

        $roles = Role::with('permissions')->get();

        foreach ($roles as $role) {
            $this->command->info('🎭 '.strtoupper($role->name));
            $this->command->info('   📋 Permission: '.$role->permissions->pluck('name')->first());

            // Describe what each permission allows
            $permission = $role->permissions->first();
            if ($permission) {
                switch ($permission->name) {
                    case 'admin_access':
                        $this->command->info('   🔑 Akses: Semua fitur sistem (users, roles, settings, reports)');
                        break;
                    case 'staff_access':
                        $this->command->info('   🔑 Akses: Kelola buku, transaksi, laporan dasar');
                        break;
                    case 'member_access':
                        $this->command->info('   🔑 Akses: Lihat katalog, pinjam/kembali buku');
                        break;
                }
            }
            $this->command->info('');
        }

        $this->command->info('🔑 LOGIN INFO (Password: password):');
        $this->command->info('   Super Admin: admin@testing.com');
        $this->command->info('   Ketua Perpus: ketua@testing.com');
        $this->command->info('   Petugas: petugas1@testing.com / petugas2@testing.com');
        $this->command->info('   Siswa: siswa1@siswa.sch.id / siswa2@siswa.sch.id');
        $this->command->info('');
        $this->command->info('🎯 Sistem permission disederhanakan menjadi 3 tingkat akses');
        $this->command->info('');
    }
}
