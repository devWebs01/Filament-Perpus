<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Mattiverse\Userstamps\Traits\Userstamps;

class Transaction extends Model
{
    use HasFactory, SoftDeletes, Userstamps;

    protected $with = [
        'book',
        'user',
        'penalties',
    ];

    protected $fillable = [
        'book_id',
        'user_id',
        'borrow_date',
        'return_date',
        'status_id',
        'penalty_total',
    ];

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($transaction) {
            // Auto-generate kode transaksi unik
            $transaction->code = static::generateUniqueCode();

            // Set default status ke 'dipinjam' jika tidak ada status yang diset
            if (! $transaction->status_id) {
                $defaultStatus = Status::where('name', 'Konfirmasi Pinjam')->first();
                if ($defaultStatus) {
                    $transaction->status_id = $defaultStatus->id;
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
     * Get all of the penalties for the Transaction
     */
    public function penalties(): HasMany
    {
        return $this->hasMany(Penalty::class);
    }

    /**
     * Get the status that owns the Transaction
     */
    public function status(): BelongsTo
    {
        return $this->belongsTo(Status::class);
    }
}
