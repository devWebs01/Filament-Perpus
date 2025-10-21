<?php

namespace App\Filament\Resources\Users\Schemas;

use Filament\Forms\Components\CheckboxList;
use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\HtmlString;

/**
 * User Form Schema
 *
 * This form schema handles user creation and editing with integrated
 * role management and user details for the library system.
 */
class UserForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                // Basic Information Section
                Section::make('Informasi Dasar')
                    ->description('Informasi akun pengguna dan kredensial')
                    ->icon('heroicon-o-user')
                    ->schema([
                        TextInput::make('name')
                            ->label('Nama Lengkap')
                            ->required()
                            ->maxLength(255)
                            ->autofocus()
                            ->columnSpanFull(),

                        TextInput::make('email')
                            ->label('Alamat Email')
                            ->email()
                            ->required()
                            ->unique(ignoreRecord: true)
                            ->maxLength(255)
                            ->columnSpanFull(),

                        TextInput::make('password')
                            ->label('Kata Sandi')
                            ->password()
                            ->dehydrateStateUsing(fn ($state) => $state ? Hash::make($state) : null)
                            ->dehydrated(fn ($state) => filled($state))
                            ->required(fn (string $context): bool => $context === 'create')
                            ->helperText(fn (string $context): string => $context === 'edit' ? 'Kosongkan untuk tetap menggunakan kata sandi saat ini' : '')
                            ->columnSpanFull(),

                        TextInput::make('password_confirmation')
                            ->label('Ulangi Kata Sandi')
                            ->password()
                            ->required(fn (string $context): bool => $context === 'create')
                            ->dehydrated(false)
                            ->same('password')
                            ->helperText('Masukkan kembali kata sandi untuk konfirmasi')
                            ->columnSpanFull(),

                        DateTimePicker::make('email_verified_at')
                            ->label('Email Terverifikasi Pada')
                            ->helperText('Kapan email diverifikasi. Kosongkan untuk belum diverifikasi.')
                            ->columnSpanFull()
                            ->default(now())
                            ->hidden(),
                    ])
                    ->columns(2),

                // Role & Permissions Section
                Section::make('Peran & Izin Akses')
                    ->description('Tetapkan peran pengguna dan izin akses ke sistem')
                    ->icon('heroicon-o-shield-check')
                    ->schema([
                        CheckboxList::make('roles')
                            ->label('Tetapkan Peran')
                            ->relationship('roles', 'name')
                            ->options(function () {
                                $availableRoles = [
                                    'super_admin' => '🔴 Super Admin - Akses penuh ke sistem',
                                    'ketua_perpustakaan' => '🟠 Ketua Perpustakaan - Kontrol administratif penuh',
                                    'petugas' => '🔵 Petugas Perpustakaan - Pengelolaan operasional harian',
                                    'siswa' => '🟢 Siswa - Akses sumber daya perpustakaan',
                                ];

                                // Only show roles that current user can assign
                                if (! auth()->user()->hasRole('super_admin')) {
                                    unset($availableRoles['super_admin']);
                                }

                                return $availableRoles;
                            })
                            ->bulkToggleable()
                            ->helperText(new HtmlString('
                                <strong>Informasi Peran & Izin Akses:</strong><br>
                                • <strong>🔴 Super Admin:</strong> Akses penuh ke semua fitur sistem<br>
                                • <strong>🟠 Ketua Perpustakaan:</strong> Kontrol administratif, kelola pengguna & laporan<br>
                                • <strong>🔵 Petugas Perpustakaan:</strong> Kelola buku, transaksi peminjaman/pengembalian<br>
                                • <strong>🟢 Siswa:</strong> Akses katalog buku dan lihat transaksi pribadi
                            '))
                            ->required()
                            ->columns(1),
                    ])
                    ->columns(1),

                // Library Information Section
                Section::make('Informasi Perpustakaan')
                    ->description('Informasi tambahan untuk sistem perpustakaan')
                    ->icon('heroicon-o-building-library')
                    ->schema([
                        TextInput::make('UserDetail.phone_number')
                            ->label('Nomor Telepon')
                            ->tel()
                            ->maxLength(20),

                        TextInput::make('UserDetail.nik')
                            ->label('NIK (Nomor Induk Kependudukan)')
                            ->maxLength(16)
                            ->helperText('Nomor identitas nasional 16 digit'),

                        TextInput::make('UserDetail.birth_place')
                            ->label('Tempat Lahir')
                            ->maxLength(100),

                        Select::make('UserDetail.gender')
                            ->label('Jenis Kelamin')
                            ->options([
                                'L' => 'Laki-laki',
                                'P' => 'Perempuan',
                            ])
                            ->searchable(),

                        DateTimePicker::make('UserDetail.birth_date')
                            ->label('Tanggal Lahir')
                            ->date()
                            ->maxDate(now())
                            ->columnSpanFull(),

                        Textarea::make('UserDetail.address')
                            ->label('Alamat')
                            ->rows(3)
                            ->maxLength(500)
                            ->columnSpanFull(),

                        // Student Information
                        TextInput::make('UserDetail.nis')
                            ->label('NIS (Nomor Induk Siswa)')
                            ->maxLength(20)
                            ->helperText('Nomor identitas siswa'),

                        TextInput::make('UserDetail.nisn')
                            ->label('NISN (Nomor Induk Siswa Nasional)')
                            ->maxLength(10)
                            ->helperText('Nomor identitas siswa nasional 10 digit'),

                        TextInput::make('UserDetail.class')
                            ->label('Kelas')
                            ->maxLength(10)
                            ->placeholder('contoh: 12A'),

                        Select::make('UserDetail.membership_status')
                            ->label('Status Keanggotaan Perpustakaan')
                            ->options([
                                'active' => 'Aktif',
                                'suspended' => 'Ditangguhkan',
                                'expired' => 'Kadaluarsa',
                            ])
                            ->default('active')
                            ->required(),
                    ])
                    ->columns(2),

            ]);
    }
}
