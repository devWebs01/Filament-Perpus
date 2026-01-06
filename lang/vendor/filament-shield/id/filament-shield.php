<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Filament Shield - Role Labels
    |--------------------------------------------------------------------------
    |
    | Translation untuk role labels yang ditampilkan di UI
    |
    */

    'resource_permission_prefixes_labels' => [
        // Role Labels
        'super_admin' => 'Kepala Perpustakaan',
        'petugas' => 'Petugas',
        'siswa' => 'Siswa',

        // Permission Prefixes - Resource
        'book' => 'Buku',
        'category' => 'Kategori',
        'transaction' => 'Transaksi',
        'user' => 'Pengguna',
        'shelf' => 'Rak',
        'bookmark' => 'Bookmark',
        'status' => 'Status',

        // Permission Actions
        'view' => 'Lihat',
        'view_any' => 'Lihat Semua',
        'create' => 'Buat',
        'update' => 'Update',
        'delete' => 'Hapus',
        'delete_any' => 'Hapus Semua',
        'restore' => 'Pulihkan',
        'restore_any' => 'Pulihkan Semua',
        'force_delete' => 'Hapus Permanen',
        'force_delete_any' => 'Hapus Permanen Semua',
        'replicate' => 'Duplikat',
        'reorder' => 'Urutkan Ulang',
    ],

    // Role Resource
    'role' => [
        'label' => 'Role',
        'plural_label' => 'Roles',
    ],
];
