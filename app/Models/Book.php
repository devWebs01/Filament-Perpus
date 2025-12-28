<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Mattiverse\Userstamps\Traits\Userstamps;

class Book extends Model
{
    /** @use HasFactory<\Database\Factories\BookFactory> */
    use HasFactory, SoftDeletes, Userstamps;

    protected $fillable = [
        'title',
        'image',
        'category_id',
        'isbn',
        'barcode',
        'author',
        'year_published',
        'publisher',
        'synopsis',
        'book_count',
        'bookshelf',
        'source',
        'price',
        'type',
    ];

    public function category()
    {
        return $this->belongsTo(Category::class);
    }

    /**
     * Get all of the transactions for the Book
     */
    public function transactions(): HasMany
    {
        return $this->hasMany(Transaction::class);
    }

    /**
     * Get active borrow transactions for this book
     */
    public function activeTransactions(): HasMany
    {
        return $this->hasMany(Transaction::class)
            ->whereHas('status', function ($query) {
                $query->where('name', 'Dipinjam');
            });
    }

    /**
     * Check if book is available for borrowing
     */
    public function isAvailable(): bool
    {
        return $this->book_count > $this->activeTransactions()->count();
    }

    /**
     * Get available count for this book
     */
    public function getAvailableCount(): int
    {
        return max(0, $this->book_count - $this->activeTransactions()->count());
    }

    /**
     * Get all bookmarks for this book
     */
    public function bookmarks(): HasMany
    {
        return $this->hasMany(Bookmark::class);
    }

    /**
     * Find book by barcode or ISBN
     */
    public static function findByBarcode(string $barcode): ?self
    {
        return static::where('barcode', $barcode)
            ->orWhere('isbn', $barcode)
            ->first();
    }

    /**
     * Boot method untuk menangani event model.
     * Barcode generation sekarang ditangani oleh BookObserver.
     */
    protected static function boot(): void
    {
        parent::boot();
    }
}
