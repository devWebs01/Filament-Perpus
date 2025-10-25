<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Filament\Models\Contracts\FilamentUser;
use Filament\Panel;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Mattiverse\Userstamps\Traits\Userstamps;

class User extends Authenticatable implements FilamentUser
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable, SoftDeletes, Userstamps;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'email_verified_at',
        'role',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    /**
     * Get the user details associated with the user.
     *
     * @return HasOne<UserDetail>
     */
    public function userDetail(): HasOne
    {
        return $this->hasOne(UserDetail::class);
    }

    /**
     * Get the available roles for users.
     */
    public static function getAvailableRoles(): array
    {
        return [
            'super_admin' => 'Super Admin',
            'ketua_perpustakaan' => 'Ketua Perpustakaan',
            'petugas' => 'Petugas',
            'siswa' => 'Siswa',
        ];
    }

    /**
     * Check if user has specific role.
     */
    public function hasRole(string $role): bool
    {
        return $this->role === $role;
    }

    /**
     * Check if user has any of the given roles.
     */
    public function hasAnyRole(array $roles): bool
    {
        return in_array($this->role, $roles);
    }

    /**
     * Check if user is Super Admin.
     */
    public function isSuperAdmin(): bool
    {
        return $this->role === 'super_admin';
    }

    /**
     * Check if user is Admin (Super Admin or Ketua Perpustakaan).
     */
    public function isAdmin(): bool
    {
        return $this->hasAnyRole(['super_admin', 'ketua_perpustakaan']);
    }

    /**
     * Check if user is Staff (Petugas, Super Admin, or Ketua Perpustakaan).
     */
    public function isStaff(): bool
    {
        return $this->hasAnyRole(['super_admin', 'ketua_perpustakaan', 'petugas']);
    }

    /**
     * Check if user is Student.
     */
    public function isStudent(): bool
    {
        return $this->role === 'siswa';
    }

    /**
     * Get role display name.
     */
    public function getRoleDisplayName(): string
    {
        $roles = self::getAvailableRoles();

        return $roles[$this->role] ?? 'Unknown';
    }

    /**
     * Determine if the user can access the Filament panel.
     */
    public function canAccessPanel(Panel $panel): bool
    {
        // All authenticated users can access the admin panel
        // Roles will control what they can see and do
        return true;
    }
}
