<?php

namespace App\Services;

use App\Models\Book;
use App\Models\UserDetail;

/**
 * BarcodeScannerService
 *
 * Service untuk menangani scanning barcode pada sistem perpustakaan.
 * Mendukung barcode untuk kartu anggota (User) dan buku (Book).
 */
class BarcodeScannerService
{
    /**
     * Cari user berdasarkan barcode (qr_code dari UserDetail)
     *
     * @param  string  $barcode  Barcode yang discan
     */
    public function findUserByBarcode(string $barcode): ?UserDetail
    {
        // Cari berdasarkan qr_code di user_details
        $userDetail = UserDetail::where('qr_code', $barcode)->first();

        if ($userDetail) {
            return $userDetail;
        }

        // Fallback: coba cari berdasarkan user_id jika barcode langsung berisi ID
        if (is_numeric($barcode)) {
            return UserDetail::where('user_id', (int) $barcode)->first();
        }

        // Fallback: coba cari user berdasarkan NIS/NISN
        $userDetail = UserDetail::where('nis', $barcode)
            ->orWhere('nisn', $barcode)
            ->first();

        return $userDetail;
    }

    /**
     * Cari buku berdasarkan barcode atau ISBN
     *
     * @param  string  $barcode  Barcode yang discan
     */
    public function findBookByBarcode(string $barcode): ?Book
    {
        return Book::where('barcode', $barcode)
            ->orWhere('isbn', $barcode)
            ->first();
    }

    /**
     * Validasi format barcode user
     *
     * @return array{valid: bool, type: string|null, message: string}
     */
    public function validateUserBarcode(string $barcode): array
    {
        if (empty($barcode)) {
            return [
                'valid' => false,
                'type' => null,
                'message' => 'Barcode tidak boleh kosong',
            ];
        }

        // Cek format USR + 12 karakter
        if (preg_match('/^USR[A-Z0-9]{12}$/', $barcode)) {
            return [
                'valid' => true,
                'type' => 'qr_code',
                'message' => 'Format QR Code valid',
            ];
        }

        // Cek jika numeric (user_id)
        if (is_numeric($barcode)) {
            return [
                'valid' => true,
                'type' => 'user_id',
                'message' => 'Format User ID valid',
            ];
        }

        // Cek jika NIS/NISN (numeric atau alphanumeric)
        if (preg_match('/^[A-Z0-9]{5,20}$/', strtoupper($barcode))) {
            return [
                'valid' => true,
                'type' => 'nis_nisn',
                'message' => 'Format NIS/NISN valid',
            ];
        }

        return [
            'valid' => false,
            'type' => null,
            'message' => 'Format barcode tidak dikenali',
        ];
    }

    /**
     * Validasi format barcode buku
     *
     * @return array{valid: bool, type: string|null, message: string}
     */
    public function validateBookBarcode(string $barcode): array
    {
        if (empty($barcode)) {
            return [
                'valid' => false,
                'type' => null,
                'message' => 'Barcode tidak boleh kosong',
            ];
        }

        // Cek format BOK + 12 karakter
        if (preg_match('/^BOK[A-Z0-9]{12}$/', $barcode)) {
            return [
                'valid' => true,
                'type' => 'barcode',
                'message' => 'Format Barcode valid',
            ];
        }

        // Cek jika ISBN (10 atau 13 digit)
        if (preg_match('/^\d{10}(\d{3})?$/', $barcode)) {
            return [
                'valid' => true,
                'type' => 'isbn',
                'message' => 'Format ISBN valid',
            ];
        }

        return [
            'valid' => false,
            'type' => null,
            'message' => 'Format barcode buku tidak dikenali',
        ];
    }

    /**
     * Scan barcode user dan return data lengkap
     *
     * @return array{success: bool, user: UserDetail|null, message: string}
     */
    public function scanUserBarcode(string $barcode): array
    {
        $validation = $this->validateUserBarcode($barcode);

        if (! $validation['valid']) {
            return [
                'success' => false,
                'user' => null,
                'message' => $validation['message'],
            ];
        }

        $userDetail = $this->findUserByBarcode($barcode);

        if (! $userDetail instanceof \App\Models\UserDetail) {
            return [
                'success' => false,
                'user' => null,
                'message' => 'User tidak ditemukan dengan barcode: '.$barcode,
            ];
        }

        return [
            'success' => true,
            'user' => $userDetail,
            'message' => 'User ditemukan: '.$userDetail->user->name,
        ];
    }

    /**
     * Scan barcode buku dan return data lengkap
     *
     * @return array{success: bool, book: Book|null, message: string, available: bool}
     */
    public function scanBookBarcode(string $barcode): array
    {
        $validation = $this->validateBookBarcode($barcode);

        if (! $validation['valid']) {
            return [
                'success' => false,
                'book' => null,
                'message' => $validation['message'],
                'available' => false,
            ];
        }

        $book = $this->findBookByBarcode($barcode);

        if (! $book instanceof \App\Models\Book) {
            return [
                'success' => false,
                'book' => null,
                'message' => 'Buku tidak ditemukan dengan barcode: '.$barcode,
                'available' => false,
            ];
        }

        return [
            'success' => true,
            'book' => $book,
            'message' => 'Buku ditemukan: '.$book->title,
            'available' => $book->isAvailable(),
        ];
    }

    /**
     * Cek ketersediaan buku untuk dipinjam
     *
     * @return array{available: bool, message: string, available_count: int}
     */
    public function checkBookAvailability(Book $book): array
    {
        $availableCount = $book->getAvailableCount();

        if ($availableCount <= 0) {
            return [
                'available' => false,
                'message' => 'Buku tidak tersedia (stok habis)',
                'available_count' => 0,
            ];
        }

        return [
            'available' => true,
            'message' => "Buku tersedia ({$availableCount} eksemplar)",
            'available_count' => $availableCount,
        ];
    }

    /**
     * Cek apakah user boleh meminjam buku
     *
     * @return array{allowed: bool, message: string, current_borrows: int, max_borrows: int}
     */
    public function checkUserBorrowEligibility(UserDetail $userDetail): array
    {
        $pendingStatus = \App\Models\Status::where('name', 'Menunggu Persetujuan')->first()?->id;
        $borrowedStatus = \App\Models\Status::where('name', 'Dipinjam')->first()?->id;
        $overdueStatus = \App\Models\Status::where('name', 'Terlambat')->first()?->id;

        $activeBorrows = \App\Models\Transaction::where('user_id', $userDetail->user_id)
            ->whereIn('status_id', [$borrowedStatus, $overdueStatus])
            ->count();

        \App\Models\Transaction::where('user_id', $userDetail->user_id)
            ->where('status_id', $pendingStatus)
            ->count();

        $setting = \App\Models\Setting::first();
        $maxBorrow = (int) ($setting->max_borrow ?? 3);

        if ($activeBorrows >= $maxBorrow) {
            return [
                'allowed' => false,
                'message' => "User telah mencapai batas maksimal peminjaman ({$maxBorrow} buku)",
                'current_borrows' => $activeBorrows,
                'max_borrows' => $maxBorrow,
            ];
        }

        return [
            'allowed' => true,
            'message' => 'User boleh meminjam buku',
            'current_borrows' => $activeBorrows,
            'max_borrows' => $maxBorrow,
        ];
    }

    /**
     * Parse data input barcode untuk menentukan tipe
     *
     * @return array{type: 'user'|'book'|'unknown', confidence: int}
     */
    public function parseBarcodeType(string $barcode): array
    {
        // Prefix USR = User
        if (str_starts_with(strtoupper($barcode), 'USR')) {
            return ['type' => 'user', 'confidence' => 100];
        }

        // Prefix BOK = Buku
        if (str_starts_with(strtoupper($barcode), 'BOK')) {
            return ['type' => 'book', 'confidence' => 100];
        }

        // ISBN 10/13 digit = Buku
        if (preg_match('/^\d{10}(\d{3})?$/', $barcode)) {
            return ['type' => 'book', 'confidence' => 90];
        }

        // NIS/NISN (5-20 alphanumeric) = User
        if (preg_match('/^[A-Z0-9]{5,20}$/', strtoupper($barcode))) {
            return ['type' => 'user', 'confidence' => 70];
        }

        return ['type' => 'unknown', 'confidence' => 0];
    }
}
