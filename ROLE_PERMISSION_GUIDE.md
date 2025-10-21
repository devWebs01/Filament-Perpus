# Panduan Role & Permission Sistem Perpustakaan

## 📋 Overview

Sistem informasi perpustakaan ini menggunakan sistem role dan permission berbasis **Filament Shield** untuk mengelola akses pengguna sesuai dengan aktor di perpustakaan.

## 🎭 Aktor & Role System

### 🔴 Super Admin
- **Deskripsi:** Administrator sistem dengan akses penuh
- **Total Permissions:** 49
- **Hak Akses:** Kontrol penuh terhadap semua fitur sistem

### 🟠 Ketua Perpustakaan
- **Deskripsi:** Pemimpin perpustakaan dengan kontrol administratif penuh
- **Total Permissions:** 48
- **Hak Akses:** Hampir semua fitur kecuali menghapus role dan permission

### 🔵 Petugas Perpustakaan
- **Deskripsi:** Staf operasional perpustakaan untuk pengelolaan harian
- **Total Permissions:** 26
- **Hak Akses:** Manajemen buku, transaksi, dan denda

### 🟢 Siswa
- **Deskripsi:** Pengguna perpustakaan untuk akses sumber daya
- **Total Permissions:** 14
- **Hak Akses:** Melihat katalog buku dan transaksi pribadi

## 🔐 Permission Structure

### Kategori Permissions:

#### **Panel & Navigation**
- `panel_access` - Akses ke panel admin
- `dashboard_access` - Akses ke dashboard

#### **User Management**
- `user_access` - Lihat daftar pengguna
- `user_create` - Tambah pengguna baru
- `user_read` - Lihat detail pengguna
- `user_update` - Edit data pengguna
- `user_delete` - Hapus pengguna

#### **Role & Permission Management**
- `role_access` - Lihat daftar role
- `role_create` - Buat role baru
- `role_read` - Lihat detail role
- `role_update` - Edit role
- `role_delete` - Hapus role (Super Admin only)
- `permission_access` - Lihat daftar permission
- `permission_read` - Lihat detail permission

#### **Book Management**
- `book_access` - Lihat daftar buku
- `book_create` - Tambah buku baru
- `book_read` - Lihat detail buku
- `book_update` - Edit data buku
- `book_delete` - Hapus buku

#### **Category Management**
- `category_access` - Lihat daftar kategori
- `category_create` - Tambah kategori baru
- `category_read` - Lihat detail kategori
- `category_update` - Edit kategori
- `category_delete` - Hapus kategori

#### **Transaction Management**
- `transaction_access` - Lihat daftar transaksi
- `transaction_create` - Buat transaksi baru
- `transaction_read` - Lihat detail transaksi
- `transaction_update` - Edit transaksi
- `transaction_delete` - Hapus transaksi
- `transaction_checkout` - Proses peminjaman buku
- `transaction_checkin` - Proses pengembalian buku

#### **Penalty Management**
- `penalty_access` - Lihat daftar denda
- `penalty_create` - Buat denda baru
- `penalty_read` - Lihat detail denda
- `penalty_update` - Edit denda
- `penalty_delete` - Hapus denda
- `penalty_payment` - Proses pembayaran denda

#### **Settings & Reports**
- `setting_access` - Lihat pengaturan sistem
- `setting_update` - Edit pengaturan sistem
- `report_access` - Akses laporan
- `report_view` - Lihat laporan
- `report_export` - Export laporan

## 👤 Akun Sample

### Login Information
**Password:** `password123`

| Role | Email | Nama | Deskripsi |
|------|-------|------|-----------|
| 🔴 Super Admin | `admin@testing.com` | Test User | Akses penuh sistem (Default Admin) |
| 🔴 Super Admin | `admin@perpustakaan.sch.id` | Super Admin Perpustakaan | Akses penuh sistem |
| 🟠 Ketua Perpustakaan | `ketua@perpustakaan.sch.id` | Dr. Budi Santoso, M.Pd | Kontrol administratif |
| 🔵 Petugas 1 | `petugas1@perpustakaan.sch.id` | Siti Nurhaliza | Staf operasional |
| 🔵 Petugas 2 | `petugas2@perpustakaan.sch.id` | Ahmad Fauzi | Staf operasional |
| 🟢 Siswa 1 | `siswa1@siswa.sch.id` | Rani Permata Sari | Pengguna siswa |
| 🟢 Siswa 2 | `siswa2@siswa.sch.id` | Muhammad Rizki | Pengguna siswa |

**⚡ Quick Access:** Gunakan `admin@testing.com` untuk akses super admin instan!

## 🚀 Cara Penggunaan

### 1. Mengakses System
- Buka `/admin` di browser
- Login menggunakan akun yang sesuai dengan role Anda

### 2. Manajemen User
- **Super Admin & Ketua Perpustakaan:** Dapat membuat, mengedit, dan menghapus user
- **Petugas:** Hanya dapat melihat dan mengedit detail user yang ada
- **Siswa:** Hanya dapat melihat detail user sendiri

### 3. Manajemen Buku
- **Super Admin & Ketua Perpustakaan:** Full CRUD buku
- **Petugas:** Dapat menambah, edit, dan lihat buku (tidak bisa hapus)
- **Siswa:** Hanya dapat melihat katalog buku

### 4. Transaksi Peminjaman
- **Petugas:** Dapat memproses peminjaman dan pengembalian
- **Siswa:** Hanya dapat melihat transaksi pribadi

### 5. Laporan & Analytics
- **Super Admin & Ketua Perpustakaan:** Akses lengkap laporan
- **Petugas:** Dapat melihat laporan dasar
- **Siswa:** Tidak memiliki akses laporan

## 🛠️ Technical Implementation

### File Structure
```
database/seeders/
├── LibraryRolePermissionSeeder.php    # Seeder utama role & permission
├── AdminSuperAdminSeeder.php         # Seeder setup admin@testing.com
├── ShieldSeeder.php                   # Seeder Shield (deprecated)
└── AssignUserRolesSeeder.php         # Seeder assignment role user

app/Filament/Resources/Users/
├── UserResource.php                   # Resource utama user
├── Schemas/UserForm.php              # Form user dengan role assignment
├── Tables/UsersTable.php             # Tabel user dengan filter role
├── Pages/CreateUser.php              # Page create user
└── Pages/EditUser.php                # Page edit user
```

### Seeder Commands
```bash
# Jalankan seeder utama (roles & permissions)
php artisan db:seed --class=LibraryRolePermissionSeeder

# Setup admin@testing.com sebagai super admin
php artisan db:seed --class=AdminSuperAdminSeeder

# Fresh seed (hapus semua data dan recreate)
php artisan migrate:fresh --seed
```

### Permission Checks in Code
```php
// Cek permission di controller/pages
auth()->user()->can('user_create')
auth()->user()->can('book_delete')
auth()->user()->can('transaction_checkout')

// Cek permission di blade views
@can('user_update')
    <!-- Show edit button -->
@endcan
```

## 🔒 Security Features

1. **Role-Based Access Control (RBAC)**
   - Setiap akses ke fitur sistem dikontrol oleh permission
   - Permission dikelompokkan berdasarkan resource dan action

2. **Hierarchical Permission**
   - Role yang lebih tinggi memiliki permission yang lebih luas
   - Role yang lebih rendah hanya memiliki permission yang diperlukan

3. **Secure Form Handling**
   - Form hanya menampilkan field yang sesuai dengan permission
   - Validation dan authorization di setiap level

4. **Audit Trail**
   - Semua perubahan data tercatat dengan user stamps
   - Soft delete untuk data protection

## 📝 Best Practices

1. **Principle of Least Privilege**
   - Berikan permission minimum yang diperlukan untuk setiap role
   - Review permission secara berkala

2. **Role Assignment**
   - Assign role sesuai dengan job function
   - Gunakan role combination hanya jika diperlukan

3. **Security**
   - Gunakan password yang kuat
   - Update permission saat ada perubahan job function
   - Monitor akses yang tidak normal

## 🆘 Troubleshooting

### Common Issues
1. **Permission Not Working**
   - Clear cache: `php artisan cache:clear`
   - Restart queue worker jika menggunakan queue

2. **User Cannot Access Feature**
   - Check role assignment: `php artisan tinker`
   - Verify permission exists in database

3. **Seeder Issues**
   - Fresh seed: `php artisan migrate:fresh --seed`
   - Check migration status: `php artisan migrate:status`

### Debug Commands
```bash
# Check user roles
php artisan tinker
> User::find(1)->roles;

# Check role permissions
php artisan tinker
> Role::where('name', 'petugas')->first()->permissions;

# Clear permission cache
php artisan cache:clear
php artisan config:clear
```

---

*Last Updated: 2025-10-22*
*Version: 1.0.0*