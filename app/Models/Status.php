<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Mattiverse\Userstamps\Traits\Userstamps;

class Status extends Model
{
    use HasFactory, SoftDeletes, Userstamps;

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
