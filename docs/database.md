# Database Documentation - Sistem Perpustakaan

## Overview

Sistem Perpustakaan menggunakan database relasional dengan Laravel 12. Database ini menyimpan data terkait manajemen buku, peminjaman, anggota, dan konfigurasi sistem perpustakaan.

---

## Daftar Tabel

### Tabel Utama
1. **users** - Data pengguna/sistem
2. **user_details** - Detail lengkap anggota perpustakaan
3. **books** - Data buku
4. **categories** - Kategori buku
5. **transactions** - Transaksi peminjaman
6. **statuses** - Status transaksi
7. **bookmarks** - Bookmark buku oleh anggota
8. **settings** - Konfigurasi sistem perpustakaan

### Tabel Sistem
9. **permissions** - Hak akses (Spatie Permission)
10. **roles** - Peran pengguna
11. **model_has_permissions** - Relasi permission-model
12. **model_has_roles** - Relasi role-model
13. **role_has_permissions** - Relasi role-permission
14. **notifications** - Notifikasi sistem
15. **cache** - Cache sistem
16. **cache_locks** - Lock cache
17. **jobs** - Queue jobs
18. **job_batches** - Batch jobs
19. **failed_jobs** - Jobs yang gagal
20. **sessions** - Sesi pengguna
21. **password_reset_tokens** - Token reset password

---

## Tabel Detail

### 1. users

Tabel utama untuk menyimpan data pengguna/anggota sistem.

| Nama Field | Tipe Data | Panjang | Keterangan |
|------------|-----------|---------|------------|
| id | BIGINT UNSIGNED | - | Primary key, auto increment |
| name | STRING | 255 | Nama lengkap pengguna |
| email | STRING | 255 | Email pengguna, unique |
| role | STRING | 255 | Peran pengguna (siswa/guru/staff/admin) |
| email_verified_at | TIMESTAMP | - | Waktu verifikasi email, nullable |
| password | STRING | 255 | Password terenkripsi |
| remember_token | STRING | 100 | Token untuk "remember me" |
| custom_fields | JSON | - | Custom fields tambahan, nullable |
| created_at | TIMESTAMP | - | Waktu dibuat |
| updated_at | TIMESTAMP | - | Waktu diupdate |
| deleted_at | TIMESTAMP | - | Soft delete, nullable |
| created_by | BIGINT UNSIGNED | - | ID user yang membuat |
| updated_by | BIGINT UNSIGNED | - | ID user yang mengupdate |
| deleted_by | BIGINT UNSIGNED | - | ID user yang menghapus |

**Indexes:**
- PRIMARY: `id`
- UNIQUE: `email`

---

### 2. user_details

Tabel tambahan untuk menyimpan data detail anggota perpustakaan.

| Nama Field | Tipe Data | Panjang | Keterangan |
|------------|-----------|---------|------------|
| id | BIGINT UNSIGNED | - | Primary key, auto increment |
| user_id | BIGINT UNSIGNED | - | Foreign key ke tabel users, on delete cascade |
| nik | STRING | 255 | Nomor Induk Kependudukan (16 digit), nullable |
| nis | STRING | 255 | Nomor Induk Siswa, nullable |
| nisn | STRING | 255 | Nomor Induk Siswa Nasional (10 digit), nullable |
| class | STRING | 255 | Kelas (contoh: 12A, 11B), nullable |
| address | TEXT | - | Alamat lengkap, nullable |
| phone_number | STRING | 255 | Nomor telepon/WhatsApp, nullable |
| birth_date | DATE | - | Tanggal lahir, nullable |
| birth_place | STRING | 255 | Tempat lahir, nullable |
| gender | ENUM | - | Jenis kelamin (male/female), nullable |
| religion | STRING | 255 | Agama, nullable |
| join_date | DATE | - | Tanggal bergabung, nullable |
| membership_status | ENUM | - | Status keanggotaan (active/suspended/expired/pending), default: active |
| profile_photo | STRING | 255 | Path foto profil, nullable |
| barcode | STRING | 255 | QR Code untuk identifikasi anggota, nullable |
| barcode_image | STRING | 255 | Path file QR Code, nullable |
| created_at | TIMESTAMP | - | Waktu dibuat |
| updated_at | TIMESTAMP | - | Waktu diupdate |

**Indexes:**
- PRIMARY: `id`
- INDEX: `user_id`
- INDEX: `nis`
- INDEX: `nisn`
- INDEX: `membership_status`
- INDEX: `user_id`, `membership_status`

---

### 3. books

Tabel untuk menyimpan data buku perpustakaan.

| Nama Field | Tipe Data | Panjang | Keterangan |
|------------|-----------|---------|------------|
| id | BIGINT UNSIGNED | - | Primary key, auto increment |
| title | STRING | 255 | Judul buku |
| image | STRING | 255 | Path cover buku |
| category_id | BIGINT UNSIGNED | - | Foreign key ke tabel categories, on delete cascade |
| isbn | STRING | 255 | Nomor ISBN buku |
| author | STRING | 255 | Penulis buku |
| year_published | YEAR | - | Tahun terbit |
| publisher | STRING | 255 | Penerbit |
| synopsis | LONGTEXT | - | Sinopsis buku |
| book_count | INTEGER | - | Jumlah copy buku tersedia |
| bookshelf | STRING | 255 | Lokasi rak buku, nullable |
| source | STRING | 255 | Sumber buku (beli/hadiah/dll), nullable |
| price | STRING | 255 | Harga buku, nullable |
| type | ENUM | - | Tipe buku (fiction/non-fiction/reference/textbook/journal/other) |
| barcode | STRING | 255 | QR Code untuk buku, nullable |
| barcode_image | STRING | 255 | Path file QR Code, nullable |
| created_at | TIMESTAMP | - | Waktu dibuat |
| updated_at | TIMESTAMP | - | Waktu diupdate |
| deleted_at | TIMESTAMP | - | Soft delete, nullable |
| created_by | BIGINT UNSIGNED | - | ID user yang membuat |
| updated_by | BIGINT UNSIGNED | - | ID user yang mengupdate |
| deleted_by | BIGINT UNSIGNED | - | ID user yang menghapus |

**Indexes:**
- PRIMARY: `id`
- FOREIGN: `category_id` → `categories(id)`

---

### 4. categories

Tabel untuk menyimpan kategori buku.

| Nama Field | Tipe Data | Panjang | Keterangan |
|------------|-----------|---------|------------|
| id | BIGINT UNSIGNED | - | Primary key, auto increment |
| name | STRING | 255 | Nama kategori |
| slug | STRING | 255 | Slug URL kategori |
| created_at | TIMESTAMP | - | Waktu dibuat |
| updated_at | TIMESTAMP | - | Waktu diupdate |
| deleted_at | TIMESTAMP | - | Soft delete, nullable |
| created_by | BIGINT UNSIGNED | - | ID user yang membuat |
| updated_by | BIGINT UNSIGNED | - | ID user yang mengupdate |
| deleted_by | BIGINT UNSIGNED | - | ID user yang menghapus |

**Indexes:**
- PRIMARY: `id`

---

### 5. transactions

Tabel untuk menyimpan transaksi peminjaman buku.

| Nama Field | Tipe Data | Panjang | Keterangan |
|------------|-----------|---------|------------|
| id | BIGINT UNSIGNED | - | Primary key, auto increment |
| code | STRING | 255 | Kode transaksi unik |
| borrow_date | DATE | - | Tanggal peminjaman, nullable |
| return_date | DATE | - | Tanggal pengembalian aktual, nullable |
| due_date | DATE | - | Tanggal jatuh tempo, nullable |
| book_id | BIGINT UNSIGNED | - | Foreign key ke tabel books, on delete cascade |
| user_id | BIGINT UNSIGNED | - | Foreign key ke tabel users, on delete cascade |
| status_id | BIGINT UNSIGNED | - | Foreign key ke tabel statuses, on delete cascade |
| penalty_total | STRING | 255 | Total denda keterlambatan, nullable |
| notes | TEXT | - | Catatan tambahan, nullable |
| created_at | TIMESTAMP | - | Waktu dibuat |
| updated_at | TIMESTAMP | - | Waktu diupdate |
| deleted_at | TIMESTAMP | - | Soft delete, nullable |
| created_by | BIGINT UNSIGNED | - | ID user yang membuat |
| updated_by | BIGINT UNSIGNED | - | ID user yang mengupdate |
| deleted_by | BIGINT UNSIGNED | - | ID user yang menghapus |

**Indexes:**
- PRIMARY: `id`
- FOREIGN: `book_id` → `books(id)`
- FOREIGN: `user_id` → `users(id)`
- FOREIGN: `status_id` → `statuses(id)`

---

### 6. statuses

Tabel untuk menyimpan status transaksi peminjaman.

| Nama Field | Tipe Data | Panjang | Keterangan |
|------------|-----------|---------|------------|
| id | BIGINT UNSIGNED | - | Primary key, auto increment |
| name | STRING | 255 | Nama status (Dipinjam, Dikembalikan, Terlambat, dll) |
| amount | STRING | 255 | Jumlah denda terkait status, nullable |
| created_at | TIMESTAMP | - | Waktu dibuat |
| updated_at | TIMESTAMP | - | Waktu diupdate |
| deleted_at | TIMESTAMP | - | Soft delete, nullable |
| created_by | BIGINT UNSIGNED | - | ID user yang membuat |
| updated_by | BIGINT UNSIGNED | - | ID user yang mengupdate |
| deleted_by | BIGINT UNSIGNED | - | ID user yang menghapus |

**Indexes:**
- PRIMARY: `id`

---

### 7. bookmarks

Tabel untuk menyimpan bookmark buku oleh anggota.

| Nama Field | Tipe Data | Panjang | Keterangan |
|------------|-----------|---------|------------|
| id | BIGINT UNSIGNED | - | Primary key, auto increment |
| user_id | BIGINT UNSIGNED | - | Foreign key ke tabel users, on delete cascade |
| book_id | BIGINT UNSIGNED | - | Foreign key ke tabel books, on delete cascade |
| created_at | TIMESTAMP | - | Waktu dibuat |
| updated_at | TIMESTAMP | - | Waktu diupdate |

**Indexes:**
- PRIMARY: `id`
- UNIQUE: `user_id`, `book_id`
- FOREIGN: `user_id` → `users(id)`
- FOREIGN: `book_id` → `books(id)`

---

### 8. settings

Tabel untuk menyimpan konfigurasi sistem perpustakaan.

| Nama Field | Tipe Data | Panjang | Keterangan |
|------------|-----------|---------|------------|
| id | BIGINT UNSIGNED | - | Primary key, auto increment |
| name | STRING | 255 | Nama perpustakaan |
| logo | STRING | 255 | Path logo perpustakaan, nullable |
| address | STRING | 255 | Alamat perpustakaan, nullable |
| phone | STRING | 255 | Nomor telepon, nullable |
| limit_day | STRING | 255 | Batas hari peminjaman (default: 7) |
| max_borrow | INTEGER | - | Maksimal buku yang dapat dipinjam (default: 3) |
| created_at | TIMESTAMP | - | Waktu dibuat |
| updated_at | TIMESTAMP | - | Waktu diupdate |
| deleted_at | TIMESTAMP | - | Soft delete, nullable |
| created_by | BIGINT UNSIGNED | - | ID user yang membuat |
| updated_by | BIGINT UNSIGNED | - | ID user yang mengupdate |
| deleted_by | BIGINT UNSIGNED | - | ID user yang menghapus |

**Indexes:**
- PRIMARY: `id`

---

### 9. permissions

Tabel untuk menyimpan hak akses (Spatie Permission).

| Nama Field | Tipe Data | Panjang | Keterangan |
|------------|-----------|---------|------------|
| id | BIGINT UNSIGNED | - | Primary key, auto increment |
| name | STRING | 255 | Nama permission |
| guard_name | STRING | 255 | Guard yang digunakan (web/api) |
| created_at | TIMESTAMP | - | Waktu dibuat |
| updated_at | TIMESTAMP | - | Waktu diupdate |

**Indexes:**
- PRIMARY: `id`
- UNIQUE: `name`, `guard_name`

---

### 10. roles

Tabel untuk menyimpan peran pengguna (Spatie Permission).

| Nama Field | Tipe Data | Panjang | Keterangan |
|------------|-----------|---------|------------|
| id | BIGINT UNSIGNED | - | Primary key, auto increment |
| name | STRING | 255 | Nama role |
| guard_name | STRING | 255 | Guard yang digunakan (web/api) |
| created_at | TIMESTAMP | - | Waktu dibuat |
| updated_at | TIMESTAMP | - | Waktu diupdate |

**Indexes:**
- PRIMARY: `id`
- UNIQUE: `name`, `guard_name`

---

### 11. model_has_permissions

Tabel pivot untuk relasi permission dengan model (Spatie Permission).

| Nama Field | Tipe Data | Panjang | Keterangan |
|------------|-----------|---------|------------|
| permission_id | BIGINT UNSIGNED | - | Foreign key ke tabel permissions |
| model_type | STRING | 255 | Tipe model (App\Models\User) |
| model_id | BIGINT UNSIGNED | - | ID dari model |

**Indexes:**
- PRIMARY: `permission_id`, `model_id`, `model_type`
- INDEX: `model_id`, `model_type`
- FOREIGN: `permission_id` → `permissions(id)` ON DELETE CASCADE

---

### 12. model_has_roles

Tabel pivot untuk relasi role dengan model (Spatie Permission).

| Nama Field | Tipe Data | Panjang | Keterangan |
|------------|-----------|---------|------------|
| role_id | BIGINT UNSIGNED | - | Foreign key ke tabel roles |
| model_type | STRING | 255 | Tipe model (App\Models\User) |
| model_id | BIGINT UNSIGNED | - | ID dari model |

**Indexes:**
- PRIMARY: `role_id`, `model_id`, `model_type`
- INDEX: `model_id`, `model_type`
- FOREIGN: `role_id` → `roles(id)` ON DELETE CASCADE

---

### 13. role_has_permissions

Tabel pivot untuk relasi role dengan permission (Spatie Permission).

| Nama Field | Tipe Data | Panjang | Keterangan |
|------------|-----------|---------|------------|
| permission_id | BIGINT UNSIGNED | - | Foreign key ke tabel permissions |
| role_id | BIGINT UNSIGNED | - | Foreign key ke tabel roles |

**Indexes:**
- PRIMARY: `permission_id`, `role_id`
- FOREIGN: `permission_id` → `permissions(id)` ON DELETE CASCADE
- FOREIGN: `role_id` → `roles(id)` ON DELETE CASCADE

---

### 14. notifications

Tabel untuk menyimpan notifikasi sistem.

| Nama Field | Tipe Data | Panjang | Keterangan |
|------------|-----------|---------|------------|
| id | UUID | - | Primary key, UUID |
| type | STRING | 255 | Tipe/kelas notifikasi |
| notifiable_id | BIGINT UNSIGNED | - | ID model yang menerima notifikasi |
| notifiable_type | STRING | 255 | Tipe model yang menerima notifikasi |
| data | TEXT | - | Data notifikasi (JSON) |
| read_at | TIMESTAMP | - | Waktu dibaca, nullable |
| created_at | TIMESTAMP | - | Waktu dibuat |
| updated_at | TIMESTAMP | - | Waktu diupdate |

**Indexes:**
- PRIMARY: `id`
- INDEX: `notifiable_id`, `notifiable_type`

---

### 15. cache

Tabel untuk menyimpan cache sistem.

| Nama Field | Tipe Data | Panjang | Keterangan |
|------------|-----------|---------|------------|
| key | STRING | 255 | Primary key, kunci cache |
| value | MEDIUMTEXT | - | Nilai cache (terserialized) |
| expiration | INTEGER | - | Waktu kadaluarsa (Unix timestamp) |

**Indexes:**
- PRIMARY: `key`

---

### 16. cache_locks

Tabel untuk menyimpan lock cache.

| Nama Field | Tipe Data | Panjang | Keterangan |
|------------|-----------|---------|------------|
| key | STRING | 255 | Primary key, kunci lock |
| owner | STRING | 255 | Pemilik lock |
| expiration | INTEGER | - | Waktu kadaluarsa (Unix timestamp) |

**Indexes:**
- PRIMARY: `key`

---

### 17. jobs

Tabel untuk menyimpan queue jobs.

| Nama Field | Tipe Data | Panjang | Keterangan |
|------------|-----------|---------|------------|
| id | BIGINT UNSIGNED | - | Primary key, auto increment |
| queue | STRING | 255 | Nama queue |
| payload | LONGTEXT | - | Data job (JSON) |
| attempts | TINYINT UNSIGNED | - | Jumlah percobaan |
| reserved_at | INTEGER UNSIGNED | - | Waktu reservation, nullable |
| available_at | INTEGER UNSIGNED | - | Waktu tersedia untuk dieksekusi |
| created_at | INTEGER UNSIGNED | - | Waktu dibuat |

**Indexes:**
- PRIMARY: `id`
- INDEX: `queue`

---

### 18. job_batches

Tabel untuk menyimpan batch jobs.

| Nama Field | Tipe Data | Panjang | Keterangan |
|------------|-----------|---------|------------|
| id | STRING | 255 | Primary key, UUID batch |
| name | STRING | 255 | Nama batch |
| total_jobs | INTEGER | - | Total jobs dalam batch |
| pending_jobs | INTEGER | - | Jobs yang pending |
| failed_jobs | INTEGER | - | Jobs yang gagal |
| failed_job_ids | LONGTEXT | - | IDs dari jobs yang gagal (JSON) |
| options | MEDIUMTEXT | - | Opsi batch, nullable |
| cancelled_at | INTEGER | - | Waktu pembatalan, nullable |
| created_at | INTEGER | - | Waktu dibuat |
| finished_at | INTEGER | - | Waktu selesai, nullable |

**Indexes:**
- PRIMARY: `id`

---

### 19. failed_jobs

Tabel untuk menyimpan jobs yang gagal.

| Nama Field | Tipe Data | Panjang | Keterangan |
|------------|-----------|---------|------------|
| id | BIGINT UNSIGNED | - | Primary key, auto increment |
| uuid | STRING | 255 | UUID job, unique |
| connection | TEXT | - | Nama koneksi queue |
| queue | TEXT | - | Nama queue |
| payload | LONGTEXT | - | Data job (JSON) |
| exception | LONGTEXT | - | Exception/error message |
| failed_at | TIMESTAMP | - | Waktu gagal |

**Indexes:**
- PRIMARY: `id`
- UNIQUE: `uuid`

---

### 20. sessions

Tabel untuk menyimpan sesi pengguna.

| Nama Field | Tipe Data | Panjang | Keterangan |
|------------|-----------|---------|------------|
| id | STRING | 255 | Primary key, ID sesi |
| user_id | BIGINT UNSIGNED | - | Foreign key ke users, nullable |
| ip_address | STRING | 45 | IP address pengguna, nullable |
| user_agent | TEXT | - | Browser user agent, nullable |
| payload | LONGTEXT | - | Data sesi (terencrypted) |
| last_activity | INTEGER | - | Timestamp aktivitas terakhir |

**Indexes:**
- PRIMARY: `id`
- INDEX: `user_id`
- INDEX: `last_activity`

---

### 21. password_reset_tokens

Tabel untuk menyimpan token reset password.

| Nama Field | Tipe Data | Panjang | Keterangan |
|------------|-----------|---------|------------|
| email | STRING | 255 | Primary key, email pengguna |
| token | STRING | 255 | Token reset password |
| created_at | TIMESTAMP | - | Waktu pembuatan token, nullable |

**Indexes:**
- PRIMARY: `email`

---

## Entity Relationship Diagram (ERD)

```
┌─────────────┐         ┌──────────────────┐         ┌─────────────┐
│   users     │────────1│  user_details    │         │   roles     │
│             │         │                  │         │             │
│ id (PK)     │         │ id (PK)          │         │ id (PK)     │
│ email       │         │ user_id (FK)     │         │ name        │
│ password    │         │ nik              │         └──────┬───────┘
│ role        │         │ nis              │                │
└──────┬──────┘         │ nisn             │         ┌──────┴───────┐
       │                │ class            │         │model_has_   │
       │                │ membership_status│         │   roles      │
       │                └──────────────────┘         └─────────────┘
       │
       │                                ┌──────────────────┐
       │                                │   transactions   │
       │                ┌───────────────│                  │
       │                │               │ id (PK)          │
       ▼                ▼               │ code             │
┌─────────────┐  ┌──────────────┐      │ borrow_date      │
│   books     │  │   bookmarks  │      │ return_date      │
│             │  │              │      │ due_date         │
│ id (PK)     │  │ id (PK)      │      │ book_id (FK)     │
│ title       │  │ user_id (FK) │──────┤ user_id (FK)──────┤
│ category_id │  │ book_id (FK) │──────┤ status_id (FK)   │
│ barcode     │  └──────────────┘      └──────────────────┘
└──────┬──────┘                                  │
       │                                         │
       │                ┌──────────────────┐      │
       └───────────────│   categories     │      │
                        │                  │      │
                        │ id (PK)          │      │
                        │ name             │      │
                        └──────────────────┘      │
                                                  │
                        ┌──────────────────┐      │
                        │    statuses      │◄─────┘
                        │                  │
                        │ id (PK)          │
                        │ name             │
                        │ amount           │
                        └──────────────────┘
```

---

## Konvensi Penamaan

### Userstamps
Sistem menggunakan package `wildside/userstamps` untuk tracking pembuatan dan pengubahan data. Fields berikut ditambahkan secara otomatis:

- `created_by` - ID user yang membuat record
- `updated_by` - ID user yang terakhir mengupdate record
- `deleted_by` - ID user yang menghapus record (soft delete)

### Soft Deletes
Sebagian besar tabel menggunakan soft deletes dengan field:
- `deleted_at` - Timestamp ketika record dihapus

### Default Timestamps
Semua tabel memiliki:
- `created_at` - Timestamp pembuatan
- `updated_at` - Timestamp pengubahan terakhir

---

## Tipe Data ENUM

### books.type
- `fiction` - Fiksi
- `non-fiction` - Non-fiksi
- `reference` - Referensi
- `textbook` - Buku teks
- `journal` - Jurnal
- `other` - Lainnya

### user_details.membership_status
- `active` - Aktif
- `suspended` - Ditangguhkan
- `expired` - Kadaluarsa
- `pending` - Menunggu konfirmasi

### user_details.gender
- `male` - Laki-laki
- `female` - Perempuan

---

## Catatan Penting

1. **Userstamps**: Semua tabel utama menggunakan package `userstamps` untuk tracking siapa yang membuat/mengubah/menghapus data.

2. **Soft Deletes**: Data tidak dihapus permanen dari database, hanya ditandai dengan timestamp di `deleted_at`.

3. **Cascade Delete**: Beberapa foreign key menggunakan `ON DELETE CASCADE` untuk menghapus related data secara otomatis.

4. **QR/Barcode Code**: Sistem menggunakan QR Code untuk identifikasi buku dan anggota.

5. **Permission System**: Menggunakan Spatie Permission package untuk manajemen hak akses berbasis role dan permission.

---

## Referensi

- Laravel 12 Documentation: https://laravel.com/docs/12.x
- Spatie Permission: https://spatie.be/docs/laravel-permission
- Userstamps Package: https://github.com/wildside/userstamps
