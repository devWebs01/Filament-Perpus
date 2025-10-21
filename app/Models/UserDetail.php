<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use Mattiverse\Userstamps\Traits\Userstamps;

class UserDetail extends Model
{
    use HasFactory, SoftDeletes, Userstamps;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'user_id',           // ID user yang terkait
        'nik',               // Nomor Induk Kependudukan (untuk semua aktor)
        'nis',               // Nomor Induk Siswa (khusus siswa)
        'nisn',              // Nomor Induk Siswa Nasional (khusus siswa)
        'class',             // Kelas siswa (contoh: X RPL 1)
        'major',             // Jurusan siswa (contoh: Rekayasa Perangkat Lunak)
        'semester',          // Semester siswa (contoh: 1, 2, 3, dst)
        'address',           // Alamat lengkap
        'phone_number',      // Nomor telepon/HP
        'birth_date',        // Tanggal lahir
        'birth_place',       // Tempat lahir
        'gender',            // Jenis kelamin (L/P)
        'religion',          // Agama
        'join_date',         // Tanggal bergabung dengan perpustakaan
        'membership_status', // Status keanggotaan (active, inactive, suspended)
        'profile_photo',     // Path foto profil
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
     * Relasi ke model User
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Mendapatkan nama lengkap dari relasi user
     */
    public function getDisplayNameAttribute(): string
    {
        return $this->user->name;
    }

    /**
     * Mendapatkan nama lengkap (alias untuk displayName)
     */
    public function getFullNameAttribute(): string
    {
        return $this->user->name;
    }

    /**
     * Cek apakah user adalah siswa (memiliki NIS dan NISN)
     */
    public function isStudent(): bool
    {
        return ! is_null($this->nis) && ! is_null($this->nisn);
    }

    /**
     * Cek apakah user adalah petugas/ketua perpus (tidak memiliki NIS dan NISN)
     */
    public function isStaff(): bool
    {
        return is_null($this->nis) && is_null($this->nisn);
    }
}
