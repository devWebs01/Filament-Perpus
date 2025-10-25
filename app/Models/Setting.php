<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Mattiverse\Userstamps\Traits\Userstamps;

class Setting extends Model
{
    use SoftDeletes, Userstamps;

    protected $fillable = [
        'name',
        'logo',
        'address',
        'phone',
        'limit_day'
    ];
}
