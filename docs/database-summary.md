# Ringkasan Struktur Database - Sistem Perpustakaan

## Gambaran Umum

Sistem Perpustakaan ini adalah aplikasi manajemen perpustakaan berbasis web yang dibangun dengan Laravel 12. Sistem ini mengelola data buku, anggota, transaksi peminjaman, dan konfigurasi sistem perpustakaan.

---

## Arsitektur Database

### Kategori Tabel

```
┌─────────────────────────────────────────────────────────┐
│              SISTEM PERPUSTAKAAN                         │
├─────────────────────────────────────────────────────────┤
│                                                          │
│  ┌──────────────┐  ┌──────────────┐  ┌──────────────┐ │
│  │   Master     │  │ Transaksi    │  │   Sistem     │ │
│  │   Data       │  │              │  │              │ │
│  ├──────────────┤  ├──────────────┤  ├──────────────┤ │
│  │ users        │  │ transactions │  │ permissions  │ │
│  │ user_details │  │ bookmarks    │  │ roles        │ │
│  │ books        │  │              │  │ settings     │ │
│  │ categories   │  │              │  │ notifications│ │
│  │ statuses     │  │              │  │ cache        │ │
│  └──────────────┘  └──────────────┘  │ jobs         │ │
│                                      │ sessions     │ │
│                                      └──────────────┘ │
└─────────────────────────────────────────────────────────┘
```

---

## Hubungan Antar Tabel (Relationships)

### 1. users - user_details (One-to-One)
```
users (1) ─────── (1) user_details
├─ id                 ├─ user_id
├─ name              └─ detail lengkap
└─ email
```
Setiap user memiliki satu detail tambahan yang berisi informasi lengkap anggota.

---

### 2. books - categories (Many-to-One)
```
categories (1) ─────── (N) books
├─ id                 ├─ category_id
├─ name              └─ book data
└─ slug
```
Setiap buku belongs to satu kategori. Satu kategori memiliki banyak buku.

---

### 3. books - transactions (One-to-Many)
```
books (1) ─────── (N) transactions
├─ id                 ├─ book_id
├─ title             └─ transaction data
└─ isbn
```
Satu buku dapat memiliki banyak transaksi peminjaman.

---

### 4. users - transactions (One-to-Many)
```
users (1) ─────── (N) transactions
├─ id                 ├─ user_id
├─ name             └─ transaction data
└─ email
```
Satu user dapat melakukan banyak transaksi peminjaman.

---

### 5. statuses - transactions (One-to-Many)
```
statuses (1) ─────── (N) transactions
├─ id                 ├─ status_id
├─ name             └─ status (Dipinjam, dll)
└─ amount
```
Setiap transaksi memiliki satu status. Status menentukan kondisi peminjaman.

---

### 6. users - bookmarks (Many-to-Many dengan books)
```
users (N) ─────── (N) books
        │          │
        └── bookmarks ─┘
           ├─ user_id
           └─ book_id
```
User dapat mem-bookmark banyak buku, dan buku dapat di-bookmark oleh banyak user.

---

### 7. users - roles - permissions (Many-to-Many)
```
users (N) ────── model_has_roles ────── (N) roles
                                                   │
                                                   │ role_has_permissions
                                                   │
                                              (N) permissions

users (N) ── model_has_permissions ──── (N) permissions
```
Sistem permission menggunakan Spatie Permission package dengan relasi many-to-many.

---

## Alur Peminjaman Buku

```
┌──────────┐
│  Anggota │
└─────┬────┘
      │ 1. Login
      ▼
┌──────────────────┐
│  Katalog Buku    │
│  (Browse Books)  │
└─────┬────────────┘
      │ 2. Pilih Buku
      ▼
┌──────────────────┐
│  Bookmark?       │───Ya──► Simpan ke Bookmarks
└─────┬────────────┘
      │ Tidak
      ▼
┌──────────────────┐
│  Pinjam Buku     │
│  (Borrow)        │
└─────┬────────────┘
      │ 3. Create Transaction
      ▼
┌─────────────────────────────┐
│  transactions               │
│  ├─ code: AUTO              │
│  ├─ borrow_date: NOW()      │
│  ├─ due_date: NOW() + 7 hari│
│  ├─ status_id: 1 (Dipinjam) │
│  └─ book_count--            │
└─────────────────────────────┘
      │
      │ 4. Kembalikan Buku
      ▼
┌─────────────────────────────┐
│  Update Transaction         │
│  ├─ return_date: NOW()      │
│  ├─ status_id: 2 (Dikembalikan)│
│  └─ book_count++            │
└─────────────────────────────┘
      │
      ▼ (Terlambat?)
┌─────────────────────────────┐
│  Hitung Denda               │
│  ├─ penalty_total =         │
│  │   (return_date - due_date)│
│  │   × denda_per_hari       │
│  └─ status_id: 3 (Terlambat)│
└─────────────────────────────┘
```

---

## Fitur Utama Berdasarkan Database

### 1. Manajemen Anggota
- **users**: Login, authentication
- **user_details**: Profil lengkap anggota
  - Data pribadi (NIK, NIS, NISN)
  - Data kontak (alamat, telepon)
  - Keanggotaan (status, join_date)
  - QR Code untuk identifikasi

### 2. Manajemen Buku
- **books**: Data buku lengkap
  - Informasi dasar (judul, penulis, penerbit)
  - Klasifikasi (kategori, tipe)
  - Inventaris (book_count, bookshelf, source)
  - QR Code untuk scanning

### 3. Sistem Peminjaman
- **transactions**: Catatan peminjaman
  - Tracking tanggal (pinjam, kembali, jatuh tempo)
  - Status transaksi
  - Perhitungan denda

### 4. Bookmark Buku
- **bookmarks**: Simpan buku favorit
  - Relasi many-to-many user-books

### 5. Konfigurasi Sistem
- **settings**: Pengaturan perpustakaan
  - Nama dan logo
  - Kontak dan alamat
  - Aturan peminjaman (limit_day, max_borrow)

### 6. Hak Akses
- **permissions, roles**: Spatie Permission
  - Role-based access control
  - Permission per resource

---

## Statistik yang Dapat Dihasilkan

Dari struktur database ini, dapat dihasilkan berbagai statistik:

### Statistik Buku
```sql
-- Total buku per kategori
SELECT c.name, COUNT(*) as total
FROM books b
JOIN categories c ON b.category_id = c.id
GROUP BY c.id;

-- Buku paling banyak dipinjam
SELECT b.title, COUNT(*) as borrow_count
FROM transactions t
JOIN books b ON t.book_id = b.id
GROUP BY b.id
ORDER BY borrow_count DESC;

-- Buku tidak pernah dipinjam
SELECT b.title
FROM books b
LEFT JOIN transactions t ON b.id = t.book_id
WHERE t.id IS NULL;
```

### Statistik Anggota
```sql
-- Anggota paling aktif
SELECT u.name, COUNT(*) as borrow_count
FROM transactions t
JOIN users u ON t.user_id = u.id
GROUP BY u.id
ORDER BY borrow_count DESC;

-- Status keanggotaan
SELECT membership_status, COUNT(*) as total
FROM user_details
GROUP BY membership_status;
```

### Statistik Peminjaman
```sql
-- Transaksi per status
SELECT s.name, COUNT(*) as total
FROM transactions t
JOIN statuses s ON t.status_id = s.id
GROUP BY s.id;

-- Buku terlambat
SELECT COUNT(*) as total_late
FROM transactions
WHERE return_date > due_date;
```

---

## Keamanan Data

### Userstamps Tracking
Setiap perubahan data tercatat:
- `created_by`: Siapa yang membuat
- `updated_by`: Siapa yang terakhir mengubah
- `deleted_by`: Siapa yang menghapus

### Soft Deletes
Data tidak benar-benar dihapus:
- `deleted_at`: Timestamp penghapusan
- Data masih dapat direstore

### Foreign Key Constraints
Mencegah data orphan:
- `ON DELETE CASCADE`: Hapus related data
- `constrained()`: Mencegah delete jika ada referensi

---

## Rekomendasi Index

Untuk performa optimal, pastikan index berikut ada:

### High Priority
```sql
-- transactions
CREATE INDEX idx_transactions_user_status ON transactions(user_id, status_id);
CREATE INDEX idx_transactions_book_status ON transactions(book_id, status_id);
CREATE INDEX idx_transactions_dates ON transactions(borrow_date, return_date);

-- books
CREATE INDEX idx_books_category ON books(category_id);
CREATE INDEX idx_books_type ON books(type);

-- user_details
CREATE INDEX idx_user_details_status ON user_details(membership_status);
CREATE INDEX idx_user_details_nis ON user_details(nis);
```

---

## Backup & Maintenance

### Daily Backup
- Backup seluruh database
- Simpan minimal 7 hari terakhir

### Weekly Maintenance
- Optimasi tabel: `OPTIMIZE TABLE`
- Analisis query lambat

### Monthly Cleanup
- Bersihkan `cache` yang kadaluarsa
- Arsip `sessions` lama
- Review `failed_jobs`

---

## Referensi Query

### Mendapatkan Buku yang Sedang Dipinjam
```php
$book = Book::with(['transactions' => function($query) {
    $query->where('status_id', 1) // Status: Dipinjam
          ->where('user_id', auth()->id());
}])->find($bookId);
```

### Cek Status Keanggotaan
```php
$user = User::with('userDetail')->find($userId);
if ($user->userDetail->membership_status === 'suspended') {
    // Tolak peminjaman
}
```

### Hitung Denda Keterlambatan
```php
$transaction = Transaction::find($id);
if ($transaction->return_date > $transaction->due_date) {
    $daysLate = $transaction->return_date->diffInDays($transaction->due_date);
    $penalty = $daysLate * $dailyPenaltyRate;
}
```

---

## Dokumentasi Terkait

- [Database Lengkap](./database.md) - Detail lengkap setiap tabel
- [Technical Documentation](./technical.md) - Dokumentasi teknis sistem
- [User Guide](./user-guide.md) - Panduan penggunaan sistem
- [Getting Started](./getting-started.md) - Memulai dengan sistem

---

*Documentation generated: 2026-01-15*
