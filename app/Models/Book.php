<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Book extends Model
{
    /** @use HasFactory<\Database\Factories\BookFactory> */
    use HasFactory;

    protected $fillable = [
        'title',
        'image',
        'category_id',
        'isbn',
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
}
