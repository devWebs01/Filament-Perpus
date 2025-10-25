# Simple Role System Documentation

## Overview

Project ini menggunakan sistem role yang **sederhana** tanpa package Spatie Shield. Role disimpan langsung di field `role` pada tabel `users`.

## Sistem Role

### 🎭 **Available Roles:**

| Role Key | Display Name | Description |
|----------|--------------|-------------|
| `super_admin` | Super Admin | Administrator sistem dengan akses penuh |
| `ketua_perpustakaan` | Ketua Perpustakaan | Pemimpin dengan kontrol administratif penuh |
| `petugas` | Petugas | Staf operasional untuk pengelolaan harian |
| `siswa` | Siswa | Pengguna perpustakaan untuk akses sumber daya |

### 🛡️ **Role Hierarchy:**

1. **Super Admin** - Akses penuh ke semua fitur
2. **Ketua Perpustakaan** - Akses admin tanpa hapus user system
3. **Petugas** - Akses kelola buku dan transaksi
4. **Siswa** - Akses lihat katalog dan pinjam buku

## Cara Penggunaan

### 1. **User Model Methods:**

```php
// Check specific role
$user->hasRole('super_admin');        // true/false
$user->hasRole('petugas');             // true/false

// Check role groups
$user->isSuperAdmin();                 // super_admin only
$user->isAdmin();                      // super_admin OR ketua_perpustakaan
$user->isStaff();                      // super_admin OR ketua_perpustakaan OR petugas
$user->isStudent();                    // siswa only

// Get role display name
$user->getRoleDisplayName();            // "Super Admin", "Petugas", etc

// Get all available roles
User::getAvailableRoles();             // Array of all roles
```

### 2. **Policy Authorization:**

System menggunakan Laravel Policies dengan role-based checks:

```php
// Example in UserPolicy
public function delete(User $user, User $model): bool
{
    // Users cannot delete themselves
    if ($user->id === $model->id) {
        return false;
    }

    // Only super admin can delete users
    return $user->isSuperAdmin();
}
```

### 3. **Seeder System:**

```bash
# Run all seeders
php artisan db:seed

# Run only user system seeder
php artisan db:seed --class=LibrarySystemSeeder
```

### 4. **Migration & Setup:**

```bash
# Run migration to add role field
php artisan migrate

# Migration yang dijalankan:
# - 2025_10_25_152628_add_role_to_users_table.php
```

## Access Control Matrix

| Resource/Action | Super Admin | Ketua Perpus | Petugas | Siswa |
|------------------|-------------|--------------|---------|-------|
| **Users** |
| View Users | ✅ | ✅ | ✅ | ❌ |
| Create Users | ✅ | ✅ | ❌ | ❌ |
| Update Users | ✅ | ✅ | ❌ | Themselves only |
| Delete Users | ✅ | ❌ | ❌ | ❌ |
| **Books** |
| View Books | ✅ | ✅ | ✅ | ✅ |
| Create Books | ✅ | ✅ | ✅ | ❌ |
| Update Books | ✅ | ✅ | ✅ | ❌ |
| Delete Books | ✅ | ✅ | ❌ | ❌ |
| **Transactions** |
| View Transactions | ✅ | ✅ | ✅ | ✅ |
| Create Transactions | ✅ | ✅ | ✅ | ❌ |
| Update Transactions | ✅ | ✅ | ✅ | Themselves only |
| Delete Transactions | ✅ | ✅ | ❌ | ❌ |
| **Categories** |
| View Categories | ✅ | ✅ | ✅ | ✅ |
| Create Categories | ✅ | ✅ | ✅ | ❌ |
| Update Categories | ✅ | ✅ | ✅ | ❌ |
| Delete Categories | ✅ | ✅ | ❌ | ❌ |

## Login Credentials

Default users yang dibuat oleh seeder (password: `password`):

| Role | Email | Name |
|------|-------|------|
| Super Admin | admin@testing.com | Super Admin Perpustakaan |
| Ketua Perpus | ketua@testing.com | Dr. Budi Santoso, M.Pd |
| Petugas | petugas1@testing.com | Siti Nurhaliza |
| Petugas | petugas2@testing.com | Ahmad Fauzi |
| Staff | staff@testing.com | Michael Chen |
| Siswa | siswa1@siswa.sch.id | Rani Permata Sari |
| Siswa | siswa2@siswa.sch.id | Muhammad Rizki |

## File Structure

### **Models & Policies:**
- `app/Models/User.php` - User model dengan role methods
- `app/Policies/` - Authorization policies menggunakan role checks

### **Database:**
- `database/migrations/2025_10_25_152628_add_role_to_users_table.php`
- `database/seeders/LibrarySystemSeeder.php` - Users dan roles
- `database/seeders/DatabaseSeeder.php` - Main seeder

### **Filament Resources:**
- `app/Filament/Resources/UserResource.php` - Form dan table dengan role field

## Migration from Spatie Shield

### ✅ **Removed:**
- Package `bezhansalleh/filament-shield`
- Package `spatie/laravel-permission`
- Permission tables (`permissions`, `roles`, `role_has_permissions`, dll)
- Complex permission system

### ✅ **Replaced With:**
- Simple `role` field in `users` table
- Role methods in User model
- Policy-based authorization
- Cleaner seeder system

## Benefits

1. **🚀 Simpler** - No complex permission tables
2. **⚡ Faster** - Direct field access, no joins needed
3. **🎯 Clearer** - Easy to understand role hierarchy
4. **🛠️ Maintainable** - Less code, easier to debug
5. **💾 Smaller** - Fewer database tables and dependencies

## Security Notes

- **Super Admin** adalah role tertinggi dengan akses penuh
- **Role assignment** hanya bisa dilakukan oleh admin
- **Self-service**: User bisa update profile sendiri
- **Role hierarchy** diimplementasi di level application logic, bukan database level