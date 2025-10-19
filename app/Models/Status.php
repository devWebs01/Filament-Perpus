<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Status extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'amount',
    ];

    /**
     * Get all of the transactions for the Status
     */
    public function transactions(): HasMany
    {
        return $this->hasMany(Transaction::class);
    }
}
