<?php

namespace App\Services;

use App\Models\Book;
use App\Models\Setting;
use App\Models\Status;
use App\Models\Transaction;
use App\Models\UserDetail;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

/**
 * TransactionService
 *
 * Service untuk menangani business logic transaksi peminjaman
 * dan pengembalian buku perpustakaan.
 */
class TransactionService
{
    protected int $defaultBorrowDays = 7;

    protected int $penaltyPerDay = 1000;

    public function __construct(protected BarcodeScannerService $barcodeScanner)
    {
        $setting = Setting::first();
        $this->defaultBorrowDays = (int) ($setting->borrow_days ?? 7);
        $this->penaltyPerDay = (int) ($setting->penalty_per_day ?? 1000);
    }

    /**
     * Buat permintaan peminjaman baru (oleh user)
     *
     * @param  int  $userId  ID user yang meminjam
     * @param  int  $bookId  ID buku yang dipinjam
     * @return array{success: bool, transaction: Transaction|null, message: string}
     */
    public function createBorrowRequest(int $userId, int $bookId): array
    {
        return DB::transaction(function () use ($userId, $bookId): array {
            $user = UserDetail::where('user_id', $userId)->first();
            $book = Book::find($bookId);

            if (! $user) {
                return [
                    'success' => false,
                    'transaction' => null,
                    'message' => 'User tidak ditemukan',
                ];
            }

            if (! $book) {
                return [
                    'success' => false,
                    'transaction' => null,
                    'message' => 'Buku tidak ditemukan',
                ];
            }

            // Cek status user
            if (! $user->isMembershipActive()) {
                return [
                    'success' => false,
                    'transaction' => null,
                    'message' => 'Keanggotaan user tidak aktif',
                ];
            }

            // Cek eligibility
            $eligibility = $this->barcodeScanner->checkUserBorrowEligibility($user);
            if (! $eligibility['allowed']) {
                return [
                    'success' => false,
                    'transaction' => null,
                    'message' => $eligibility['message'],
                ];
            }

            // Cek stok buku
            $availability = $this->barcodeScanner->checkBookAvailability($book);
            if (! $availability['available']) {
                return [
                    'success' => false,
                    'transaction' => null,
                    'message' => $availability['message'],
                ];
            }

            // Cek apakah user sudah memiliki pending request untuk buku ini
            $pendingStatus = Status::where('name', 'Menunggu Persetujuan')->first();
            $existingRequest = Transaction::where('user_id', $userId)
                ->where('book_id', $bookId)
                ->where('status_id', $pendingStatus?->id)
                ->first();

            if ($existingRequest) {
                return [
                    'success' => false,
                    'transaction' => null,
                    'message' => 'Anda sudah memiliki permintaan peminjaman untuk buku ini',
                ];
            }

            // Buat transaction dengan status "Menunggu Persetujuan"
            $transaction = new Transaction;
            $transaction->user_id = $userId;
            $transaction->book_id = $bookId;
            $transaction->borrow_date = now();
            $transaction->due_date = now()->addDays($this->defaultBorrowDays);
            $transaction->status_id = $pendingStatus?->id;
            $transaction->save();

            Log::info('Borrow request created', [
                'transaction_id' => $transaction->id,
                'user_id' => $userId,
                'book_id' => $bookId,
            ]);

            return [
                'success' => true,
                'transaction' => $transaction,
                'message' => 'Permintaan peminjaman berhasil dibuat. Menunggu konfirmasi admin.',
            ];
        });
    }

    /**
     * Konfirmasi peminjaman (oleh admin)
     *
     * @param  int  $transactionId  ID transaction
     * @return array{success: bool, message: string}
     */
    public function confirmBorrow(int $transactionId): array
    {
        return DB::transaction(function () use ($transactionId): array {
            $transaction = Transaction::with(['book', 'user'])->find($transactionId);

            if (! $transaction) {
                return [
                    'success' => false,
                    'message' => 'Transaksi tidak ditemukan',
                ];
            }

            // Hanya bisa confirm jika status "Menunggu Persetujuan"
            $pendingStatus = Status::where('name', 'Menunggu Persetujuan')->first();
            if ($transaction->status_id !== $pendingStatus?->id) {
                return [
                    'success' => false,
                    'message' => 'Transaksi tidak dalam status menunggu persetujuan',
                ];
            }

            // Cek lagi eligibility dan availability saat konfirmasi
            /** @var UserDetail|null $user */
            $user = $transaction->user->userDetail;
            $eligibility = $this->barcodeScanner->checkUserBorrowEligibility($user);

            if (! $eligibility['allowed']) {
                return [
                    'success' => false,
                    'message' => $eligibility['message'],
                ];
            }

            /** @var Book $book */
            $book = $transaction->book;
            $availability = $this->barcodeScanner->checkBookAvailability($book);
            if (! $availability['available']) {
                return [
                    'success' => false,
                    'message' => $availability['message'],
                ];
            }

            // Update status ke "Dipinjam"
            $borrowedStatus = Status::where('name', 'Dipinjam')->first();
            $transaction->status_id = $borrowedStatus?->id;
            $transaction->save();

            Log::info('Borrow confirmed', [
                'transaction_id' => $transaction->id,
                'user_id' => $transaction->user_id,
                'book_id' => $transaction->book_id,
            ]);

            return [
                'success' => true,
                'message' => 'Peminjaman berhasil dikonfirmasi',
            ];
        });
    }

    /**
     * Proses pengembalian buku
     *
     * @param  int  $transactionId  ID transaction
     * @param  array{return_status_id?: int, fine_amount?: int, notes?: string|null}  $data  Data pengembalian
     * @return array{success: bool, message: string, penalty: int, days_overdue: int}
     */
    public function returnBook(int $transactionId, array $data = []): array
    {
        return DB::transaction(function () use ($transactionId, $data): array {
            $transaction = Transaction::find($transactionId);

            if (! $transaction) {
                return [
                    'success' => false,
                    'message' => 'Transaksi tidak ditemukan',
                    'penalty' => 0,
                    'days_overdue' => 0,
                ];
            }

            // Cek apakah buku sedang dipinjam
            $borrowedStatus = Status::where('name', 'Dipinjam')->first();
            $overdueStatus = Status::where('name', 'Terlambat')->first();

            if (! in_array($transaction->status_id, [$borrowedStatus?->id, $overdueStatus?->id])) {
                return [
                    'success' => false,
                    'message' => 'Buku tidak sedang dipinjam',
                    'penalty' => 0,
                    'days_overdue' => 0,
                ];
            }

            // Jika ada data custom (dari form modal)
            if (isset($data['return_status_id'])) {
                $returnStatus = Status::find($data['return_status_id']);
                $fineAmount = (int) ($data['fine_amount'] ?? 0);
                $notes = $data['notes'] ?? null;

                // Hitung keterlambatan untuk informasi
                $daysOverdue = 0;
                if ($transaction->due_date < now()) {
                    $daysOverdue = (int) now()->diffInDays($transaction->due_date);
                }

                $transaction->return_date = now();
                $transaction->status_id = $returnStatus?->id;
                $transaction->penalty_total = (string) $fineAmount;
                if ($notes) {
                    $transaction->notes = $notes;
                }
                $transaction->save();

                $statusName = $returnStatus?->name ?? 'Dikembalikan';
                $message = "Buku berhasil diproses dengan status: {$statusName}";
                if ($fineAmount > 0) {
                    $message .= '. Total denda: Rp '.number_format($fineAmount);
                }

                Log::info('Book returned with custom status', [
                    'transaction_id' => $transaction->id,
                    'status' => $statusName,
                    'fine' => $fineAmount,
                    'days_overdue' => $daysOverdue,
                ]);

                return [
                    'success' => true,
                    'message' => $message,
                    'penalty' => $fineAmount,
                    'days_overdue' => $daysOverdue,
                ];
            }

            // Default behavior (backward compatibility)
            $daysOverdue = 0;
            $penalty = 0;

            if ($transaction->due_date < now()) {
                $daysOverdue = (int) now()->diffInDays($transaction->due_date);
                $penalty = $daysOverdue * $this->penaltyPerDay;
            }

            // Update status ke "Dikembalikan"
            $returnedStatus = Status::where('name', 'Dikembalikan')->first();
            $transaction->return_date = now();
            $transaction->status_id = $returnedStatus?->id;
            $transaction->penalty_total = (string) $penalty;
            $transaction->save();

            Log::info('Book returned', [
                'transaction_id' => $transaction->id,
                'days_overdue' => $daysOverdue,
                'penalty' => $penalty,
            ]);

            return [
                'success' => true,
                'message' => $daysOverdue > 0
                    ? "Buku dikembalikan terlambat {$daysOverdue} hari. Denda: Rp ".number_format($penalty)
                    : 'Buku berhasil dikembalikan tepat waktu',
                'penalty' => $penalty,
                'days_overdue' => $daysOverdue,
            ];
        });
    }

    /**
     * Batalkan permintaan peminjaman
     *
     * @param  int  $transactionId  ID transaction
     * @param  string  $reason  Alasan pembatalan
     * @return array{success: bool, message: string}
     */
    public function cancelRequest(int $transactionId, string $reason = ''): array
    {
        $transaction = Transaction::find($transactionId);

        if (! $transaction) {
            return [
                'success' => false,
                'message' => 'Transaksi tidak ditemukan',
            ];
        }

        // Hanya bisa batalkan jika status "Menunggu Persetujuan"
        $pendingStatus = Status::where('name', 'Menunggu Persetujuan')->first();
        if ($transaction->status_id !== $pendingStatus?->id) {
            return [
                'success' => false,
                'message' => 'Hanya permintaan yang belum disetujui yang bisa dibatalkan',
            ];
        }

        $cancelledStatus = Status::where('name', 'Dibatalkan')->first();
        $transaction->status_id = $cancelledStatus?->id;
        $transaction->save();

        Log::info('Borrow request cancelled', [
            'transaction_id' => $transaction->id,
            'reason' => $reason,
        ]);

        return [
            'success' => true,
            'message' => 'Permintaan peminjaman berhasil dibatalkan',
        ];
    }

    /**
     * Proses peminjaman langsung (oleh admin tanpa request)
     *
     * @param  int  $userId  ID user
     * @param  int  $bookId  ID buku
     * @param  int|null  $borrowDays  Jumlah hari peminjaman (default dari setting)
     * @return array{success: bool, transaction: Transaction|null, message: string}
     */
    public function directBorrow(int $userId, int $bookId, ?int $borrowDays = null): array
    {
        return DB::transaction(function () use ($userId, $bookId, $borrowDays): array {
            $user = UserDetail::where('user_id', $userId)->first();
            $book = Book::find($bookId);

            if (! $user || ! $book) {
                return [
                    'success' => false,
                    'transaction' => null,
                    'message' => 'User atau buku tidak ditemukan',
                ];
            }

            // Validasi
            $eligibility = $this->barcodeScanner->checkUserBorrowEligibility($user);
            if (! $eligibility['allowed']) {
                return [
                    'success' => false,
                    'transaction' => null,
                    'message' => $eligibility['message'],
                ];
            }

            $availability = $this->barcodeScanner->checkBookAvailability($book);
            if (! $availability['available']) {
                return [
                    'success' => false,
                    'transaction' => null,
                    'message' => $availability['message'],
                ];
            }

            // Buat transaction langsung dengan status "Dipinjam"
            $borrowedStatus = Status::where('name', 'Dipinjam')->first();
            $days = $borrowDays ?? $this->defaultBorrowDays;

            $transaction = new Transaction;
            $transaction->user_id = $userId;
            $transaction->book_id = $bookId;
            $transaction->borrow_date = now();
            $transaction->due_date = now()->addDays($days);
            $transaction->status_id = $borrowedStatus?->id;
            $transaction->save();

            Log::info('Direct borrow created', [
                'transaction_id' => $transaction->id,
                'user_id' => $userId,
                'book_id' => $bookId,
                'borrow_days' => $days,
            ]);

            return [
                'success' => true,
                'transaction' => $transaction,
                'message' => 'Peminjaman berhasil dibuat',
            ];
        });
    }

    /**
     * Get transaksi berdasarkan status
     *
     * @param  string|array  $statusNames  Nama/nama status
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function getTransactionsByStatus($statusNames)
    {
        $statusIds = Status::whereIn('name', (array) $statusNames)
            ->pluck('id');

        return Transaction::with(['book', 'user', 'status'])
            ->whereIn('status_id', $statusIds)->latest()
            ->get();
    }

    /**
     * Get transaksi yang overdue
     *
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function getOverdueTransactions()
    {
        $borrowedStatus = Status::where('name', 'Dipinjam')->first();

        return Transaction::with(['book', 'user', 'status'])
            ->where('status_id', $borrowedStatus?->id)
            ->where('due_date', '<', now())
            ->oldest('due_date')
            ->get();
    }

    /**
     * Update status overdue transactions
     * Bisa dijalankan via scheduled task
     *
     * @return int Jumlah transaction yang di-update
     */
    public function updateOverdueStatus(): int
    {
        $borrowedStatus = Status::where('name', 'Dipinjam')->first();
        $overdueStatus = Status::where('name', 'Terlambat')->first();

        $updated = Transaction::where('status_id', $borrowedStatus?->id)
            ->where('due_date', '<', now())
            ->update(['status_id' => $overdueStatus?->id]);

        Log::info('Overdue transactions updated', ['count' => $updated]);

        return $updated;
    }

    /**
     * Get statistik transaksi
     *
     * @return array{active: int, pending: int, returned: int, overdue: int}
     */
    public function getTransactionStats(): array
    {
        $stats = [
            'active' => 0,
            'pending' => 0,
            'returned' => 0,
            'overdue' => 0,
        ];

        $statuses = Status::whereIn('name', [
            'Dipinjam',
            'Menunggu Persetujuan',
            'Dikembalikan',
            'Terlambat',
        ])->pluck('id', 'name');

        $stats['pending'] = Transaction::where('status_id', $statuses['Menunggu Persetujuan'])->count();
        $stats['active'] = Transaction::where('status_id', $statuses['Dipinjam'])->count();
        $stats['returned'] = Transaction::where('status_id', $statuses['Dikembalikan'])->count();
        $stats['overdue'] = Transaction::where('status_id', $statuses['Terlambat'])->count();

        return $stats;
    }
}
