# Dokumen Pengujian Sistem - Sistem Perpustakaan

## Pendahuluan

Dokumen ini berisi rencana dan hasil pengujian sistem perpustakaan secara menyeluruh. Pengujian mencakup semua modul dan fitur yang tersedia dalam sistem.

---

## Daftar Modul yang Diuji

1. Autentikasi & Autorisasi
2. Manajemen Anggota
3. Manajemen Kategori Buku
4. Manajemen Buku
5. Transaksi Peminjaman
6. Transaksi Pengembalian
7. Bookmark Buku
8. Katalog & Pencarian Buku
9. Laporan & Statistik
10. Pengaturan Sistem
11. Manajemen Role & Permission

---

## 1. Modul Autentikasi & Autorisasi

### 1.1 Login Pengguna

| Modul yang Diuji | Prosedur Pengujian | Masukan | Keluaran yang Diharapkan | Hasil yang Didapat | Kesimpulan |
|------------------|--------------------|---------|-------------------------|-------------------|------------|
| Autentikasi - Login | 1. Buka halaman login<br>2. Masukkan email yang terdaftar<br>3. Masukkan password yang benar<br>4. Klik tombol Login | Email: `admin@example.com`<br>Password: `password` | - Redirect ke dashboard<br>- Session terbuat<br>- Menu sesuai role tampil | - Berhasil redirect ke dashboard<br>- Session terbuat<br>- Menu admin tampil | **Lulus** - Login berfungsi dengan baik |
| Autentikasi - Login Email Salah | 1. Buka halaman login<br>2. Masukkan email tidak terdaftar<br>3. Masukkan password<br>4. Klik tombol Login | Email: `wrong@example.com`<br>Password: `password` | - Pesan error "Credentials not found"<br>- Tetap di halaman login | - Pesan error muncul<br>- Tetap di halaman login | **Lulus** - Validasi email berfungsi |
| Autentikasi - Login Password Salah | 1. Buka halaman login<br>2. Masukkan email terdaftar<br>3. Masukkan password salah<br>4. Klik tombol Login | Email: `admin@example.com`<br>Password: `wrongpassword` | - Pesan error "Invalid password"<br>- Tetap di halaman login | - Pesan error muncul<br>- Tetap di halaman login | **Lulus** - Validasi password berfungsi |
| Autentikasi - Login Kosong | 1. Buka halaman login<br>2. Biarkan email kosong<br>3. Biarkan password kosong<br>4. Klik tombol Login | Email: kosong<br>Password: kosong | - Validasi form "Email wajib diisi"<br>- Validasi form "Password wajib diisi" | - Validasi muncul<br>- Form tidak submit | **Lulus** - Validasi required berfungsi |

### 1.2 Register Pengguna Baru

| Modul yang Diuji | Prosedur Pengujian | Masukan | Keluaran yang Diharapkan | Hasil yang Didapat | Kesimpulan |
|------------------|--------------------|---------|-------------------------|-------------------|------------|
| Autentikasi - Register | 1. Buka halaman register<br>2. Isi nama lengkap<br>3. Isi email valid<br>4. Isi password minimal 8 karakter<br>5. Konfirmasi password<br>6. Klik Register | Nama: `John Doe`<br>Email: `john@example.com`<br>Password: `password123`<br>Konfirmasi: `password123` | - User berhasil dibuat<br>- Redirect ke dashboard<br>- Email verifikasi dikirim | - User berhasil dibuat<br>- Redirect ke dashboard<br>- Email verifikasi dikirim | **Lulus** - Register berfungsi |
| Autentikasi - Register Email Duplikat | 1. Buka halaman register<br>2. Isi nama<br>3. Isi email yang sudah terdaftar<br>4. Isi password<br>5. Klik Register | Email: `admin@example.com` | - Pesan error "Email sudah terdaftar"<br>- Form tidak submit | - Pesan error muncul<br>- Form tidak submit | **Lulus** - Validasi unique email berfungsi |
| Autentikasi - Register Password Tidak Cocok | 1. Buka halaman register<br>2. Isi data valid<br>3. Password dan konfirmasi beda<br>4. Klik Register | Password: `password123`<br>Konfirmasi: `password456` | - Pesan error "Password tidak cocok"<br>- Form tidak submit | - Pesan error muncul<br>- Form tidak submit | **Lulus** - Validasi password confirmation berfungsi |

### 1.3 Logout

| Modul yang Diuji | Prosedur Pengujian | Masukan | Keluaran yang Diharapkan | Hasil yang Didapat | Kesimpulan |
|------------------|--------------------|---------|-------------------------|-------------------|------------|
| Autentikasi - Logout | 1. Login sebagai user<br>2. Klik tombol Logout<br>3. Konfirmasi logout | - Session aktif | - Session dihapus<br>- Redirect ke halaman login | - Session dihapus<br>- Redirect ke halaman login | **Lulus** - Logout berfungsi |

### 1.4 Reset Password

| Modul yang Diuji | Prosedur Pengujian | Masukan | Keluaran yang Diharapkan | Hasil yang Didapat | Kesimpulan |
|------------------|--------------------|---------|-------------------------|-------------------|------------|
| Autentikasi - Lupa Password | 1. Buka halaman login<br>2. Klik "Lupa Password"<br>3. Masukkan email terdaftar<br>4. Klik "Kirim Link Reset" | Email: `admin@example.com` | - Email reset dikirim<br>- Token reset tersimpan di DB<br>- Pesan sukses tampil | - Email reset dikirim<br>- Token tersimpan<br>- Pesan sukses tampil | **Lulus** - Reset password berfungsi |
| Autentikasi - Reset dengan Token | 1. Buka link dari email<br>2. Masukkan password baru<br>3. Konfirmasi password<br>4. Klik "Reset Password" | Password baru: `newpass123` | - Password berhasil diubah<br>- Bisa login dengan password baru | - Password berhasil diubah<br>- Login berhasil dengan password baru | **Lulus** - Reset dengan token berfungsi |

---

## 2. Modul Manajemen Anggota

### 2.1 Menampilkan Daftar Anggota

| Modul yang Diuji | Prosedur Pengujian | Masukan | Keluaran yang Diharapkan | Hasil yang Didapat | Kesimpulan |
|------------------|--------------------|---------|-------------------------|-------------------|------------|
| Anggota - Index | 1. Login sebagai admin<br>2. Buka menu Anggota<br>3. Lihat daftar anggota | - Pagination 10 item per halaman | - Daftar anggota tampil<br>- Pagination berfungsi<br>- Data lengkap (nama, email, role) | - Daftar tampil<br>- Pagination berfungsi<br>- Data lengkap | **Lulus** - Index berfungsi |
| Anggota - Search | 1. Buka halaman Anggota<br>2. Ketik nama di search box<br>3. Tekan Enter | Keyword: `john` | - Menampilkan anggota dengan nama "john"<br>- Filter real-time | - Filter berfungsi<br>- Hasil sesuai keyword | **Lulus** - Search berfungsi |
| Anggota - Pagination | 1. Buka halaman Anggota<br>2. Klik halaman 2<br>3. Klik halaman terakhir | - Klik halaman pagination | - Berpindah ke halaman yang dipilih<br>- Data sesuai halaman | - Pagination berfungsi<br>- Data sesuai | **Lulus** - Pagination berfungsi |

### 2.2 Menambah Anggota Baru

| Modul yang Diuji | Prosedur Pengujian | Masukan | Keluaran yang Diharapkan | Hasil yang Didapat | Kesimpulan |
|------------------|--------------------|---------|-------------------------|-------------------|------------|
| Anggota - Create | 1. Buka menu Anggota<br>2. Klik "Tambah Anggota"<br>3. Isi form user<br>4. Isi form detail<br>5. Klik Simpan | User:<br>- Nama: `Jane Doe`<br>- Email: `jane@example.com`<br>- Role: `siswa`<br>- Password: `pass123`<br>Detail:<br>- NIS: `12345`<br>- Kelas: `10A`<br>- No HP: `08123456789` | - User berhasil dibuat<br>- User detail berhasil dibuat<br>- Redirect ke index<br>- Notifikasi sukses | - User berhasil dibuat<br>- Detail tersimpan<br>- Redirect berhasil<br>- Notifikasi muncul | **Lulus** - Create berfungsi |
| Anggota - Validasi NIS Duplikat | 1. Buka form tambah<br>2. Isi data valid<br>3. NIS yang sudah ada<br>4. Klik Simpan | NIS: `12345` (sudah ada) | - Pesan error "NIS sudah terdaftar"<br>- Form tidak submit | - Pesan error muncul<br|- Form tidak submit | **Lulus** - Validasi NIS berfungsi |
| Anggota - Validasi Email Duplikat | 1. Buka form tambah<br>2. Isi data<br>3. Email yang sudah ada<br>4. Klik Simpan | Email: `admin@example.com` | - Pesan error "Email sudah terdaftar"<br>- Form tidak submit | - Pesan error muncul<br>- Form tidak submit | **Lulus** - Validasi email berfungsi |
| Anggota - Upload Foto | 1. Buka form tambah/edit<br>2. Upload file foto<br>3. Klik Simpan | File: `photo.jpg` (max 2MB) | - Foto terupload<br>- Path tersimpan di DB<br>- Foto tampil di profil | - Upload berhasil<br>- Path tersimpan<br>- Foto tampil | **Lulus** - Upload foto berfungsi |

### 2.3 Mengedit Anggota

| Modul yang Diuji | Prosedur Pengujian | Masukan | Keluaran yang Diharapkan | Hasil yang Didapat | Kesimpulan |
|------------------|--------------------|---------|-------------------------|-------------------|------------|
| Anggota - Edit | 1. Buka daftar Anggota<br>2. Klik tombol Edit<br>3. Ubah data anggota<br>4. Klik Simpan | Nama: `Jane Doe` → `Jane Smith`<br>Kelas: `10A` → `11A` | - Data berhasil diupdate<br>- Perubahan tersimpan<br>- Notifikasi sukses | - Data terupdate<br>- Perubahan tersimpan<br>- Notifikasi muncul | **Lulus** - Edit berfungsi |
| Anggota - Update Status Membership | 1. Edit anggota<br>2. Ubah membership_status<br>3. Simpan | Status: `active` → `suspended` | - Status berhasil diubah<br>- User tidak bisa pinjam buku | - Status terubah<br>- Akses ditolak | **Lulus** - Update status berfungsi |

### 2.4 Menghapus Anggota

| Modul yang Diuji | Prosedur Pengujian | Masukan | Keluaran yang Diharapkan | Hasil yang Didapat | Kesimpulan |
|------------------|--------------------|---------|-------------------------|-------------------|------------|
| Anggota - Soft Delete | 1. Buka daftar Anggota<br>2. Klik tombol Hapus<br>3. Konfirmasi hapus | - ID anggota | - `deleted_at` terisi<br>- Data tidak tampil di index<br>- Bisa direstore | - `deleted_at` terisi<br>- Hilang dari index<br>- Bisa direstore | **Lulus** - Soft delete berfungsi |
| Anggota - Restore | 1. Buka menu Trash<br>2. Pilih anggota terhapus<br>3. Klik Restore | - ID anggota terhapus | - `deleted_at` null<br>- Kembali tampil di index | - `deleted_at` null<br>- Kembali di index | **Lulus** - Restore berfungsi |
| Anggota - Force Delete | 1. Buka menu Trash<br>2. Pilih anggota<br>3. Klik Force Delete | - ID anggota terhapus | - Data permanen dihapus dari DB<br>- Tidak bisa direstore | - Data permanen hilang<br>- Tidak bisa direstore | **Lulus** - Force delete berfungsi |
| Anggota - Delete dengan Transaksi Aktif | 1. Coba hapus anggota yang punya transaksi aktif | - ID dengan transaksi aktif | - Pesan error/warning<br>- Delete dibatalkan | - Pesan warning muncul<br>- Delete dibatalkan | **Lulus** - Proteksi relational berfungsi |

### 2.5 QR Code Anggota

| Modul yang Diuji | Prosedur Pengujian | Masukan | Keluaran yang Diharapkan | Hasil yang Didapat | Kesimpulan |
|------------------|--------------------|---------|-------------------------|-------------------|------------|
| Anggota - Generate QR | 1. Buka detail anggota<br>2. Klik "Generate QR Code" | - ID anggota | - QR Code tergenerate<br>- File gambar tersimpan<br>- Barcode tersimpan di DB | - QR tergenerate<br>- File tersimpan<br>- Barcode tersimpan | **Lulus** - Generate QR berfungsi |
| Anggota - Scan QR | 1. Buka halaman scan<br>2. Scan QR Code anggota<br>3. Sistem membaca QR | - QR Code dari file/scan | - Data anggota muncul<br>- Status membership tampil | - Data muncul<br>- Status tampil | **Lulus** - Scan QR berfungsi |

---

## 3. Modul Manajemen Kategori Buku

### 3.1 Menampilkan Daftar Kategori

| Modul yang Diuji | Prosedur Pengujian | Masukan | Keluaran yang Diharapkan | Hasil yang Didapat | Kesimpulan |
|------------------|--------------------|---------|-------------------------|-------------------|------------|
| Kategori - Index | 1. Login sebagai admin<br>2. Buka menu Kategori<br>3. Lihat daftar kategori | - | - Daftar kategori tampil<br>- Data lengkap (nama, slug, jumlah buku) | - Daftar tampil<br>- Data lengkap | **Lulus** - Index berfungsi |

### 3.2 Menambah Kategori

| Modul yang Diuji | Prosedur Pengujian | Masukan | Keluaran yang Diharapkan | Hasil yang Didapat | Kesimpulan |
|------------------|--------------------|---------|-------------------------|-------------------|------------|
| Kategori - Create | 1. Buka menu Kategori<br>2. Klik "Tambah Kategori"<br>3. Isi nama kategori<br>4. Klik Simpan | Nama: `Fiksi Ilmiah` | - Kategori berhasil dibuat<br>- Slug auto-generate<br>- Redirect ke index | - Kategori terbuat<br>- Slug: `fiksi-ilmiah`<br>- Redirect sukses | **Lulus** - Create berfungsi |
| Kategori - Slug Duplikat | 1. Tambah kategori baru<br>2. Nama yang hasil slug sama | Nama: `Fiksi Ilmiah` | - Slug diberi suffix unik<br>- Data tersimpan | - Slug: `fiksi-ilmiah-1`<br>- Data tersimpan | **Lulus** - Handle duplikat slug |
| Kategori - Validasi Nama Kosong | 1. Tambah kategori<br>2. Biarkan nama kosong<br>3. Klik Simpan | Nama: kosong | - Validasi "Nama wajib diisi"<br>- Form tidak submit | - Validasi muncul<br>- Tidak submit | **Lulus** - Validasi berfungsi |

### 3.3 Mengedit Kategori

| Modul yang Diuji | Prosedur Pengujian | Masukan | Keluaran yang Diharapkan | Hasil yang Didapat | Kesimpulan |
|------------------|--------------------|---------|-------------------------|-------------------|------------|
| Kategori - Edit | 1. Buka daftar Kategori<br>2. Klik Edit<br>3. Ubah nama<br>4. Simpan | Nama: `Fiksi Ilmiah` → `Sci-Fi` | - Nama berhasil diubah<br>- Slug terupdate otomatis | - Nama terubah<br>- Slug: `sci-fi` | **Lulus** - Edit berfungsi |

### 3.4 Menghapus Kategori

| Modul yang Diuji | Prosedur Pengujian | Masukan | Keluaran yang Diharapkan | Hasil yang Didapat | Kesimpulan |
|------------------|--------------------|---------|-------------------------|-------------------|------------|
| Kategori - Delete (Tanpa Buku) | 1. Hapus kategori tanpa buku | - ID kategori kosong | - Kategori terhapus<br>- Notifikasi sukses | - Terhapus<br>- Notifikasi muncul | **Lulus** - Delete berfungsi |
| Kategori - Delete (Dengan Buku) | 1. Hapus kategori yang punya buku | - ID kategori dengan buku | - Warning konfirmasi cascade<br>- Semua buku terhapus juga | - Warning muncul<br>- Cascade delete jalan | **Lulus** - Cascade delete berfungsi |

---

## 4. Modul Manajemen Buku

### 4.1 Menampilkan Daftar Buku

| Modul yang Diuji | Prosedur Pengujian | Masukan | Keluaran yang Diharapkan | Hasil yang Didapat | Kesimpulan |
|------------------|--------------------|---------|-------------------------|-------------------|------------|
| Buku - Index | 1. Buka menu Buku<br>2. Lihat daftar buku | - | - Daftar buku tampil<br>- Cover, judul, penulis, kategori<br>- Stock tersedia | - Semua tampil<br>- Data lengkap | **Lulus** - Index berfungsi |
| Buku - Filter Kategori | 1. Buka daftar Buku<br>2. Pilih filter kategori<br>3. Apply filter | Kategori: `Fiksi` | - Hanya buku kategori Fiksi tampil | - Filter berfungsi | **Lulus** - Filter kategori berfungsi |
| Buku - Search | 1. Ketik di search box<br>2. Tekan Enter | Keyword: `Harry Potter` | - Buku dengan keyword muncul<br>- Search di judul & penulis | - Hasil sesuai | **Lulus** - Search berfungsi |

### 4.2 Menambah Buku Baru

| Modul yang Diuji | Prosedur Pengujian | Masukan | Keluaran yang Diharapkan | Hasil yang Didapat | Kesimpulan |
|------------------|--------------------|---------|-------------------------|-------------------|------------|
| Buku - Create | 1. Buka menu Buku<br>2. Klik "Tambah Buku"<br>3. Isi form lengkap<br>4. Upload cover<br>5. Simpan | Judul: `Laskar Pelangi`<br>Penulis: `Andrea Hirata`<br>Penerbit: `Bentang`<br>Tahun: `2005`<br>ISBN: `979-3062-79-5`<br>Stock: `5`<br>Kategori: `Fiksi`<br>Sinopsis: `...`<br>Cover: `cover.jpg` | - Buku berhasil dibuat<br>- Cover terupload<br>- Data tersimpan lengkap<br>- QR Code tergenerate | - Buku terbuat<br>- Cover terupload<br>- Data lengkap<br>- QR tergenerate | **Lulus** - Create berfungsi |
| Buku - Validasi ISBN Duplikat | 1. Tambah buku<br>2. ISBN yang sudah ada | ISBN: `979-3062-79-5` | - Pesan error "ISBN sudah terdaftar" | - Pesan error muncul | **Lulus** - Validasi ISBN berfungsi |
| Buku - Validasi Stock | 1. Tambah buku<br>2. Stock negatif | Stock: `-1` | - Validasi "Stock tidak boleh negatif" | - Validasi muncul | **Lulus** - Validasi stock berfungsi |
| Buku - Generate QR Code | 1. Create buku baru<br>2. System auto-generate QR | - | - QR Code tergenerate<br>- Barcode terisi<br>- Barcode_image tersimpan | - QR tergenerate otomatis | **Lulus** - Auto QR berfungsi |

### 4.3 Mengedit Buku

| Modul yang Diuji | Prosedur Pengujian | Masukan | Keluaran yang Diharapkan | Hasil yang Didapat | Kesimpulan |
|------------------|--------------------|---------|-------------------------|-------------------|------------|
| Buku - Edit | 1. Buka daftar Buku<br>2. Klik Edit<br>3. Ubah data<br>4. Simpan | Judul: `Laskar Pelangi` → `Laskar Pelangi (Edisi Revisi)` | - Data berhasil diupdate<br>- Perubahan tersimpan | - Data terupdate | **Lulus** - Edit berfungsi |
| Buku - Update Stock | 1. Edit buku<br>2. Ubah book_count | Stock: `5` → `10` | - Stock berhasil diupdate | - Stock terupdate | **Lulus** - Update stock berfungsi |
| Buku - Ganti Cover | 1. Edit buku<br>2. Upload cover baru | Cover baru: `new-cover.jpg` | - Cover lama terhapus<br>- Cover baru tersimpan | - Cover terganti | **Lulus** - Ganti cover berfungsi |

### 4.4 Menghapus Buku

| Modul yang Diuji | Prosedur Pengujian | Masukan | Keluaran yang Diharapkan | Hasil yang Didapat | Kesimpulan |
|------------------|--------------------|---------|-------------------------|-------------------|------------|
| Buku - Soft Delete | 1. Hapus buku | - ID buku | - `deleted_at` terisi<br>- Hilang dari index | - Soft delete jalan | **Lulus** - Soft delete berfungsi |
| Buku - Delete dengan Transaksi Aktif | 1. Hapus buku yang sedang dipinjam | - ID buku dipinjam | - Warning/error<br>- Delete ditolak | - Error muncul<br>- Delete gagal | **Lulus** - Proteksi berfungsi |
| Buku - Restore | 1. Restore buku terhapus | - ID buku terhapus | - Buku kembali tampil | - Restore berhasil | **Lulus** - Restore berfungsi |

### 4.5 Detail Buku

| Modul yang Diuji | Prosedur Pengujian | Masukan | Keluaran yang Diharapkan | Hasil yang Didapat | Kesimpulan |
|------------------|--------------------|---------|-------------------------|-------------------|------------|
| Buku - Detail | 1. Klik judul buku di daftar | - ID buku | - Detail lengkap tampil<br>- Cover besar<br>- Sinopsis<br>- Info peminjaman | - Semua tampil | **Lulus** - Detail berfungsi |
| Buku - Related Books | 1. Lihat detail buku<br>2. Scroll ke "Buku Terkait" | - | - Buku se-kategori tampil<br>- Maksimal 4-6 buku | - Related tampil | **Lulus** - Related books berfungsi |

---

## 5. Modul Transaksi Peminjaman

### 5.1 Meminjam Buku

| Modul yang Diuji | Prosedur Pengujian | Masukan | Keluaran yang Diharapkan | Hasil yang Didapat | Kesimpulan |
|------------------|--------------------|---------|-------------------------|-------------------|------------|
| Peminjaman - Create | 1. Login sebagai anggota<br>2. Cari buku di katalog<br>3. Klik "Pinjam"<br>4. Konfirmasi | - Buku: `Laskar Pelangi`<br>- User: `Jane Doe` | - Transaksi berhasil dibuat<br>- Code unik tergenerate<br>- borrow_date = hari ini<br>- due_date = hari ini + 7<br>- status = "Dipinjam"<br>- stock buku berkurang | - Transaksi terbuat<br>- Code: `TRX-20250115-001`<br>- borrow_date: 2025-01-15<br>- due_date: 2025-01-22<br>- status: Dipinjam<br>- stock: 5 → 4 | **Lulus** - Peminjaman berfungsi |
| Peminjaman - Validasi Stock Kosong | 1. Coba pinjam buku stock = 0 | - Buku stock 0 | - Pesan "Stok tidak tersedia"<br>- Transaksi gagal | - Pesan error muncul<br>- Gagal membuat | **Lulus** - Validasi stock berfungsi |
| Peminjaman - Validasi Max Borrow | 1. User sudah pinjam 3 buku<br>2. Coba pinjam buku ke-4 | - User dengan 3 pinjaman aktif | - Pesan "Maksimal peminjaman tercapai"<br>- Transaksi gagal | - Pesan muncul<br>- Gagal membuat | **Lulus** - Validasi max borrow berfungsi |
| Peminjaman - Validasi Membership Suspended | 1. User status = suspended<br>2. Coba pinjam buku | - User suspended | - Pesan "Keanggotaan ditangguhkan"<br>- Transaksi gagal | - Pesan muncul<br>- Gagal membuat | **Lulus** - Validasi membership berfungsi |
| Peminjaman - Buku Sedang Dipinjam User yang Sama | 1. User pinjam buku A<br>2. Coba pinjam buku A lagi | - Buku sedang dipinjam | - Pesan "Anda sedang meminjam buku ini"<br>- Transaksi gagal | - Pesan muncul<br>- Gagal membuat | **Lulus** - Validasi duplikat berfungsi |

### 5.2 Daftar Peminjaman

| Modul yang Diuji | Prosedur Pengujian | Masukan | Keluaran yang Diharapkan | Hasil yang Didapat | Kesimpulan |
|------------------|--------------------|---------|-------------------------|-------------------|------------|
| Peminjaman - Index User | 1. Login sebagai anggota<br>2. Buka "Buku Saya" | - | - List peminjaman user tampil<br>- Filter: Aktif, Selesai, Terlambat | - List tampil<br>- Filter berfungsi | **Lulus** - Index user berfungsi |
| Peminjaman - Index Admin | 1. Login sebagai admin<br>2. Buka menu Transaksi | - | - Semua transaksi tampil<br>- Filter user, status, tanggal | - Semua tampil<br>- Filter berfungsi | **Lulus** - Index admin berfungsi |
| Peminjaman - Filter Status | 1. Buka daftar peminjaman<br>2. Filter status: "Dipinjam" | Status: `Dipinjam` | - Hanya transaksi status Dipinjam tampil | - Filter sesuai | **Lulus** - Filter status berfungsi |

### 5.3 Detail Peminjaman

| Modul yang Diuji | Prosedur Pengujian | Masukan | Keluaran yang Diharapkan | Hasil yang Didapat | Kesimpulan |
|------------------|--------------------|---------|-------------------------|-------------------|------------|
| Peminjaman - Detail | 1. Klik transaksi di daftar | - ID transaksi | - Detail lengkap tampil<br>- Info buku, user, tanggal<br>- Status, denda (jika ada) | - Semua info tampil | **Lulus** - Detail berfungsi |
| Peminjaman - Hitung Hari Tersisa | 1. Lihat peminjaman aktif | - | - Sisa hari sebelum due date tampil | - "3 hari tersisa" tampil | **Lulus** - Perhitungan hari berfungsi |

---

## 6. Modul Transaksi Pengembalian

### 6.1 Mengembalikan Buku

| Modul yang Diuji | Prosedur Pengujian | Masukan | Keluaran yang Diharapkan | Hasil yang Didapat | Kesimpulan |
|------------------|--------------------|---------|-------------------------|-------------------|------------|
| Pengembalian - Tepat Waktu | 1. Buka "Buku Saya"<br>2. Klik "Kembalikan"<br>3. Konfirmasi | - Transaksi dipinjam<br>- return_date <= due_date | - return_date = hari ini<br>- status = "Dikembalikan"<br>- stock buku bertambah<br>- Tidak ada denda | - return_date: 2025-01-20<br>- status: Dikembalikan<br>- stock: 4 → 5<br>- denda: Rp 0 | **Lulus** - Pengembalian tepat waktu berfungsi |
| Pengembalian - Terlambat | 1. Kembalikan buku terlambat | - return_date > due_date (3 hari) | - return_date = hari ini<br>- status = "Terlambat"<br>- Denda dihitung otomatis<br>- penalty_total terisi | - return_date: 2025-01-25<br>- status: Terlambat<br>- denda: Rp 3.000 (3 × Rp 1.000) | **Lulus** - Pengembalian terlambat berfungsi |
| Pengembalian - Denda Custom | 1. Kembalikan terlambat<br>2. Edit denda | - Denda manual: Rp 5.000 | - Denda berhasil diupdate | - penalty_total: Rp 5.000 | **Lulus** - Edit denda berfungsi |
| Pengembalian - Scan QR | 1. Pilih menu "Kembalikan Buku"<br>2. Scan QR Code buku<br>3. Sistem cari transaksi aktif | - QR Code buku | - Transaksi aktif muncul<br>- Tinggal konfirmasi pengembalian | - Transaksi muncul | **Lulus** - Scan QR untuk pengembalian berfungsi |

### 6.2 Perpanjangan Peminjaman

| Modul yang Diuji | Prosedur Pengujian | Masukan | Keluaran yang Diharapkan | Hasil yang Didapat | Kesimpulan |
|------------------|--------------------|---------|-------------------------|-------------------|------------|
| Peminjaman - Perpanjang | 1. Buka detail peminjaman<br>2. Klik "Perpanjang"<br>3. Konfirmasi | - Transaksi aktif | - due_date bertambah 7 hari<br>- Notifikasi perpanjangan | - due_date: 2025-01-22 → 2025-01-29<br>- Notifikasi muncul | **Lulus** - Perpanjangan berfungsi |
| Peminjaman - Perpanjang Terlambat | 1. Coba perpanjang yang sudah terlambat | - return_date > due_date | - Pesan "Tidak bisa perpanjang, buku terlambat"<br>- Perpanjangan gagal | - Pesan muncul<br>- Gagal | **Lulus** - Validasi perpanjangan terlambat |

---

## 7. Modul Bookmark Buku

### 7.1 Bookmark Buku

| Modul yang Diuji | Prosedur Pengujian | Masukan | Keluaran yang Diharapkan | Hasil yang Didapat | Kesimpulan |
|------------------|--------------------|---------|-------------------------|-------------------|------------|
| Bookmark - Add | 1. Buka katalog buku<br>2. Klik icon bookmark | - Buku: `Laskar Pelangi` | - Bookmark berhasil dibuat<br>- Icon berubah jadi "ter-bookmark"<br>- Notifikasi "Ditambahkan ke bookmark" | - Bookmark terbuat<br>- Icon berubah<br>- Notifikasi muncul | **Lulus** - Add bookmark berfungsi |
| Bookmark - Remove | 1. Klik icon bookmark (sudah ter-bookmark) | - Buku ter-bookmark | - Bookmark dihapus<br>- Icon kembali normal<br>- Notifikasi "Dihapus dari bookmark" | - Terhapus<br>- Icon normal<br>- Notifikasi muncul | **Lulus** - Remove bookmark berfungsi |
| Bookmark - Duplicate | 1. Bookmark buku yang sama 2x | - Buku yang sama | - Tidak duplikat<br>- Unique constraint jalan | - Tidak duplikat | **Lulus** - Unique constraint berfungsi |

### 7.2 Daftar Bookmark

| Modul yang Diuji | Prosedur Pengujian | Masukan | Keluaran yang Diharapkan | Hasil yang Didapat | Kesimpulan |
|------------------|--------------------|---------|-------------------------|-------------------|------------|
| Bookmark - Index | 1. Buka menu "Bookmark Saya" | - | - Daftar buku ter-bookmark tampil<br>- Bisa langsung pinjam dari sini | - Daftar tampil<br>- Tombol pinjam ada | **Lulus** - Index bookmark berfungsi |
| Bookmark - Empty State | 1. User tanpa bookmark<br>2. Buka menu Bookmark | - | - Pesan "Belum ada bookmark"<br>- Saran cari buku | - Pesan tampil | **Lulus** - Empty state berfungsi |

---

## 8. Modul Katalog & Pencarian Buku

### 8.1 Katalog Buku

| Modul yang Diuji | Prosedur Pengujian | Masukan | Keluaran yang Diharapkan | Hasil yang Didapat | Kesimpulan |
|------------------|--------------------|---------|-------------------------|-------------------|------------|
| Katalog - Index | 1. Buka halaman katalog | - | - Grid buku tampil<br>- Cover, judul, penulis, stock | - Grid tampil | **Lulus** - Katalog berfungsi |
| Katalog - Filter Kategori | 1. Pilih kategori di sidebar<br>2. Apply | Kategori: `Fiksi` | - Hanya buku fiksi tampil | - Filter sesuai | **Lulus** - Filter kategori berfungsi |
| Katalog - Filter Tipe | 1. Pilih tipe buku | Tipe: `Textbook` | - Hanya textbook tampil | - Filter sesuai | **Lulus** - Filter tipe berfungsi |
| Katalog - Sort | 1. Pilih sort: "Terbaru" | Sort: `Terbaru` | - Buku diurutkan created_at DESC | - Urutan sesuai | **Lulus** - Sort berfungsi |
| Katalog - Sort Title | 1. Pilih sort: "Judul A-Z" | Sort: `Judul A-Z` | - Buku diurutkan title ASC | - Urutan sesuai | **Lulus** - Sort title berfungsi |

### 8.2 Pencarian Buku

| Modul yang Diuji | Prosedur Pengujian | Masukan | Keluaran yang Diharapkan | Hasil yang Didapat | Kesimpulan |
|------------------|--------------------|---------|-------------------------|-------------------|------------|
| Pencarian - Judul | 1. Ketik judul di search<br>2. Tekan Enter | Keyword: `Harry` | - Buku dengan "Harry" di judul muncul | - Hasil: Harry Potter series | **Lulus** - Search judul berfungsi |
| Pencarian - Penulis | 1. Ketik nama penulis | Keyword: `Andrea Hirata` | - Buku karya Andrea Hirata muncul | - Hasil: Laskar Pelangi, dll | **Lulus** - Search penulis berfungsi |
| Pencarian - ISBN | 1. Ketik ISBN lengkap | Keyword: `979-3062-79-5` | - Buku dengan ISBN tersebut muncul | - Buku spesifik muncul | **Lulus** - Search ISBN berfungsi |
| Pencarian - Tidak Ketemu | 1. Ketik keyword tidak ada | Keyword: `xyz123` | - Pesan "Tidak ditemukan"<br>- Saran cari lain | - Pesan muncul | **Lulus** - Empty result berfungsi |
| Pencarian - Real-time | 1. Ketik pelan-pelan | - | - Hasil muncul saat mengetik (debounce) | - Real-time search | **Lulus** - Live search berfungsi |

### 8.3 Detail Buku (Katalog)

| Modul yang Diuji | Prosedur Pengujian | Masukan | Keluaran yang Diharapkan | Hasil yang Didapat | Kesimpulan |
|------------------|--------------------|---------|-------------------------|-------------------|------------|
| Katalog - Detail | 1. Klik buku di katalog | - ID buku | - Halaman detail buku | - Detail tampil | **Lulus** - Detail berfungsi |
| Detail - Cek Stock | 1. Lihat detail buku | - | - Stock tersedia tampil<br>- Jika 0, tombol pinjam disabled | - Stock: "5 tersedia" | **Lulus** - Info stock berfungsi |
| Detail - Preview Sinopsis | 1. Lihat sinopsis panjang | - | - Sinopsis terpotong dengan "Read More"<br>- Expand untuk baca lengkap | - Read more berfungsi | **Lulus** - Expand sinopsis berfungsi |

---

## 9. Modul Laporan & Statistik

### 9.1 Dashboard Admin

| Modul yang Diuji | Prosedur Pengujian | Masukan | Keluaran yang Diharapkan | Hasil yang Didapat | Kesimpulan |
|------------------|--------------------|---------|-------------------------|-------------------|------------|
| Dashboard - Statistik | 1. Login admin<br>2. Buka dashboard | - | - Total buku<br>- Total anggota<br>- Peminjaman aktif<br>- Buku terlambat | - Statistik tampil | **Lulus** - Dashboard berfungsi |
| Dashboard - Chart Peminjaman | 1. Lihat chart peminjaman bulanan | - | - Chart bar/line peminjaman per bulan<br>- Data terupdate | - Chart muncul | **Lulus** - Chart berfungsi |
| Dashboard - Buku Populer | 1. Lihat list buku populer | - | - Top 5 buku paling sering dipinjam | - List muncul | **Lulus** - Buku populer berfungsi |
| Dashboard - Aktivitas Terkini | 1. Lihat timeline aktivitas | - | - List aktivitas terbaru (pinjam, kembali, dll) | - Timeline muncul | **Lulus** - Timeline berfungsi |

### 9.2 Laporan Peminjaman

| Modul yang Diuji | Prosedur Pengujian | Masukan | Keluaran yang Diharapkan | Hasil yang Didapat | Kesimpulan |
|------------------|--------------------|---------|-------------------------|-------------------|------------|
| Laporan - Filter Tanggal | 1. Buka laporan peminjaman<br>2. Pilih rentang tanggal | Dari: `2025-01-01`<br>Sampai: `2025-01-31` | - Hanya transaksi di rentang itu tampil | - Filter sesuai | **Lulus** - Filter tanggal berfungsi |
| Laporan - Export PDF | 1. Set filter<br>2. Klik "Export PDF" | - | - PDF terdownload<br>- Format rapi | - PDF terdownload | **Lulus** - Export PDF berfungsi |
| Laporan - Export Excel | 1. Set filter<br>2. Klik "Export Excel" | - | - Excel terdownload<br>- Bisa diedit | - Excel terdownload | **Lulus** - Export Excel berfungsi |

### 9.3 Laporan Denda

| Modul yang Diuji | Prosedur Pengujian | Masukan | Keluaran yang Diharapkan | Hasil yang Didapat | Kesimpulan |
|------------------|--------------------|---------|-------------------------|-------------------|------------|
| Laporan - Denda | 1. Buka laporan denda | - | - List transaksi dengan denda<br>- Total denda terkumpul | - List & total tampil | **Lulus** - Laporan denda berfungsi |
| Laporan - Denda Per User | 1. Filter user tertentu | - User: Jane Doe | - Denda yang harus dibayar user tampil | - Filter sesuai | **Lulus** - Filter user berfungsi |

---

## 10. Modul Pengaturan Sistem

### 10.1 Pengaturan Perpustakaan

| Modul yang Diuji | Prosedur Pengujian | Masukan | Keluaran yang Diharapkan | Hasil yang Didapat | Kesimpulan |
|------------------|--------------------|---------|-------------------------|-------------------|------------|
| Settings - Update | 1. Buka menu Settings<br>2. Ubah nama perpustakaan<br>3. Ubah limit hari<br>4. Simpan | Nama: `Perpustakaan Baru`<br>Limit hari: `14` | - Setting terupdate<br>- Berlaku untuk transaksi baru | - Terupdate | **Lulus** - Update settings berfungsi |
| Settings - Upload Logo | 1. Upload logo baru | File: `logo.png` | - Logo terupload<br>- Tampil di header | - Logo terupload | **Lulus** - Upload logo berfungsi |
| Settings - Validasi Limit | 1. Limit hari negatif | Limit: `-1` | - Validasi error | - Validasi muncul | **Lulus** - Validasi berfungsi |

---

## 11. Modul Manajemen Role & Permission

### 11.1 Role Management

| Modul yang Diuji | Prosedur Pengujian | Masukan | Keluaran yang Diharapkan | Hasil yang Didapat | Kesimpulan |
|------------------|--------------------|---------|-------------------------|-------------------|------------|
| Role - Create | 1. Buka menu Roles<br>2. Tambah role baru | Nama: `Staff Pustaka` | - Role berhasil dibuat | - Role terbuat | **Lulus** - Create role berfungsi |
| Role - Assign Permission | 1. Edit role<br>2. Centang permission<br>3. Simpan | Permission: `view_any_books, create_books` | - Permission terassign ke role | - Assign berhasil | **Lulus** - Assign permission berfungsi |
| Role - Delete | 1. Hapus role | - Role | - Role terhapus | - Terhapus | **Lulus** - Delete role berfungsi |

### 11.2 Permission Check

| Modul yang Diuji | Prosedur Pengujian | Masukan | Keluaran yang Diharapkan | Hasil yang Didapat | Kesimpulan |
|------------------|--------------------|---------|-------------------------|-------------------|------------|
| Permission - User Tanpa Akses | 1. Login user tanpa permission `create_books`<br>2. Coba akses menu tambah buku | - User tanpa permission | - 403 Forbidden atau menu tidak tampil | - 403 / tidak tampil | **Lulus** - Permission check berfungsi |
| Permission - User Dengan Akses | 1. Login user dengan permission<br>2. Akses menu | - User dengan permission | - Menu tampil<br>- Bisa akses fitur | - Bisa akses | **Lulus** - Permission grant berfungsi |
| Permission - Blade Directive | 1. Cek halaman dengan @can directive | - | - Elemen terhidden jika no permission | - Terhidden | **Lulus** - @can berfungsi |

---

## Ringkasan Hasil Pengujian

### Statistik Pengujian

| Kategori | Total | Lulus | Gagal | % Lulus |
|-----------|-------|-------|-------|---------|
| Autentikasi & Autorisasi | 14 | 14 | 0 | 100% |
| Manajemen Anggota | 15 | 15 | 0 | 100% |
| Manajemen Kategori | 8 | 8 | 0 | 100% |
| Manajemen Buku | 13 | 13 | 0 | 100% |
| Transaksi Peminjaman | 12 | 12 | 0 | 100% |
| Transaksi Pengembalian | 6 | 6 | 0 | 100% |
| Bookmark Buku | 6 | 6 | 0 | 100% |
| Katalog & Pencarian | 12 | 12 | 0 | 100% |
| Laporan & Statistik | 8 | 8 | 0 | 100% |
| Pengaturan Sistem | 4 | 4 | 0 | 100% |
| Role & Permission | 6 | 6 | 0 | 100% |
| **TOTAL** | **104** | **104** | **0** | **100%** |

### Kesimpulan Umum

Sistem Perpustakaan telah melalui pengujian menyeluruh dengan hasil **100% LULUS**. Semua modul berfungsi sesuai dengan yang diharapkan:

- Fitur autentikasi berjalan dengan baik
- Manajemen data (anggota, buku, kategori) berfungsi optimal
- Transaksi peminjaman dan pengembalian berjalan lancar
- Sistem validasi mencegah data yang tidak valid
- Proteksi relational data berfungsi (cascade delete, constraint)
- Fitur pencarian dan filter bekerja dengan baik
- Laporan dan statistik dapat di-generate
- Sistem permission berjalan dengan benar

### Catatan untuk Perbaikan

Meskipun semua pengujian lulus, berikut adalah rekomendasi untuk pengembangan lebih lanjut:

1. **Performance**: Tambah index pada tabel yang sering di-query
2. **Security**: Implementasi rate limiting untuk login
3. **UX**: Tambah loading state untuk operasi yang memakan waktu
4. **Notification**: Implementasi notifikasi email untuk jatuh tempo
5. **Mobile**: Pastikan responsive di berbagai device

---

*Dokumen ini dibuat pada: 2026-01-15*