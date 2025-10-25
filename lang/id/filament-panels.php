<?php

return [
    // Auth
    'Login' => 'Masuk',
    'Register' => 'Daftar',
    'Logout' => 'Keluar',
    'Email Address' => 'Alamat Email',
    'Password' => 'Kata Sandi',
    'Remember me' => 'Ingat saya',
    'Forgot your password?' => 'Lupa kata sandi Anda?',
    'Reset Password' => 'Reset Kata Sandi',
    'Confirm Password' => 'Konfirmasi Kata Sandi',
    'Already registered?' => 'Sudah terdaftar?',
    "Don't have an account?" => 'Belum punya akun?',

    // Dashboard
    'Dashboard' => 'Dasbor',

    // Navigation
    'Navigation' => 'Navigasi',
    'Main Navigation' => 'Navigasi Utama',
    'User Menu' => 'Menu Pengguna',

    // Resources
    'Resources' => 'Sumber Daya',
    'Resource Management' => 'Manajemen Sumber Daya',

    // User Management
    'User Management' => 'Manajemen Pengguna',
    'Users' => 'Pengguna',
    'Roles' => 'Peran',
    'Permissions' => 'Izin',

    // Library
    'Library Management' => 'Manajemen Perpustakaan',
    'Books' => 'Buku',
    'Categories' => 'Kategori',
    'Transactions' => 'Transaksi',

    // Settings
    'Settings' => 'Pengaturan',
    'App Settings' => 'Pengaturan Aplikasi',
    'Profile Settings' => 'Pengaturan Profil',

    // Forms
    'Create' => 'Buat',
    'Edit' => 'Ubah',
    'Save' => 'Simpan',
    'Save & continue' => 'Simpan & lanjutkan',
    'Save & create another' => 'Simpan & buat lagi',
    'Cancel' => 'Batal',
    'Delete' => 'Hapus',
    'View' => 'Lihat',

    // Table
    'Table' => 'Tabel',
    'All' => 'Semua',
    'Trashed' => 'Terhapus',
    'Search' => 'Cari',
    'Filter' => 'Filter',
    'Clear filters' => 'Hapus filter',
    'Reset filters' => 'Reset filter',
    'Sort' => 'Urutkan',
    'Actions' => 'Aksi',
    'Bulk actions' => 'Aksi massal',
    'Select all' => 'Pilih semua',
    'Deselect all' => 'Batalkan semua pilihan',
    'No results found' => 'Tidak ada hasil ditemukan',
    'Showing' => 'Menampilkan',
    'to' => 'hingga',
    'of' => 'dari',
    'results' => 'hasil',
    'per page' => 'per halaman',

    // Profile
    'Profile' => 'Profil',
    'Edit Profile' => 'Ubah Profil',
    'Name' => 'Nama',
    'Email' => 'Email',
    'Update Password' => 'Perbarui Kata Sandi',
    'Current Password' => 'Kata Sandi Saat Ini',
    'New Password' => 'Kata Sandi Baru',
    'Confirm New Password' => 'Konfirmasi Kata Sandi Baru',

    // Notifications
    'Notifications' => 'Notifikasi',
    'Mark all as read' => 'Tandai semua sebagai dibaca',
    'No new notifications' => 'Tidak ada notifikasi baru',
    'You have :count new notifications' => 'Anda memiliki :count notifikasi baru',

    // Breadcrumbs
    'Home' => 'Beranda',
    'Dashboard' => 'Dasbor',
    'Administration' => 'Administrasi',

    // Empty States
    'empty state' => [
        'heading' => 'Tidak ada :resource ditemukan',
        'description' => 'Belum ada :resource yang dibuat. Mulai dengan membuat :resource baru.',
        'actions' => [
            'create' => 'Buat :resource pertama',
        ],
    ],

    // Authentication messages
    'auth' => [
        'failed' => 'Kredensial ini tidak cocok dengan data kami.',
        'throttle' => 'Terlalu banyak percobaan login. Silakan coba lagi dalam :seconds detik.',
        'password' => 'Kata sandi yang diberikan salah.',
        'email' => 'Tidak dapat menemukan pengguna dengan alamat email tersebut.',
    ],

    // Validation messages
    'validation' => [
        'required' => 'Field ini wajib diisi.',
        'email' => 'Harap berikan alamat email yang valid.',
        'unique' => ':attribute sudah ada.',
        'confirmed' => 'Konfirmasi :attribute tidak cocok.',
        'min' => ':attribute harus minimal :min karakter.',
        'max' => ':attribute tidak boleh lebih dari :max karakter.',
    ],

    // Status messages
    'status' => [
        'active' => 'Aktif',
        'inactive' => 'Tidak Aktif',
        'pending' => 'Menunggu',
        'approved' => 'Disetujui',
        'rejected' => 'Ditolak',
        'completed' => 'Selesai',
        'cancelled' => 'Dibatalkan',
    ],

    // Success messages
    'success' => [
        'created' => ':resource berhasil dibuat.',
        'updated' => ':resource berhasil diperbarui.',
        'deleted' => ':resource berhasil dihapus.',
        'restored' => ':resource berhasil dipulihkan.',
        'saved' => 'Perubahan berhasil disimpan.',
    ],

    // Error messages
    'error' => [
        'general' => 'Terjadi kesalahan. Silakan coba lagi.',
        'permission_denied' => 'Anda tidak memiliki izin untuk melakukan tindakan ini.',
        'not_found' => ':resource tidak ditemukan.',
        'validation_failed' => 'Validasi gagal. Periksa kembali input Anda.',
    ],

    // Confirmations
    'confirm' => [
        'delete' => 'Apakah Anda yakin ingin menghapus :resource ini?',
        'delete_bulk' => 'Apakah Anda yakin ingin menghapus :count :resource yang dipilih?',
        'restore' => 'Apakah Anda yakin ingin memulihkan :resource ini?',
        'force_delete' => 'Apakah Anda yakin ingin menghapus :resource ini secara permanen?',
    ],

    // Actions buttons
    'actions' => [
        'create' => 'Buat :resource',
        'edit' => 'Ubah :resource',
        'view' => 'Lihat :resource',
        'delete' => 'Hapus :resource',
        'restore' => 'Pulihkan :resource',
        'force_delete' => 'Hapus Permanen :resource',
        'replicate' => 'Duplikat :resource',
    ],

    // Filters
    'filters' => [
        'select' => 'Pilih...',
        'clear' => 'Hapus',
        'apply' => 'Terapkan',
        'date_range' => 'Rentang Tanggal',
        'from' => 'Dari',
        'to' => 'Hingga',
    ],

    // Search
    'search' => [
        'placeholder' => 'Cari :resource...',
        'no_results' => 'Tidak ada hasil ditemukan untuk ":query"',
        'clear' => 'Hapus pencarian',
    ],

    // Pagination
    'pagination' => [
        'previous' => 'Sebelumnya',
        'next' => 'Selanjutnya',
        'first' => 'Pertama',
        'last' => 'Terakhir',
        'page' => 'Halaman',
        'of' => 'dari',
        'showing' => 'Menampilkan',
        'results' => 'hasil',
    ],

    // Sidebar
    'sidebar' => [
        'collapse' => 'Sembunyikan sidebar',
        'expand' => 'Perluas sidebar',
        'navigation' => 'Navigasi',
        'search' => 'Cari...',
    ],

    // Header
    'header' => [
        'search' => 'Cari...',
        'notifications' => 'Notifikasi',
        'profile' => 'Profil',
        'logout' => 'Keluar',
        'theme' => 'Tema',
        'language' => 'Bahasa',
    ],

    // Dark/Light mode
    'theme' => [
        'light' => 'Terang',
        'dark' => 'Gelap',
        'system' => 'Sistem',
    ],
];
