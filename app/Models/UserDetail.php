<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * UserDetail Model
 *
 * This model contains detailed information about users in the library system.
 * It extends the base User model with additional fields specific to library operations
 * including student information, staff details, and library membership data.
 *
 * Supported user types: student, library_head, staff
 */
class UserDetail extends Model
{
    use HasFactory;
    use SoftDeletes;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'user_id',
        'nik', // National ID
        'nis', // Student ID
        'nisn', // National Student ID
        'class',
        'address',
        'phone_number',
        'birth_date',
        'birth_place',
        'gender',
        'religion',
        'join_date',
        'membership_status',
        'profile_photo',
        'qr_code',
    ];

    /**
     * The attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'birth_date' => 'date',
            'join_date' => 'date',
        ];
    }

    /**
     * Boot method untuk menangani event model.
     * QR code generation sekarang ditangani oleh UserDetailObserver.
     */
    protected static function boot()
    {
        parent::boot();

        // Set join_date secara otomatis saat creating
        static::creating(function ($userDetail): void {
            if (empty($userDetail->join_date)) {
                $userDetail->join_date = \Illuminate\Support\Facades\Date::now();
            }
        });
    }

    /**
     * Get the user that owns the user details.
     *
     * @return BelongsTo<User, $this>
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Check if user is a student
     * Students typically have NIS and NISN numbers
     */
    public function isStudent(): bool
    {
        return ! empty($this->nis) || ! empty($this->nisn);
    }

    /**
     * Check if user is library head
     * Determine by checking if user has admin role or special conditions
     */
    public function isLibraryHead(): bool
    {
        // Check if associated user has admin permissions
        return $this->user && $this->user->email === 'admin@testing.com';
    }

    /**
     * Check if user is staff
     * Staff members are non-students with join dates
     */
    public function isStaff(): bool
    {
        return ! $this->isStudent() && ! $this->isLibraryHead() && ! empty($this->join_date);
    }

    /**
     * Check if membership is currently active
     */
    public function isMembershipActive(): bool
    {
        return $this->membership_status === 'active';
    }
}
