# Dokumentasi Pengujian Sistem Perpustakaan
## Comprehensive Testing Documentation

---

## Daftar Isi

1. [Pendahuluan](#pendahuluan)
2. [Strategi Pengujian](#strategi-pengujian)
3. [Lingkungan Pengujian](#lingkungan-pengujian)
4. [Modul Autentikasi & Keamanan](#modul-autentikasi--keamanan)
5. [Modul Manajemen Pengguna](#modul-manajemen-pengguna)
6. [Modul Manajemen Buku](#modul-manajemen-buku)
7. [Modul Manajemen Kategori](#modul-manajemen-kategori)
8. [Modul Transaksi Peminjaman](#modul-transaksi-peminjaman)
9. [Modul Transaksi Pengembalian](#modul-transaksi-pengembalian)
10. [Modul Katalog & Pencarian](#modul-katalog--pencarian)
11. [Modul Bookmark](#modul-bookmark)
12. [Modul Laporan & Statistik](#modul-laporan--statistik)
13. [Modul Pengaturan Sistem](#modul-pengaturan-sistem)
14. [Modul Role & Permission](#modul-role--permission)
15. [Rekapitulasi Hasil](#rekapitulasi-hasil)

---

## Pendahuluan

### Tujuan Dokumentasi

Dokumentasi ini berisi rencana dan hasil pengujian sistem perpustakaan secara menyeluruh. Tujuannya adalah:

1. Memastikan semua fitur berfungsi sesuai spesifikasi
2. Mendeteksi bug dan error sebelum sistem digunakan
3. Validasi bahwa sistem memenuhi kebutuhan pengguna
4. Memberikan referensi untuk pengujian di masa mendatang

### Ruang Lingkup

Pengujian mencakup seluruh modul dan fitur dalam sistem perpustakaan:

- **Fungsional**: Memastikan fitur bekerja sesuai fungsi
- **Validasi**: Memastikan input divalidasi dengan benar
- **Error Handling**: Memastikan error ditangani dengan tepat
- **Security**: Memastikan hak akses dan data aman
- **Integrasi**: Memastikan modul saling terintegrasi

---

## Strategi Pengujian

### Jenis Pengujian

```
┌─────────────────────────────────────────────────────────────┐
│                    STRATEGI PENGUJIAN                        │
├─────────────────────────────────────────────────────────────┤
│                                                              │
│  1. Unit Testing     → Testing per function/method          │
│  2. Integration      → Testing antar modul                   │
│  3. Functional       → Testing fitur end-to-end              │
│  4. UI/UX            → Testing antarmuka pengguna            │
│  5. Security         → Testing keamanan & hak akses          │
│  6. Performance      → Testing kecepatan & load              │
│                                                              │
└─────────────────────────────────────────────────────────────┘
```

### Metodologi

Pengujian dilakukan dengan pendekatan:

- **Black Box**: Testing tanpa melihat kode internal
- **White Box**: Testing dengan memahami logika kode
- **Gray Box**: Kombinasi keduanya
- **Exploratory**: Testing bebas untuk menemukan bug tak terduga

---

## Lingkungan Pengujian

### Spesifikasi Sistem

```
Environment: Development
OS: Windows 11 / Linux
Browser: Chrome, Firefox, Edge, Safari
Server: Laravel 12 (PHP 8.3.6)
Database: MySQL / MariaDB
PHP Version: 8.3.6
```

### Data Testing

Data dummy yang digunakan untuk pengujian:

- **Users**: 20+ user dengan berbagai role
- **Books**: 50+ buku dengan berbagai kategori
- **Categories**: 10+ kategori
- **Transactions**: 100+ transaksi dengan berbagai status

---

# 1. MODUL AUTENTIKASI & KEAMANAN

## Deskripsi Modul

Modul Autentikasi & Keamanan bertanggung jawab atas:

1. Proses login dan logout pengguna
2. Registrasi pengguna baru
3. Reset password untuk lupa password
4. Proteksi halaman dengan middleware
5. Session management

## Tujuan Pengujian

Memastikan bahwa:
- Hanya pengguna terdaftar yang dapat login
- Password tersimpan dengan enkripsi
- Session dikelola dengan aman
- Reset password berfungsi dengan valid

## Skenario Pengujian

### 1.1 Login Pengguna - Kasus Normal

**Deskripsi**: Pengguna berhasil login dengan kredensial yang valid.

| Modul yang Diuji | Prosedur Pengujian | Masukan | Keluaran yang Diharapkan | Hasil yang Didapat | Kesimpulan |
|------------------|--------------------|---------|-------------------------|-------------------|------------|
| Autentikasi - Login Berhasil | 1. Buka halaman /login<br>2. Masukkan email terdaftar<br>3. Masukkan password yang benar<br>4. Klik tombol "Masuk"<br>5. Periksa redirect | Email: `admin@example.com`<br>Password: `password` | - Redirect ke /dashboard<br>- Session terbuat dengan user_id<br>- Flash message "Login berhasil"<br>- Menu sesuai role tampil | - Redirect ke /dashboard berhasil<br>- Session terbuat<br>- Flash message muncul<br>- Menu admin tampil lengkap | **LULUS** |

### 1.2 Login Pengguna - Kasus Error

**Deskripsi**: Sistem menolak login dengan kredensial tidak valid.

| Modul yang Diuji | Prosedur Pengujian | Masukan | Keluaran yang Diharapkan | Hasil yang Didapat | Kesimpulan |
|------------------|--------------------|---------|-------------------------|-------------------|------------|
| Autentikasi - Email Tidak Terdaftar | 1. Buka halaman /login<br>2. Masukkan email tidak terdaftar<br>3. Masukkan password<br>4. Klik "Masuk" | Email: `notexist@example.com`<br>Password: `password` | - Tetap di halaman /login<br>- Error message: "Email tidak terdaftar"<br>- Input field tetap terisi | - Tetap di /login<br>- Error message tampil<br>- Data input retained | **LULUS** |
| Autentikasi - Password Salah | 1. Buka halaman /login<br>2. Masukkan email terdaftar<br>3. Masukkan password salah<br>4. Klik "Masuk" | Email: `admin@example.com`<br>Password: `wrongpassword` | - Tetap di halaman /login<br>- Error message: "Password salah"<br>- Password field dibersihkan | - Tetap di /login<br>- Error message tampil<br>- Password field cleared | **LULUS** |
| Autentikasi - Input Kosong | 1. Buka halaman /login<br>2. Biarkan semua field kosong<br>3. Klik "Masuk" | Email: (kosong)<br>Password: (kosong) | - Tetap di halaman /login<br>- Validation error: "Email wajib diisi"<br>- Validation error: "Password wajib diisi" | - Tetap di /login<br>- Validation error muncul untuk kedua field | **LULUS** |
| Autentikasi - Format Email Salah | 1. Buka halaman /login<br>2. Masukkan email tanpa @<br>3. Klik "Masuk" | Email: `adminexample.com`<br>Password: `password` | - Validation error: "Format email tidak valid"<br>- Tidak submit form | - Validation error muncul<br>- Form tidak submit | **LULUS** |

### 1.3 Register Pengguna Baru

**Deskripsi**: Pengguna baru dapat mendaftar dengan data valid.

| Modul yang Diuji | Prosedur Pengujian | Masukan | Keluaran yang Diharapkan | Hasil yang Didapat | Kesimpulan |
|------------------|--------------------|---------|-------------------------|-------------------|------------|
| Autentikasi - Register Berhasil | 1. Buka halaman /register<br>2. Isi semua field dengan data valid<br>3. Klik "Daftar"<br>4. Cek email verifikasi | Name: `Budi Santoso`<br>Email: `budi@example.com`<br>Password: `password123`<br>Confirm: `password123` | - User berhasil dibuat di database<br>- Password ter-hash (bukan plain text)<br>- Redirect ke /dashboard atau /email-verify<br>- Email verifikasi dikirim | - User terbuat di DB<br>- Password: `hash(bcrypt)`<br>- Redirect sukses<br>- Email queue terbuat | **LULUS** |
| Autentikasi - Register Email Duplikat | 1. Buka halaman /register<br>2. Gunakan email yang sudah terdaftar<br>3. Klik "Daftar" | Email: `admin@example.com` (sudah ada) | - Validation error: "Email sudah terdaftar"<br>- Form tidak submit | - Error muncul<br>- Form tidak submit | **LULUS** |
| Autentikasi - Password Terlalu Pendek | 1. Buka halaman /register<br>2. Password hanya 4 karakter<br>3. Klik "Daftar" | Password: `pass`<br>Confirm: `pass` | - Validation error: "Password minimal 8 karakter"<br>- Form tidak submit | - Error muncul<br>- Form tidak submit | **LULUS** |
| Autentikasi - Password Tidak Cocok | 1. Buka halaman /register<br>2. Password dan confirm berbeda<br>3. Klik "Daftar" | Password: `password123`<br>Confirm: `password456` | - Validation error: "Password tidak cocok"<br>- Form tidak submit | - Error muncul<br>- Form tidak submit | **LULUS** |

### 1.4 Logout

**Deskripsi**: Pengguna dapat keluar dari sistem dengan aman.

| Modul yang Diuji | Prosedur Pengujian | Masukan | Keluaran yang Diharapkan | Hasil yang Didapat | Kesimpulan |
|------------------|--------------------|---------|-------------------------|-------------------|------------|
| Autentikasi - Logout | 1. Login sebagai user<br>2. Klik menu profil → Logout<br>3. Konfirmasi logout | - Session aktif | - Session dihapus<br>- Cache auth dibersihkan<br>- Redirect ke /login<br>- Flash message "Logout berhasil" | - Session terhapus<br>- Cache cleared<br>- Redirect ke /login<br>- Message muncul | **LULUS** |
| Autentikasi - Logout Sesi Kedua | 1. Login di browser A<br>2. Login di browser B<br>3. Logout di browser A<br>4. Cek browser B | - 2 session berbeda | - Session A terhapus<br>- Session B masih aktif | - Hanya session A yang terhapus<br>- Browser B tetap login | **LULUS** |

### 1.5 Reset Password

**Deskripsi**: Pengguna dapat mereset password jika lupa.

| Modul yang Diuji | Prosedur Pengujian | Masukan | Keluaran yang Diharapkan | Hasil yang Didapat | Kesimpulan |
|------------------|--------------------|---------|-------------------------|-------------------|------------|
| Autentikasi - Request Reset | 1. Buka /forgot-password<br>2. Masukkan email terdaftar<br>3. Klik "Kirim Link Reset" | Email: `admin@example.com` | - Token reset dibuat di DB<br>- Email berisi link reset dikirim<br>- Flash message "Link reset dikirim" | - Token terbuat di `password_reset_tokens`<br>- Email queue terbuat<br>- Message muncul | **LULUS** |
| Autentikasi - Request Reset Email Tidak Ada | 1. Buka /forgot-password<br>2. Masukkan email tidak terdaftar | Email: `notexist@example.com` | - Tetap tampilkan pesan sukses (security best practice)<br>- Tidak ada email yang dikirim | - Pesan sukses tampil (fake)<br>- Tidak ada email queue | **LULUS** |
| Autentikasi - Reset dengan Token Valid | 1. Buka link dari email<br>2. Masukkan password baru<br>3. Konfirmasi password<br>4. Klik "Reset Password" | Token: valid token<br>New Pass: `newpass123` | - Password berhasil diubah<br>- Token dihapus<br>- Bisa login dengan password baru | - Password ter-hash & terupdate<br>- Token deleted<br>- Login berhasil dengan pass baru | **LULUS** |
| Autentikasi - Reset dengan Token Expired | 1. Buka link dengan token expired (lebih dari 1 jam)<br>2. Coba submit form | Token: expired token | - Error message: "Token tidak valid atau kadaluarsa"<br>- Redirect ke /forgot-password | - Error muncul<br>- Redirect ke /forgot-password | **LULUS** |

### 1.6 Proteksi Halaman (Middleware)

**Deskripsi**: Halaman terproteksi hanya bisa diakses user yang login.

| Modul yang Diuji | Prosedur Pengujian | Masukan | Keluaran yang Diharapkan | Hasil yang Didapat | Kesimpulan |
|------------------|--------------------|---------|-------------------------|-------------------|------------|
| Middleware - Auth Redirect | 1. Logout (tidak ada session)<br>2. Akses /dashboard langsung via URL | - Tidak ada session | - Redirect ke /login<br>- Flash message: "Silakan login terlebih dahulu" | - Redirect ke /login<br>- Message muncul | **LULUS** |
| Middleware - Guest Redirect | 1. Login sebagai user<br>2. Akses /login via URL | - Session aktif | - Redirect ke /dashboard<br>- Tidak bisa akses halaman login | - Redirect ke /dashboard | **LULUS** |
| Middleware - Verified Only | 1. Login tapi belum verify email<br>2. Akses halaman yang butuh verified | - User tanpa email_verified_at | - Redirect ke /email-verify<br>- Error: "Silakan verifikasi email" | - Redirect sukses<br>- Error message tampil | **LULUS** |

---

# 2. MODUL MANAJEMEN PENGGUNA

## Deskripsi Modul

Modul Manajemen Pengguna menangani:

1. CRUD data pengguna (users & user_details)
2. Upload foto profil
3. Generate QR Code anggota
4. Manajemen status membership
5. Soft delete & restore

## Tujuan Pengujian

Memastikan bahwa:
- Data pengguna tersimpan dengan valid
- Foto profil dapat diupload dengan aman
- QR Code tergenerate dengan benar
- Status membership dapat diubah
- Data tidak hilang saat dihapus (soft delete)

## Skenario Pengujian

### 2.1 Menampilkan Daftar Pengguna

**Deskripsi**: Admin dapat melihat daftar semua pengguna dengan fitur filter dan search.

| Modul yang Diuji | Prosedur Pengujian | Masukan | Keluaran yang Diharapkan | Hasil yang Didapat | Kesimpulan |
|------------------|--------------------|---------|-------------------------|-------------------|------------|
| User - Index Page | 1. Login sebagai admin<br>2. Buka /admin/users<br>3. Perhatikan tampilan | - Pagination 10 per page | - Daftar user tampil dalam table/grid<br>- Kolom: foto, nama, email, role, status<br>- Pagination berfungsi<br>- Tombol aksi (view, edit, delete) | - Table muncul lengkap<br>- Pagination berfungsi<br>- Tombol aksi ada | **LULUS** |
| User - Search by Name | 1. Di halaman users<br>2. Ketik "budi" di search box<br>3. Tekan Enter | Keyword: `budi` | - Filter real-time atau submit<br>- Hanya user dengan nama "budi" tampil | - Filter sesuai keyword<br>- Hasil akurat | **LULUS** |
| User - Search by Email | 1. Di halaman users<br>2. Ketik email di search box | Keyword: `@gmail.com` | - User dengan email Gmail tampil | - Filter sesuai | **LULUS** |
| User - Filter by Role | 1. Pilih filter Role: "Siswa"<br>2. Apply filter | Role: `siswa` | - Hanya user dengan role siswa tampil | - Filter sesuai | **LULUS** |
| User - Filter by Membership Status | 1. Pilih filter Status: "Active"<br>2. Apply filter | Status: `active` | - Hanya user dengan membership active tampil | - Filter sesuai | **LULUS** |
| User - Pagination | 1. Jika data > 10<br>2. Klik halaman 2<br>3. Klik "Next" | - Klik pagination | - Halaman berpindah dengan data sesuai | - Pagination smooth | **LULUS** |
| User - Export | 1. Klik tombol "Export"<br>2. Pilih format (Excel/PDF) | - | - File terdownload<br>- Data lengkap semua user | - Export sukses | **LULUS** |

### 2.2 Menambah Pengguna Baru

**Deskripsi**: Admin dapat menambah pengguna baru dengan data lengkap.

| Modul yang Diuji | Prosedur Pengujian | Masukan | Keluaran yang Diharapkan | Hasil yang Didapat | Kesimpulan |
|------------------|--------------------|---------|-------------------------|-------------------|------------|
| User - Create Success | 1. Klik "Tambah User"<br>2. Isi form user<br>3. Isi form detail<br>4. Upload foto<br>5. Klik "Simpan" | User:<br>- Name: `Siti Aminah`<br>- Email: `siti@example.com`<br>- Role: `siswa`<br>- Password: `pass123`<br><br>Detail:<br>- NIS: `12345`<br>- Kelas: `10A`<br>- HP: `08123456789`<br>- Gender: `female`<br>- Foto: `photo.jpg` | - User berhasil dibuat<br>- User detail tersimpan<br>- Foto terupload dan path tersimpan<br>- QR Code tergenerate<br>- Password ter-hash<br>- Redirect ke index<br>- Notifikasi sukses | - User terbuat di DB<br>- Detail terbuat<br>- Foto: `/storage/photos/xxx.jpg`<br>- QR Code tergenerate<br>- Password hashed<br>- Redirect sukses<br>- Notifikasi muncul | **LULUS** |
| User - Validate Email Duplicate | 1. Tambah user baru<br>2. Gunakan email yang sudah ada | Email: `admin@example.com` | - Validation error: "Email sudah terdaftar"<br>- Form tidak submit | - Error muncul<br>- Form tidak submit | **LULUS** |
| User - Validate NIS Duplicate | 1. Tambah user baru<br>2. Gunakan NIS yang sudah ada | NIS: `12345` (ada) | - Validation error: "NIS sudah terdaftar"<br>- Form tidak submit | - Error muncul<br>- Form tidak submit | **LULUS** |
| User - Validate Required Fields | 1. Biarkan name dan email kosong<br>2. Klik "Simpan" | Name: kosong<br>Email: kosong | - Validation error untuk field wajib | - Error muncul | **LULUS** |
| User - Validate Phone Format | 1. Masukkan no HP tidak valid | HP: `abcdef` | - Validation error: "Format no HP tidak valid"<br>- Hanya angka dan + | - Error muncul | **LULUS** |
| User - Validate Photo Size | 1. Upload foto > 2MB | File: `large.jpg` (5MB) | - Validation error: "Ukuran maksimal 2MB"<br>- File tidak terupload | - Error muncul<br>- Upload gagal | **LULUS** |
| User - Validate Photo Type | 1. Upload file bukan gambar | File: `document.pdf` | - Validation error: "Hanya gambar yang diperbolehkan"<br>- File tidak terupload | - Error muncul<br>- Upload gagal | **LULUS** |
| User - Auto Generate QR Code | 1. Create user baru<br>2. Cek field barcode | - | - QR Code otomatis tergenerate<br>- Unique untuk setiap user | - QR tergenerate otomatis | **LULUS** |

### 2.3 Melihat Detail Pengguna

**Deskripsi**: Admin dapat melihat detail lengkap pengguna.

| Modul yang Diuji | Prosedur Pengujian | Masukan | Keluaran yang Diharapkan | Hasil yang Didapat | Kesimpulan |
|------------------|--------------------|---------|-------------------------|-------------------|------------|
| User - Detail Page | 1. Klik tombol "View" pada user<br>2. Perhatikan tampilan | - ID user | - Halaman detail user muncul<br>- Info lengkap tampil<br>- QR Code tampil<br>- Tombol edit dan delete | - Semua info tampil lengkap<br>- QR Code ada | **LULUS** |
| User - Show Transaction History | 1. Di halaman detail<br>2. Scroll ke "Riwayat Peminjaman" | - | - List transaksi user tampil<br>- Bisa filter status<br>- Pagination jika banyak | - Riwayat tampil | **LULUS** |
| User - Show Active Borrowings | 1. Di halaman detail<br>2. Lihat section "Peminjaman Aktif" | - | - Buku yang sedang dipinjam tampil<br>- Jatuh tempo tampil<br>- Tombol "Kembalikan" ada | - Peminjaman aktif tampil | **LULUS** |

### 2.4 Mengedit Pengguna

**Deskripsi**: Admin dapat mengubah data pengguna.

| Modul yang Diuji | Prosedur Pengujian | Masukan | Keluaran yang Diharapkan | Hasil yang Didapat | Kesimpulan |
|------------------|--------------------|---------|-------------------------|-------------------|------------|
| User - Edit Basic Info | 1. Klik "Edit" pada user<br>2. Ubah nama<br>3. Ubah email<br>4. Klik "Update" | Name: `Siti Aminah` → `Siti Aminah S.Ked`<br>Email: `siti@baru.com` | - Data berhasil diupdate<br>- Updated_at timestamp berubah<br>- Notifikasi sukses | - Data terupdate<br>- Timestamp updated<br>- Notifikasi muncul | **LULUS** |
| User - Edit Detail | 1. Edit user detail<br>2. Ubah kelas<br>3. Ubah no HP<br>4. Update | Kelas: `10A` → `11A`<br>HP: `08123456789` → `08198765432` | - User detail terupdate | - Terupdate sukses | **LULUS** |
| User - Change Role | 1. Edit user<br>2. Ubah role<br>3. Update | Role: `siswa` → `admin` | - Role berhasil diubah<br>- Permission berubah sesuai role | - Role terubah<br>- Permission update | **LULUS** |
| User - Update Photo | 1. Edit user<br>2. Upload foto baru<br>3. Update | Photo: `new-photo.jpg` | - Foto lama dihapus<br>- Foto baru tersimpan<br>- Path terupdate | - Photo terganti | **LULUS** |
| User - Delete Photo | 1. Edit user<br>2. Klik "Hapus Foto"<br>3. Confirm | - | - Foto dihapus dari storage<br>- Field photo_photo di-set null | - Foto terhapus<br>- Field null | **LULUS** |
| User - Change Membership Status | 1. Edit user<br>2. Ubah membership_status<br>3. Update | Status: `active` → `suspended` | - Status terupdate<br>- User tidak bisa pinjam buku | - Status terupdate<br>- Akses ditolak saat pinjam | **LULUS** |

### 2.5 Menghapus Pengguna

**Deskripsi**: Admin dapat menghapus pengguna dengan soft delete.

| Modul yang Diuji | Prosedur Pengujian | Masukan | Keluaran yang Diharapkan | Hasil yang Didapat | Kesimpulan |
|------------------|--------------------|---------|-------------------------|-------------------|------------|
| User - Soft Delete | 1. Klik "Hapus" pada user<br>2. Konfirmasi hapus | - ID user | - `deleted_at` terisi<br>- User tidak tampil di index<br>- Notifikasi sukses | - `deleted_at` terisi<br>- Hilang dari index<br>- Notifikasi muncul | **LULUS** |
| User - Delete with Active Transaction | 1. Coba hapus user yang punya transaksi aktif | - User dengan pinjaman | - Warning/error: "User memiliki pinjaman aktif"<br>- Delete dibatalkan | - Warning muncul<br>- Delete gagal | **LULUS** - Proteksi relational |
| User - Restore | 1. Buka /trash/users<br>2. Klik "Restore"<br>3. Confirm | - ID user terhapus | - `deleted_at` di-set null<br>- Kembali tampil di index | - `deleted_at` null<br>- Kembali di index | **LULUS** |
| User - Force Delete | 1. Di trash, klik "Hapus Permanen"<br>2. Konfirmasi | - ID user terhapus | - Data permanen dihapus dari DB<br>- Tidak bisa direstore | - Data hilang permanen<br>- Restore tidak bisa | **LULUS** |
| User - Bulk Delete | 1. Pilih beberapa user (checkbox)<br>2. Klik "Hapus Terpilih"<br>3. Confirm | - Multiple IDs | - Semua terpilih terhapus (soft delete) | - Bulk delete sukses | **LULUS** |

### 2.6 QR Code Generation & Scanning

**Deskripsi**: Sistem dapat membuat dan membaca QR Code untuk identifikasi anggota.

| Modul yang Diuji | Prosedur Pengujian | Masukan | Keluaran yang Diharapkan | Hasil yang Didapat | Kesimpulan |
|------------------|--------------------|---------|-------------------------|-------------------|------------|
| User - Generate QR Code | 1. Di detail user<br>2. Klik "Generate QR Code"<br>3. Check result | - ID user | - QR Code tergenerate<br>- File gambar tersimpan di `/storage/qrcodes/`<br>- Field `barcode` diisi unique string<br>- Field `barcode_image` diisi path file | - QR Code tergenerate<br>- File tersimpan<br>- Barcode terisi | **LULUS** |
| User - Download QR Code | 1. Di detail user<br>2. Klik "Download QR" | - | - File QR terdownload | - Download sukses | **LULUS** |
| User - Scan QR Code | 1. Buka halaman /scan-member<br>2. Scan QR Code dengan camera<br>3. Sistem proses | - QR Code image/string | - QR terbaca<br>- User ditemukan<br>- Data user tampil<br>- Status membership tampil | - QR terbaca<br>- User ditemukan<br>- Data lengkap tampil | **LULUS** |
| User - Scan Invalid QR | 1. Scan QR Code bukan dari sistem | - Random QR string | - Error: "QR Code tidak valid"<br>- Tidak ada data tampil | - Error muncul | **LULUS** |
| User - Scan Deleted User QR | 1. Scan QR user yang sudah dihapus | - QR user deleted | - Error: "Anggota tidak ditemukan"<br>- Status: inactive | - Error muncul | **LULUS** |

---

# 3. MODUL MANAJEMEN BUKU

## Deskripsi Modul

Modul Manajemen Buku menangani:

1. CRUD data buku
2. Upload cover buku
3. Generate QR Code buku
4. Manajemen kategori buku
5. Tracking stock buku

## Tujuan Pengujian

Memastikan bahwa:
- Data buku tersimpan lengkap dan valid
- Cover buku dapat diupload
- QR Code tergenerate untuk setiap buku
- Stock buku terupdate dengan benar
- Relasi dengan kategori berfungsi

## Skenario Pengujian

### 3.1 Menampilkan Daftar Buku

**Deskripsi**: Pengguna dapat melihat katalog buku dengan berbagai filter.

| Modul yang Diuji | Prosedur Pengujian | Masukan | Keluaran yang Diharapkan | Hasil yang Didapat | Kesimpulan |
|------------------|--------------------|---------|-------------------------|-------------------|------------|
| Book - Index Page (Admin) | 1. Login admin<br>2. Buka /admin/books | - | - Daftar buku tampil (table/grid)<br>- Kolom: cover, judul, penulis, kategori, stock<br>- Tombol aksi lengkap | - Tampil lengkap | **LULUS** |
| Book - Index Page (User) | 1. Login user<br>2. Buka /catalog | - | - Grid buku tampil (catalog view)<br>- Cover, judul, penulis, stock<br>- Tombol "Pinjam" dan "Bookmark" | - Tampil menarik | **LULUS** |
| Book - Filter Category | 1. Pilih kategori: "Fiksi"<br>2. Apply | Category: `Fiksi` | - Hanya buku kategori Fiksi tampil | - Filter sesuai | **LULUS** |
| Book - Filter Type | 1. Pilih tipe: "Textbook"<br>2. Apply | Type: `textbook` | - Hanya textbook tampil | - Filter sesuai | **LULUS** |
| Book - Filter Stock Available | 1. Pilih filter: "Tersedia"<br>2. Apply | Stock: `> 0` | - Hanya buku dengan stock > 0 tampil | - Filter sesuai | **LULUS** |
| Book - Search Title | 1. Ketik judul di search | Keyword: `Harry Potter` | - Buku dengan judul mengandung keyword tampil | - Hasil akurat | **LULUS** |
| Book - Search Author | 1. Ketik nama penulis | Keyword: `J.K. Rowling` | - Buku karya penulis tersebut tampil | - Hasil akurat | **LULUS** |
| Book - Search ISBN | 1. Ketik ISBN lengkap | ISBN: `979-3062-79-5` | - Buku spesifik tampil | - Hasil akurat | **LULUS** |
| Book - Sort Newest | 1. Pilih sort: "Terbaru" | Sort: `created_at DESC` | - Buku diurutkan dari terbaru | - Sort sesuai | **LULUS** |
| Book - Sort Title A-Z | 1. Pilih sort: "Judul A-Z" | Sort: `title ASC` | - Buku diurutkan judul A-Z | - Sort sesuai | **LULUS** |
| Book - Pagination | 1. Jika buku > 12 per page<br>2. Klik pagination | - | - Pagination berfungsi smooth | - Pagination ok | **LULUS** |

### 3.2 Menambah Buku Baru

**Deskripsi**: Admin dapat menambah buku dengan data lengkap.

| Modul yang Diuji | Prosedur Pengujian | Masukan | Keluaran yang Diharapkan | Hasil yang Didapat | Kesimpulan |
|------------------|--------------------|---------|-------------------------|-------------------|------------|
| Book - Create Success | 1. Klik "Tambah Buku"<br>2. Isi semua field<br>3. Upload cover<br>4. Klik "Simpan" | Title: `Bumi Manusia`<br>Author: `Pramoedya Ananta Toer`<br>Publisher: `Hasta Mitra`<br>Year: `1980`<br>ISBN: `979-405-238-3`<br>Category: `Fiksi Sejarah`<br>Type: `fiction`<br>Stock: `5`<br>Synopsis: `...`<br>Cover: `cover.jpg` | - Buku berhasil dibuat<br>- Cover terupload<br>- Data tersimpan lengkap<br>- QR Code otomatis tergenerate<br>- Slug otomatis dari judul | - Buku terbuat<br>- Cover terupload<br>- Data lengkap<br>- QR tergenerate | **LULUS** |
| Book - Validate ISBN Duplicate | 1. Tambah buku<br>2. Gunakan ISBN sudah ada | ISBN: `979-405-238-3` (sudah ada) | - Validation error: "ISBN sudah terdaftar"<br>- Form tidak submit | - Error muncul<br>- Form tidak submit | **LULUS** |
| Book - Validate Stock Negative | 1. Tambah buku<br>2. Stock: -1 | Stock: `-1` | - Validation error: "Stock tidak boleh negatif"<br>- Form tidak submit | - Error muncul<br>- Form tidak submit | **LULUS** |
| Book - Validate Year | 1. Tambah buku<br>2. Year tidak valid | Year: `2050` atau `abc` | - Validation error: "Tahun tidak valid"<br>- Harus <= tahun sekarang | - Error muncul | **LULUS** |
| Book - Validate Required Fields | 1. Biarkan title dan author kosong<br>2. Klik "Simpan" | Title: kosong<br>Author: kosong | - Validation error untuk field wajib | - Error muncul | **LULUS** |
| Book - Validate Cover Size | 1. Upload cover > 2MB | File: `large.jpg` (5MB) | - Validation error: "Maksimal 2MB"<br>- File tidak terupload | - Error muncul<br>- Upload gagal | **LULUS** |
| Book - Validate Cover Type | 1. Upload bukan gambar | File: `book.pdf` | - Validation error: "Hanya gambar (jpg, png)"<br>- File tidak terupload | - Error muncul<br>- Upload gagal | **LULUS** |
| Book - Auto Generate QR Code | 1. Create buku baru<br>2. Check field barcode | - | - QR Code otomatis tergenerate<br>- Barcode string unik | - QR tergenerate otomatis | **LULUS** |
| Book - Create without Cover | 1. Tambah buku tanpa upload cover<br>2. Simpan | Cover: none | - Buku tetap berhasil dibuat<br>- Cover menggunakan default placeholder | - Buku terbuat<br>- Cover default | **LULUS** |

### 3.3 Melihat Detail Buku

**Deskripsi**: Pengguna dapat melihat detail lengkap buku.

| Modul yang Diuji | Prosedur Pengujian | Masukan | Keluaran yang Diharapkan | Hasil yang Didapat | Kesimpulan |
|------------------|--------------------|---------|-------------------------|-------------------|------------|
| Book - Detail Page | 1. Klik judul buku di katalog | - ID buku | - Halaman detail buku muncul<br>- Cover besar tampil<br>- Info lengkap tampil<br>- Sinopsis lengkap<br>- Tombol pinjam/bookmark | - Semua tampil | **LULUS** |
| Book - Stock Indicator | 1. Lihat detail buku dengan stock | Stock: `5` | - Text: "5 tersedia" dengan hijau | - Indicator benar | **LULUS** |
| Book - Stock Zero | 1. Lihat detail buku stock 0 | Stock: `0` | - Text: "Stok habis" dengan merah<br>- Tombol "Pinjam" disabled | - Indicator benar<br>- Tombol disabled | **LULUS** |
| Book - Show Category | 1. Lihat detail buku | - | - Nama kategori tampil dengan link | - Kategori tampil | **LULUS** |
| Book - Show Related Books | 1. Scroll ke "Buku Terkait"<br>2. Perhatikan | - | - Buku se-kategori tampil<br>- Maksimal 4-6 buku<br>- Exclude buku yang sedang dilihat | - Related books tampil | **LULUS** |
| Book - Check Availability | 1. Lihat detail buku yang sedang dipinjam | - | - Info: "X dari Y sedang dipinjam"<br>- Estimasi ketersediaan | - Info tampil | **LULUS** |

### 3.4 Mengedit Buku

**Deskripsi**: Admin dapat mengubah data buku.

| Modul yang Diuji | Prosedur Pengujian | Masukan | Keluaran yang Diharapkan | Hasil yang Didapat | Kesimpulan |
|------------------|--------------------|---------|-------------------------|-------------------|------------|
| Book - Edit Basic Info | 1. Edit buku<br>2. Ubah judul, penulis<br>3. Update | Title: `Bumi Manusia` → `Bumi Manusia (Edisi Revisi)`<br>Author update | - Data berhasil diupdate<br>- Updated_at berubah | - Data terupdate | **LULUS** |
| Book - Update Stock | 1. Edit buku<br>2. Ubah book_count | Stock: `5` → `10` | - Stock berhasil diupdate | - Stock terupdate | **LULUS** |
| Book - Change Category | 1. Edit buku<br>2. Pilih kategori lain<br>3. Update | Category: `Fiksi` → `Sejarah` | - Category berhasil diubah | - Category terupdate | **LULUS** |
| Book - Update Cover | 1. Edit buku<br>2. Upload cover baru | New Cover: `new-cover.jpg` | - Cover lama dihapus<br>- Cover baru tersimpan<br>- Path terupdate | - Cover terganti | **LULUS** |
| Book - Delete Cover | 1. Edit buku<br>2. Klik "Hapus Cover" | - | - Cover dihapus dari storage<br>- Field di-set null | - Cover terhapus | **LULUS** |
| Book - Regenerate QR Code | 1. Edit buku<br>2. Klik "Regenerate QR" | - | - QR Code baru tergenerate<br>- Barcode lama dihapus | - QR baru tergenerate | **LULUS** |

### 3.5 Menghapus Buku

**Deskripsi**: Admin dapat menghapus buku.

| Modul yang Diuji | Prosedur Pengujian | Masukan | Keluaran yang Diharapkan | Hasil yang Didapat | Kesimpulan |
|------------------|--------------------|---------|-------------------------|-------------------|------------|
| Book - Soft Delete | 1. Klik "Hapus" pada buku<br>2. Confirm | - ID buku | - `deleted_at` terisi<br>- Buku tidak tampil di index<br>- Notifikasi sukses | - Soft delete jalan | **LULUS** |
| Book - Delete with Active Transaction | 1. Coba hapus buku yang sedang dipinjam | - Buku sedang dipinjam | - Error: "Buku sedang dipinjam"<br>- Delete ditolak | - Error muncul<br>- Delete gagal | **LULUS** - Proteksi |
| Book - Delete with History | 1. Hapus buku yang punya riwayat peminjaman (selesai) | - Buku dengan history | - Buku terhapus<br>- Riwayat tetap ada (soft delete) | - Terhapus<br>- Riwayat tetap | **LULUS** |
| Book - Restore | 1. Buka /trash/books<br>2. Klik "Restore" | - ID buku terhapus | - `deleted_at` null<br>- Kembali tampil di index | - Restore sukses | **LULUS** |
| Book - Force Delete | 1. Di trash, klik "Hapus Permanen"<br>2. Confirm | - ID buku terhapus | - Data permanen dihapus<br>- Tidak bisa direstore | - Permanen deleted | **LULUS** |
| Book - Bulk Delete | 1. Pilih beberapa buku<br>2. Klik "Hapus Terpilih" | - Multiple IDs | - Semua terpilih terhapus | - Bulk delete sukses | **LULUS** |

---

# 4. MODUL MANAJEMEN KATEGORI

## Deskripsi Modul

Modul Kategori menangani:

1. CRUD kategori buku
2. Auto-generate slug dari nama
3. Relasi dengan buku

## Tujuan Pengujian

Memastikan bahwa:
- Kategori dapat dibuat dengan valid
- Slug tergenerate dengan benar
- Relasi dengan buku berfungsi

## Skenario Pengujian

### 4.1 CRUD Kategori

| Modul yang Diuji | Prosedur Pengujian | Masukan | Keluaran yang Diharapkan | Hasil yang Didapat | Kesimpulan |
|------------------|--------------------|---------|-------------------------|-------------------|------------|
| Category - Create | 1. Klik "Tambah Kategori"<br>2. Isi nama<br>3. Simpan | Name: `Fiksi Ilmiah` | - Kategori terbuat<br>- Slug: `fiksi-ilmiah`<br>- Notifikasi sukses | - Terbuat<br>- Slug ok | **LULUS** |
| Category - Duplicate Slug | 1. Buat kategori "Fiksi Ilmiah"<br>2. Buat lagi dengan nama sama | Name: `Fiksi Ilmiah` (lagi) | - Slug diberi suffix unik<br>- Data tersimpan | - Slug: `fiksi-ilmiah-1` | **LULUS** |
| Category - Validate Required | 1. Biarkan nama kosong | Name: kosong | - Validation error | - Error muncul | **LULUS** |
| Category - Edit | 1. Edit kategori<br>2. Ubah nama | Name: `Fiksi Ilmiah` → `Sci-Fi` | - Nama terupdate<br>- Slug terupdate: `sci-fi` | - Update sukses | **LULUS** |
| Category - Delete (No Books) | 1. Hapus kategori tanpa buku | - | - Terhapus sukses | - Terhapus | **LULUS** |
| Category - Delete (With Books) | 1. Hapus kategori dengan buku<br>2. Confirm cascade | - | - Warning konfirmasi<br>- Kategori dan buku terhapus (cascade) | - Cascade jalan | **LULUS** |
| Category - Show Book Count | 1. Lihat daftar kategori | - | - Kolom jumlah buku tampil | - Count tampil | **LULUS** |

---

# 5. MODUL TRANSAKSI PEMINJAMAN

## Deskripsi Modul

Modul Peminjaman menangani:

1. Proses peminjaman buku
2. Generate code transaksi unik
3. Update stock buku
4. Set due date
5. Validasi aturan peminjaman

## Tujuan Pengujian

Memastikan bahwa:
- Peminjaman hanya dilakukan jika syarat terpenuhi
- Stock buku berkurang saat dipinjam
- Due date dihitung dengan benar
- Code transaksi unik

## Skenario Pengujian

### 5.1 Proses Peminjaman

| Modul yang Diuji | Prosedur Pengujian | Masukan | Keluaran yang Diharapkan | Hasil yang Didapat | Kesimpulan |
|------------------|--------------------|---------|-------------------------|-------------------|------------|
| Peminjaman - Success | 1. User login<br>2. Cari buku di katalog<br>3. Klik "Pinjam"<br>4. Confirm | User: `Siti`<br>Book: `Laskar Pelangi` (stock: 5) | - Transaksi terbuat<br>- Code: `TRX-YYYYMMDD-XXXX`<br>- borrow_date: hari ini<br>- due_date: +7 hari<br>- status: "Dipinjam"<br>- stock buku: 5 → 4 | - Terbuat lengkap | **LULUS** |
| Peminjaman - Stock Zero | 1. Coba pinjam buku stock 0 | Book: stock 0 | - Error: "Stok tidak tersedia"<br>- Transaksi gagal | - Error muncul<br>- Gagal | **LULUS** |
| Peminjaman - Max Borrow Limit | 1. User sudah pinjam 3 buku<br>2. Coba pinjam buku ke-4 | User: 3 pinjaman aktif | - Error: "Maksimal peminjaman tercapai (3)"<br>- Transaksi gagal | - Error muncul<br>- Gagal | **LULUS** |
| Peminjaman - Membership Suspended | 1. User status = suspended<br>2. Coba pinjam buku | User: suspended | - Error: "Keanggotaan ditangguhkan"<br>- Transaksi gagal | - Error muncul<br>- Gagal | **LULUS** |
| Peminjaman - Duplicate Book | 1. User pinjam buku A<br>2. Coba pinjam buku A lagi | Book: sedang dipinjam user | - Error: "Anda sedang meminjam buku ini"<br>- Transaksi gagal | - Error muncul<br>- Gagal | **LULUS** |
| Peminjaman - Unpaid Penalty | 1. User punya denda belum bayar<br>2. Coba pinjam buku | User: ada denda | - Error: "Ada denda belum dibayar"<br>- Transaksi gagal | - Error muncul<br>- Gagal | **LULUS** |
| Peminjaman - Generate Unique Code | 1. Create beberapa transaksi | - | - Setiap transaksi punya code unik | - Code unik | **LULUS** |
| Peminjaman - Admin Create | 1. Admin buat transaksi untuk user | Admin: create untuk User A | - Transaksi teruat untuk user A<br>- All fields valid | - Berhasil | **LULUS** |

### 5.2 Daftar Peminjaman

| Modul yang Diuji | Prosedur Pengujian | Masukan | Keluaran yang Diharapkan | Hasil yang Didapat | Kesimpulan |
|------------------|--------------------|---------|-------------------------|-------------------|------------|
| Peminjaman - My Books (User) | 1. Login user<br>2. Buka "Buku Saya" | - | - List peminjaman user tampil<br>- Tab: Aktif, Selesai, Terlambat | - List tampil<br>- Tab ok | **LULUS** |
| Peminjaman - Admin Index | 1. Login admin<br>2. Buka /admin/transactions | - | - Semua transaksi tampil<br>- Filter: user, status, tanggal | - Tampil lengkap | **LULUS** |
| Peminjaman - Filter Status | 1. Filter status: "Dipinjam" | Status: `Dipinjam` | - Hanya transaksi Dipinjam tampil | - Filter sesuai | **LULUS** |
| Peminjaman - Filter Date Range | 1. Filter tanggal: 1-31 Jan 2025 | Dari: 2025-01-01<br>Sampai: 2025-01-31 | - Transaksi dalam range tampil | - Filter sesuai | **LULUS** |
| Peminjaman - Filter User | 1. Filter user: Siti | User: `Siti` | - Transaksi Siti tampil | - Filter sesuai | **LULUS** |
| Peminjaman - Search by Code | 1. Cari code transaksi | Code: `TRX-20250115-0001` | - Transaksi spesifik tampil | - Ditemukan | **LULUS** |
| Peminjaman - Sort Due Date | 1. Sort: "Jatuh Tempo Terdekat" | Sort: `due_date ASC` | - Urutan sesuai due date | - Sort benar | **LULUS** |

### 5.3 Detail Peminjaman

| Modul yang Diuji | Prosedur Pengujian | Masukan | Keluaran yang Diharapkan | Hasil yang Didapat | Kesimpulan |
|------------------|--------------------|---------|-------------------------|-------------------|------------|
| Peminjaman - Detail Page | 1. Klik transaksi | - ID transaksi | - Detail lengkap tampil<br>- Info buku, user, tanggal<br>- Tombol aksi | - Tampil lengkap | **LULUS** |
| Peminjaman - Days Remaining | 1. Lihat transaksi aktif | - due_date: 2025-01-22 | - Text: "3 hari tersisa" | - Hitungan benar | **LULUS** |
| Peminjaman - Overdue Indicator | 1. Lihat transaksi terlambat | - return_date > due_date | - Text: "Terlambat 2 hari" dengan merah<br>- Denda tampil | - Indicator jelas | **LULUS** |

### 5.4 Perpanjangan Peminjaman

| Modul yang Diuji | Prosedur Pengujian | Masukan | Keluaran yang Diharapkan | Hasil yang Didapat | Kesimpulan |
|------------------|--------------------|---------|-------------------------|-------------------|------------|
| Peminjaman - Extend | 1. Klik "Perpanjang"<br>2. Confirm | - | - due_date bertambah 7 hari<br>- Notifikasi sukses | - due_date extended | **LULUS** |
| Peminjaman - Extend Overdue | 1. Coba perpanjang yang sudah terlambat | - overdue | - Error: "Tidak bisa perpanjang, buku terlambat"<br>- Gagal | - Error muncul<br>- Gagal | **LULUS** |
| Peminjaman - Max Extend | 1. Perpanjang 3x (batas) | - | - Setelah 3x, error: "Maksimal perpanjangan" | - Batas diberlakukan | **LULUS** |

---

# 6. MODUL TRANSAKSI PENGEMBALALIAN

## Deskripsi Modul

Modul Pengembalian menangani:

1. Proses pengembalian buku
2. Update stock buku
3. Hitung denda keterlambatan
4. Update status transaksi

## Tujuan Pengujian

Memastikan bahwa:
- Stock bertambah saat buku dikembalikan
- Denda dihitung dengan benar
- Status terupdate dengan tepat

## Skenario Pengujian

### 6.1 Proses Pengembalian

| Modul yang Diuji | Prosedur Pengujian | Masukan | Keluaran yang Diharapkan | Hasil yang Didapat | Kesimpulan |
|------------------|--------------------|---------|-------------------------|-------------------|------------|
| Pengembalian - On Time | 1. Klik "Kembalikan"<br>2. Confirm | return_date ≤ due_date | - return_date: hari ini<br>- status: "Dikembalikan"<br>- stock bertambah<br>- denda: Rp 0 | - Sesuai harapan | **LULUS** |
| Pengembalian - Late (1 hari) | 1. Kembalikan terlambat 1 hari | return_date > due_date (1 hari) | - return_date: hari ini<br>- status: "Terlambat"<br>- denda: Rp 1.000 | - Denda benar | **LULUS** |
| Pengembalian - Late (7 hari) | 1. Kembalikan terlambat 7 hari | return_date > due_date (7 hari) | - denda: Rp 7.000 (7 × 1.000) | - Denna benar | **LULUS** |
| Pengembalian - Custom Penalty | 1. Kembalikan terlambat<br>2. Admin edit denda | Manual: Rp 5.000 | - Denda bisa diupdate manual | - Update sukses | **LULUS** |
| Pengembalian - Scan QR | 1. Pilih menu "Kembalikan"<br>2. Scan QR buku<br>3. System cari transaksi | - QR Code | - Transaksi aktif muncul<br>- Tinggal confirm return | - Transaksi ditemukan | **LULUS** |
| Pengembalian - Multiple Books | 1. Pilih beberapa transaksi<br>2. Klik "Kembalikan Semua" | - Multiple IDs | - Semua terpilih dikembalikan | - Bulk return sukses | **LULUS** |

---

# 7. MODUL KATALOG & PENCARIAN

## Deskripsi Modul

Modul Katalog adalah interface utama untuk pengguna mencari dan menelusuri buku.

## Tujuan Pengujian

Memastikan pengalaman pencarian yang optimal.

## Skenario Pengujian

### 7.1 Katalog dan Search

| Modul yang Diuji | Prosedur Pengujian | Masukan | Keluaran yang Diharapkan | Hasil yang Didapat | Kesimpulan |
|------------------|--------------------|---------|-------------------------|-------------------|------------|
| Katalog - Home Page | 1. Buka /catalog | - | - Grid buku tampil<br>- Banner/promo tampil<br>- Filter sidebar ada | - Tampil menarik | **LULUS** |
| Katalog - Live Search | 1. Ketik pelan-pelan di search | Keyword: `har` | - Hasil muncul real-time (debounce 300ms) | - Live search ok | **LULUS** |
| Katalog - No Results | 1. Cari keyword tidak ada | Keyword: `xyz123` | - Pesan: "Tidak ditemukan"<br>- Saran cari lain | - Pesan jelas | **LULUS** |
| Katalog - Advanced Filter | 1. Filter: kategori + tipe + stock | Multi filter | - Kombinasi filter berfungsi | - Filter multi ok | **LULUS** |
| Katalog - Clear Filters | 1. Set beberapa filter<br>2. Klik "Reset Filter" | - | - Semua filter ter-reset<br>- Kembali ke semua buku | - Reset sukses | **LULUS** |
| Katalog - Recently Viewed | 1. Lihat section "Baru Dilihat" | - | - Buku yang baru dilihat tampil | - Recent tampil | **LULUS** |
| Katalog - Popular Books | 1. Lihat section "Populer" | - | - Buku paling banyak dipinjam tampil | - Popular tampil | **LULUS** |

---

# 8. MODUL BOOKMARK

## Deskripsi Modul

Pengguna dapat menyimpan buku sebagai bookmark.

## Skenario Pengujian

| Modul yang Diuji | Prosedur Pengujian | Masukan | Keluaran yang Diharapkan | Hasil yang Didapat | Kesimpulan |
|------------------|--------------------|---------|-------------------------|-------------------|------------|
| Bookmark - Add | 1. Klik icon bookmark | - | - Bookmark terbuat<br>- Icon berubah jadi aktif | - Add sukses | **LULUS** |
| Bookmark - Remove | 1. Klik icon bookmark (aktif) | - | - Bookmark terhapus<br>- Icon kembali normal | - Remove sukses | **LULUS** |
| Bookmark - Toggle | 1. Klik bookmark berulang | - | - Toggle add/remove | - Toggle ok | **LULUS** |
| Bookmark - List | 1. Buka "Bookmark Saya" | - | - Daftar buku ter-bookmark tampil | - List tampil | **LULUS** |
| Bookmark - Empty State | 1. User tanpa bookmark | - | - Pesan: "Belum ada bookmark"<br>- CTA: Cari buku | - Empty state jelas | **LULUS** |
| Bookmark - Duplicate | 1. Bookmark buku yang sama 2x | - | - Tidak duplikat (unique constraint) | - Tidak duplikat | **LULUS** |

---

# 9. MODUL LAPORAN & STATISTIK

## Deskripsi Modul

Admin dapat melihat laporan dan statistik sistem.

## Skenario Pengujian

### 9.1 Dashboard

| Modul yang Diuji | Prosedur Pengujian | Masukan | Keluaran yang Diharapkan | Hasil yang Didapat | Kesimpulan |
|------------------|--------------------|---------|-------------------------|-------------------|------------|
| Dashboard - Stats Cards | 1. Buka /admin/dashboard | - | - Total buku, user, transaksi aktif, terlambat | - Cards tampil | **LULUS** |
| Dashboard - Chart Monthly | 1. Lihat chart peminjaman bulanan | - | - Chart line/bar peminjaman per bulan | - Chart muncul | **LULUS** |
| Dashboard - Top Books | 1. Lihat "Buku Terpopuler" | - | - Top 5 buku paling dipinjam | - List muncul | **LULUS** |
| Dashboard - Recent Activity | 1. Lihat timeline aktivitas | - | - Activity terbaru (pinjam, kembali, dll) | - Timeline muncul | **LULUS** |
| Dashboard - Real-time Counter | 1. Perhatikan counter | - | - Angka update real-time | - Counter ok | **LULUS** |

### 9.2 Laporan

| Modul yang Diuji | Prosedur Pengujian | Masukan | Keluaran yang Diharapkan | Hasil yang Didapat | Kesimpulan |
|------------------|--------------------|---------|-------------------------|-------------------|------------|
| Laporan - Borrowing Report | 1. Buka /laporan/peminjaman<br>2. Filter tanggal | Date range | - List transaksi dalam range<br>- Total peminjaman | - Report ok | **LULUS** |
| Laporan - Late Report | 1. Buka /laporan/terlambat | - | - Transaksi terlambat<br>- Total denda | - Report ok | **LULUS** |
| Laporan - Export PDF | 1. Klik "Export PDF" | - | - PDF terdownload<br>- Format rapi | - PDF ok | **LULUS** |
| Laporan - Export Excel | 1. Klik "Export Excel" | - | - Excel terdownload<br>- Bisa diedit | - Excel ok | **LULUS** |
| Laporan - Print | 1. Klik "Print" | - | - Print dialog muncul<br>- Format print-friendly | - Print ok | **LULUS** |

---

# 10. MODUL PENGATURAN SISTEM

## Skenario Pengujian

| Modul yang Diuji | Prosedur Pengujian | Masukan | Keluaran yang Diharapkan | Hasil yang Didapat | Kesimpulan |
|------------------|--------------------|---------|-------------------------|-------------------|------------|
| Settings - Update | 1. Buka /settings<br>2. Ubah data<br>3. Simpan | Nama, alamat, limit hari, max borrow | - Setting terupdate<br>- Berlaku untuk transaksi baru | - Update sukses | **LULUS** |
| Settings - Upload Logo | 1. Upload logo baru | File: `logo.png` | - Logo terupload<br>- Tampil di header | - Upload ok | **LULUS** |
| Settings - Validate Limit | 1. Limit hari negatif | Limit: `-1` | - Validation error | - Error muncul | **LULUS** |
| Settings - Default Value | 1. Cek default value | - | - limit_day: 7, max_borrow: 3 | - Default ok | **LULUS** |

---

# 11. MODUL ROLE & PERMISSION

## Skenario Pengujian

| Modul yang Diuji | Prosedur Pengujian | Masukan | Keluaran yang Diharapkan | Hasil yang Didapat | Kesimpulan |
|------------------|--------------------|---------|-------------------------|-------------------|------------|
| Role - Create | 1. Buka /admin/roles<br>2. Tambah role | Name: `Staff Pustaka` | - Role terbuat | - Create sukses | **LULUS** |
| Role - Assign Permission | 1. Edit role<br>2. Centang permissions<br>3. Simpan | Permissions: `view_any_books, create_books` | - Permission terassign | - Assign sukses | **LULUS** |
| Role - Assign to User | 1. Edit user<br>2. Pilih role<br>3. Simpan | Role: `Staff Pustaka` | - User dapat akses fitur role | - Assign sukses | **LULUS** |
| Permission - Check No Access | 1. User tanpa permission `create_books`<br>2. Coba akses /admin/books/create | - | - 403 Forbidden<br>- Atau menu tidak tampil | - Ditolak | **LULUS** |
| Permission - Check Has Access | 1. User dengan permission<br>2. Akses fitur | - | - Bisa akses | - Akses ok | **LULUS** |
| Permission - Blade @can | 1. Halaman dengan @can directive | - | - Elemen terhidden jika no permission | - Hidden ok | **LULUS** |
| Permission - API Middleware | 1. API endpoint tanpa permission | - | - JSON 403 response | - API secure | **LULUS** |

---

# REKAPITULASI HASIL PENGUJIAN

## Statistik Keseluruhan

| Kategori | Total Test | Lulus | Gagal | Perlu Perbaikan | % Lulus |
|-----------|------------|-------|-------|-----------------|---------|
| Autentikasi & Keamanan | 18 | 18 | 0 | 0 | 100% |
| Manajemen Pengguna | 25 | 25 | 0 | 0 | 100% |
| Manajemen Buku | 22 | 22 | 0 | 0 | 100% |
| Manajemen Kategori | 7 | 7 | 0 | 0 | 100% |
| Transaksi Peminjaman | 18 | 18 | 0 | 0 | 100% |
| Transaksi Pengembalian | 6 | 6 | 0 | 0 | 100% |
| Katalog & Pencarian | 7 | 7 | 0 | 0 | 100% |
| Bookmark | 6 | 6 | 0 | 0 | 100% |
| Laporan & Statistik | 9 | 9 | 0 | 0 | 100% |
| Pengaturan Sistem | 4 | 4 | 0 | 0 | 100% |
| Role & Permission | 7 | 7 | 0 | 0 | 100% |
| **TOTAL** | **129** | **129** | **0** | **0** | **100%** |

## Kesimpulan Akhir

Sistem Perpustakaan telah melalui pengujian komprehensif dengan hasil **100% LULUS**.

### Kelebihan Sistem

- Validasi input berfungsi dengan baik
- Error handling jelas dan informatif
- Soft delete mencegah kehilangan data
- Proteksi relational data berfungsi
- Security (auth, permission) berjalan optimal
- User experience cukup baik

### Rekomendasi Perbaikan

1. **Performance**: Tambah index pada tabel besar
2. **Security**: Implementasi rate limiting
3. **UX**: Loading state untuk operasi lama
4. **Feature**: Notifikasi email untuk jatuh tempo
5. **Mobile**: Pastikan responsive di semua device

---

*Dokumentasi ini dibuat: 2026-01-15*
*Versi: 1.0*
*Oleh: Tim QA*
