<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;
use Mattiverse\Userstamps\Traits\Userstamps;

class Transaction extends Model
{
    use HasFactory, SoftDeletes, Userstamps;

    protected $with = [
        'book',
        'user',
    ];

    protected $fillable = [
        'book_id',
        'user_id',
        'borrow_date',
        'due_date',
        'return_date',
        'status_id',
        'penalty_total',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'borrow_date' => 'date',
            'due_date' => 'date',
            'return_date' => 'date',
            'penalty_total' => 'integer',
        ];
    }

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($transaction) {
            // Auto-generate kode transaksi unik
            $transaction->code = static::generateUniqueCode();

            // Check max borrow limit for students
            if ($transaction->user_id && $transaction->user->isStudent()) {
                $maxBorrow = (int) (\App\Models\Setting::first()?->max_borrow ?? 3);

                $currentBorrows = static::where('user_id', $transaction->user_id)
                    ->whereHas('status', function ($query) {
                        $query->where('name', 'Dipinjam');
                    })
                    ->count();

                if ($currentBorrows >= $maxBorrow) {
                    throw new \Exception("Siswa telah mencapai batas maksimal peminjaman ({$maxBorrow} buku).");
                }
            }

            // Only set a default status if none is provided
            // The specific default status should be handled by the service or form that creates the transaction
        });

        // When a transaction is deleted, update book stock accordingly
        static::deleted(function ($transaction) {
            if ($transaction->book) {
                // If the transaction was for a borrowed book (not yet returned),
                // we need to increase the stock since the book is no longer "held"
                $borrowedStatus = Status::where('name', 'Dipinjam')->first();
                $overdueStatus = Status::where('name', 'Terlambat')->first();

                // Check if the transaction was for an active borrow (not yet returned)
                if (in_array($transaction->status_id, [$borrowedStatus?->id, $overdueStatus?->id])) {
                    $transaction->book->increment('book_count');
                }
            }
        });

        // When a transaction is restored, adjust book stock based on status
        static::restored(function ($transaction) {
            if ($transaction->book) {
                // If the restored transaction was for a borrowed book (not yet returned),
                // we need to decrease the stock again
                $borrowedStatus = Status::where('name', 'Dipinjam')->first();
                $overdueStatus = Status::where('name', 'Terlambat')->first();

                // Check if the transaction was for an active borrow (not yet returned)
                if (in_array($transaction->status_id, [$borrowedStatus?->id, $overdueStatus?->id])) {
                    $transaction->book->decrement('book_count');
                }
            }
        });
    }

    public static function generateUniqueCode()
    {
        do {
            $code = 'TRX-'.date('Ymd').'-'.str_pad(rand(1, 9999), 4, '0', STR_PAD_LEFT);
        } while (static::where('code', $code)->exists());

        return $code;
    }

    /**
     * Get the user that owns the Transaction
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the book that owns the Transaction
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function book()
    {
        return $this->belongsTo(Book::class);
    }

    /**
     * Get the status that owns the Transaction
     */
    public function status(): BelongsTo
    {
        return $this->belongsTo(Status::class);
    }

    /**
     * Check if transaction is borrowed
     */
    public function isBorrowed(): bool
    {
        return $this->status && $this->status->name === 'Dipinjam';
    }

    /**
     * Check if transaction is returned
     */
    public function isReturned(): bool
    {
        return $this->status && $this->status->name === 'Dikembalikan';
    }

    /**
     * Check if transaction is overdue
     */
    public function isOverdue(): bool
    {
        return $this->isBorrowed() && $this->due_date < now();
    }

    /**
     * Check if transaction is overdue for more than 24 hours
     */
    public function isOverdueMoreThan24Hours(): bool
    {
        return $this->isBorrowed() && $this->due_date->diffInHours(now()) > 24;
    }

    /**
     * Get the number of hours overdue
     */
    public function getHoursOverdue(): int
    {
        if (! $this->isBorrowed() || ! $this->isOverdue()) {
            return 0;
        }

        return $this->due_date->diffInHours(now());
    }

    /**
     * Get days overdue (integer)
     */
    public function getDaysOverdue(): int
    {
        if (! $this->isBorrowed() || ! $this->isOverdue()) {
            return 0;
        }

        return now()->diffInDays($this->due_date);
    }

    /**
     * Calculate penalty for overdue transaction
     */
    public function getPenalty(): float
    {
        $daysOverdue = $this->getDaysOverdue();
        $penaltyPerDay = 1000; // Rp 1000 per hari

        return $daysOverdue * $penaltyPerDay;
    }

    /**
     * Get formatted penalty amount
     */
    public function getFormattedPenalty(): string
    {
        return 'Rp '.number_format($this->getPenalty(), 0, ',', '.');
    }

    /**
     * Get formatted due date
     */
    public function getFormattedDueDate(): string
    {
        return $this->due_date->format('d M Y');
    }

    /**
     * Get formatted borrow date
     */
    public function getFormattedBorrowDate(): string
    {
        return $this->borrow_date->format('d M Y');
    }

    /**
     * Get status color for UI display
     */
    public function getStatusColor(): string
    {
        if ($this->isOverdue()) {
            $daysOverdue = $this->getDaysOverdue();

            if ($daysOverDue <= 3) {
                return 'warning';
            } elseif ($daysOverDue <= 7) {
                return 'danger';
            } else {
                return 'danger';
            }
        }

        switch ($this->status?->name) {
            case 'Dipinjam':
                return 'success';
            case 'Dikembalikan':
                return 'info';
            case 'Terlambat':
                return 'danger';
            default:
                return 'secondary';
        }
    }

    /**
     * Get status badge class
     */
    public function getStatusBadgeClass(): string
    {
        return match ($this->getStatusColor()) {
            'success' => 'bg-green-100 text-green-800',
            'warning' => 'bg-yellow-100 text-yellow-800',
            'danger' => 'bg-red-100 text-red-800',
            'info' => 'bg-blue-100 text-blue-800',
            'secondary' => 'bg-gray-100 text-gray-800',
        };
    }
}
