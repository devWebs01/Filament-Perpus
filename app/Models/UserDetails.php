<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;
use Mattiverse\Userstamps\Traits\Userstamps;

/**
 * UserDetails Model
 *
 * This model contains detailed information about users in the library system.
 * It extends the base User model with additional fields specific to library operations
 * including student information, staff details, and library membership data.
 *
 * Supported user types: student, library_head, staff
 */
class UserDetails extends Model
{
    use HasFactory, SoftDeletes, Userstamps;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'user_id',
        'nik', // National ID
        'nis', // Student ID
        'nisn', // National Student ID
        'class',
        'major',
        'semester',
        'address',
        'phone_number',
        'birth_date',
        'birth_place',
        'gender',
        'religion',
        'join_date',
        'membership_status',
        'profile_photo',
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
     * Get the user that owns the user details.
     *
     * @return BelongsTo<User, UserDetails>
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

    /**
     * Get user type display name
     */
    public function getUserTypeDisplayNameAttribute(): string
    {
        if ($this->isLibraryHead()) {
            return 'Library Head';
        } elseif ($this->isStudent()) {
            return 'Student';
        } elseif ($this->isStaff()) {
            return 'Library Staff';
        }

        return 'Unknown';
    }

    /**
     * Get membership status display name
     */
    public function getMembershipStatusDisplayNameAttribute(): string
    {
        return match ($this->membership_status) {
            'active' => 'Active',
            'suspended' => 'Suspended',
            'expired' => 'Expired',
            default => 'Unknown',
        };
    }

    /**
     * Scope to get only students
     * Students have NIS or NISN numbers
     *
     * @param  \Illuminate\Database\Eloquent\Builder  $query
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeStudents($query)
    {
        return $query->where(function ($q) {
            $q->whereNotNull('nis')
                ->orWhereNotNull('nisn');
        });
    }

    /**
     * Scope to get only library heads
     * Based on admin email or other criteria
     *
     * @param  \Illuminate\Database\Eloquent\Builder  $query
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeLibraryHeads($query)
    {
        return $query->whereHas('user', function ($q) {
            $q->where('email', 'admin@testing.com');
        });
    }

    /**
     * Scope to get only staff
     * Staff are non-students with join dates
     *
     * @param  \Illuminate\Database\Eloquent\Builder  $query
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeStaff($query)
    {
        return $query->where(function ($q) {
            $q->whereNull('nis')
                ->whereNull('nisn');
        })
            ->whereNotNull('join_date')
            ->whereHas('user', function ($q) {
                $q->where('email', '!=', 'admin@testing.com');
            });
    }

    /**
     * Scope to get only active memberships
     *
     * @param  \Illuminate\Database\Eloquent\Builder  $query
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeActiveMembers($query)
    {
        return $query->where('membership_status', 'active');
    }
}
