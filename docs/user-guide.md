# User Guide

## Fitur Utama

### 📚 Manajemen Buku
- **Katalog Lengkap**: Simpan detail ISBN, penulis, penerbit, tahun, dan sinopsis.
- **Barcode Generator**: Otomatis generate barcode Code128 untuk setiap buku.
- **Stock Management**: Lacak jumlah eksemplar buku yang tersedia, dipinjam, atau rusak.
- **Kategorisasi**: Kelompokkan buku berdasarkan kategori (Fiksi, Sains, Sejarah, dll).
- **Cover Image**: Upload gambar sampul buku untuk tampilan katalog yang menarik.

### 👥 Manajemen Anggota
- **Profil Lengkap**: Data NIS/NISN, kelas, alamat, kontak, dan foto profil.
- **Kartu Anggota Digital**: QR Code otomatis untuk setiap anggota yang bisa dicetak.
- **Riwayat Peminjaman**: Lacak riwayat buku yang pernah dipinjam anggota.
- **Status Keanggotaan**: Aktifkan atau nonaktifkan anggota dengan mudah.

### 🔄 Transaksi Peminjaman
- **Alur Persetujuan**: Peminjaman oleh siswa masuk status "Menunggu Persetujuan" admin.
- **Pengembalian Fleksibel**:
  - ✅ **Dikembalikan**: Buku kembali dalam kondisi baik.
  - ⚠️ **Rusak Ringan**: Denda Rp 5.000.
  - ⛔ **Rusak Berat**: Denda Rp 10.000.
  - ❌ **Hilang**: Denda Rp 50.000.
- **Denda Otomatis**: Menghitung denda keterlambatan Rp 500/hari (configurable).

### 🔔 Notifikasi & Laporan
- **Email Reminder**: Notifikasi otomatis untuk anggota yang terlambat mengembalikan buku.
- **Dashboard Admin**: Statistik real-time (Total Buku, Peminjaman Aktif, Keterlambatan).
- **Export Data**: Unduh laporan buku, anggota, dan transaksi ke Excel/CSV.

---

## Alur Penggunaan

### 1. Pendaftaran Anggota
1. Admin masuk ke menu **Users**.
2. Klik **New User** atau import data siswa.
3. Lengkapi data (Nama, Email, Role).
4. QR Code kartu anggota akan otomatis ter-generate.

### 2. Menambah Koleksi Buku
1. Masuk ke menu **Books**.
2. Klik **New Book**.
3. Isi detail buku (Judul, Penulis, Stok).
4. Upload cover dan simpan. Barcode akan otomatis dibuat.

### 3. Proses Peminjaman
1. **Request**: Siswa login, cari buku, dan klik **Pinjam**. Status: `Menunggu Persetujuan`.
2. **Approval**: Admin cek menu **Transactions**, verifikasi stok dan syarat, lalu ubah status ke `Dipinjam`.

### 4. Proses Pengembalian
1. Anggota membawa buku ke petugas.
2. Admin cari transaksi (bisa scan barcode buku/user).
3. Admin klik **Kembalikan**.
4. Pilih kondisi buku (Baik/Rusak/Hilang).
5. Sistem hitung total denda (terlambat + kerusakan).
6. Transaksi selesai.

---

## Command Kustom

### Cek Keterlambatan & Kirim Notifikasi
Jalankan command ini (biasanya via scheduler) untuk mengirim email ke peminjam yang terlambat.
```bash
# Cek dan kirim notifikasi
php artisan books:check-overdue

# Mode simulasi (tanpa kirim email)
php artisan books:check-overdue --dry-run
```

### Re-generate QR Codes
Jika gambar QR Code hilang atau corropt, generate ulang dengan:
```bash
php artisan lib:regenerate-qr-codes
```

---

## Troubleshooting Umum

### Tidak bisa upload gambar?
Pastikan folder storage sudah di-link dan memiliki permission yang benar.
```bash
php artisan storage:link
chmod -R 775 storage
```

### Email notifikasi tidak masuk?
1. Cek konfigurasi `.env` bagian `MAIL_*`.
2. Pastikan queue worker berjalan: `php artisan queue:work`.
3. Untuk local dev, gunakan mailhog atau log driver.

### Barcode tidak muncul?
Pastikan library `GD` extension di PHP sudah aktif. Cek dengan `php -m | grep gd`.
