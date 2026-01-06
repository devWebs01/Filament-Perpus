<?php

namespace App\Services;

use App\Models\Book;
use App\Models\Setting;
use App\Models\Status;
use App\Models\Transaction;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

/**
 * BorrowBookService
 *
 * Service untuk menangani business logic peminjaman buku
 * oleh user melalui frontend/guest area.
 */
class BorrowBookService
{
    protected int $defaultBorrowDays = 7;

    public function __construct()
    {
        $setting = Setting::first();
        $this->defaultBorrowDays = (int) ($setting->limit_day ?? config('app.library.default_loan_days', 7));
    }

    /**
     * Validasi kelayakan peminjaman buku
     *
     * @param  Book  $book  Buku yang akan dipinjam
     * @return array{is_valid: bool, errors: array}
     */
    public function validateBorrowEligibility(Book $book): array
    {
        $errors = [];

        // Cek stok buku
        if ($book->book_count <= 0) {
            $errors[] = 'Stok buku tidak tersedia.';
        }

        // Cek apakah user sudah login
        if (! auth()->check()) {
            $errors[] = 'Anda harus login terlebih dahulu.';
        }

        $user = auth()->user();

        // Cek kelengkapan profile user
        $userDetail = $user->userDetail;
        if (! $userDetail || ! $userDetail->nis || ! $userDetail->phone_number) {
            $errors[] = 'Lengkapi data profil Anda terlebih dahulu (NIS dan Nomor Telepon).';
        }

        // Cek status keanggotaan
        if ($userDetail && ! $userDetail->isMembershipActive()) {
            $errors[] = 'Keanggotaan Anda tidak aktif.';
        }

        // Cek apakah user memiliki buku yang terlambat
        $overdueCount = Transaction::where('user_id', $user->id)
            ->whereHas('status', function ($query) {
                $query->where('name', 'Dipinjam');
            })
            ->where('due_date', '<', now())
            ->count();

        if ($overdueCount > 0) {
            $errors[] = 'Anda memiliki buku yang terlambat. Kembalikan terlebih dahulu sebelum meminjam buku baru.';
        }

        // Cek batas maksimal peminjaman
        $activeBorrows = Transaction::where('user_id', $user->id)
            ->whereHas('status', function ($query) {
                $query->whereIn('name', ['Menunggu Persetujuan', 'Dipinjam']);
            })
            ->count();

        if ($activeBorrows >= 3) {
            $errors[] = 'Anda sudah mencapai batas maksimal peminjaman (3 buku).';
        }

        // Cek apakah user sudah meminjam buku yang sama
        $existingBorrow = Transaction::where('user_id', $user->id)
            ->where('book_id', $book->id)
            ->whereHas('status', function ($query) {
                $query->whereIn('name', ['Menunggu Persetujuan', 'Dipinjam']);
            })
            ->first();

        if ($existingBorrow) {
            $errors[] = 'Anda sedang meminjam atau memiliki permintaan untuk buku ini.';
        }

        return [
            'is_valid' => empty($errors),
            'errors' => $errors,
        ];
    }

    /**
     * Proses peminjaman buku
     *
     * @param  Book  $book  Buku yang akan dipinjam
     * @return array{success: bool, message: string, data: array}
     */
    public function processBorrow(Book $book): array
    {
        return DB::transaction(function () use ($book) {
            $user = auth()->user();

            // Validasi eligibility
            $validation = $this->validateBorrowEligibility($book);
            if (! $validation['is_valid']) {
                return [
                    'success' => false,
                    'message' => implode('. ', $validation['errors']),
                ];
            }

            // Cek lagi stok sebelum create (race condition prevention)
            $book->refresh();
            if ($book->book_count <= 0) {
                return [
                    'success' => false,
                    'message' => 'Maaf, stok buku baru saja habis.',
                ];
            }

            // Ambil status "Menunggu Persetujuan"
            $pendingStatus = Status::where('name', 'Menunggu Persetujuan')->first();

            if (! $pendingStatus) {
                return [
                    'success' => false,
                    'message' => 'Status "Menunggu Persetujuan" tidak ditemukan. Hubungi admin.',
                ];
            }

            // Buat transaksi peminjaman
            $transaction = new Transaction;
            $transaction->user_id = $user->id;
            $transaction->book_id = $book->id;
            $transaction->borrow_date = now();
            $transaction->due_date = now()->addDays($this->defaultBorrowDays);
            $transaction->status_id = $pendingStatus->id;
            $transaction->save();

            Log::info('Borrow request created', [
                'transaction_id' => $transaction->id,
                'user_id' => $user->id,
                'book_id' => $book->id,
            ]);

            return [
                'success' => true,
                'message' => 'Permintaan peminjaman berhasil dibuat. Silakan tunggu konfirmasi dari admin.',
                'data' => [
                    'transaction_id' => $transaction->id,
                    'remaining_stock' => $book->book_count,
                ],
            ];
        });
    }

    /**
     * Batalkan permintaan peminjaman
     *
     * @param  Transaction  $transaction  Transaksi yang akan dibatalkan
     * @return array{success: bool, message: string}
     */
    public function cancelBorrow(Transaction $transaction): array
    {
        // Validasi kepemilikan
        if ($transaction->user_id !== auth()->id()) {
            return [
                'success' => false,
                'message' => 'Anda tidak memiliki izin untuk membatalkan peminjaman ini.',
            ];
        }

        // Hanya bisa batalkan jika status "Menunggu Persetujuan"
        $pendingStatus = Status::where('name', 'Menunggu Persetujuan')->first();
        if ($transaction->status_id !== $pendingStatus?->id) {
            return [
                'success' => false,
                'message' => 'Hanya permintaan yang belum disetujui yang bisa dibatalkan.',
            ];
        }

        // Update status ke "Dibatalkan"
        $cancelledStatus = Status::where('name', 'Dibatalkan')->first();
        $transaction->status_id = $cancelledStatus?->id;
        $transaction->save();

        Log::info('Borrow request cancelled by user', [
            'transaction_id' => $transaction->id,
            'user_id' => auth()->id(),
        ]);

        return [
            'success' => true,
            'message' => 'Permintaan peminjaman berhasil dibatalkan.',
        ];
    }
}
