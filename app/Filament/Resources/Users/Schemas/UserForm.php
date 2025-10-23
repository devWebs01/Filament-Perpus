<?php

namespace App\Filament\Resources\Users\Schemas;

use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Illuminate\Support\Facades\Hash;
use ToneGabes\BetterOptions\Forms\Components\CheckboxCards;

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
                            ->columnSpanFull()
                            ->validationMessages([
                                'required' => 'Nama lengkap wajib diisi.',
                                'max' => 'Nama lengkap maksimal 255 karakter.',
                                'string' => 'Nama lengkap harus berupa teks.',
                            ]),

                        TextInput::make('email')
                            ->label('Alamat Email')
                            ->email()
                            ->required()
                            ->unique(ignoreRecord: true)
                            ->maxLength(255)
                            ->columnSpanFull()
                            ->validationMessages([
                                'required' => 'Alamat email wajib diisi.',
                                'email' => 'Format email tidak valid.',
                                'unique' => 'Email ini sudah terdaftar, gunakan email lain.',
                                'max' => 'Email maksimal 255 karakter.',
                            ]),

                        TextInput::make('password')
                            ->label('Kata Sandi')
                            ->password()
                            ->dehydrateStateUsing(fn ($state) => $state ? Hash::make($state) : null)
                            ->dehydrated(fn ($state) => filled($state))
                            ->required(fn (string $context): bool => $context === 'create')
                            ->placeholder(fn (string $context): string => $context === 'edit' ? 'Kosongkan untuk tetap menggunakan kata sandi saat ini' : '')
                            ->columnSpanFull()
                            ->validationMessages([
                                'required' => 'Kata sandi wajib diisi saat membuat pengguna baru.',
                                'min' => 'Kata sandi minimal 8 karakter.',
                                'string' => 'Kata sandi harus berupa teks.',
                            ]),

                        TextInput::make('password_confirmation')
                            ->label('Ulangi Kata Sandi')
                            ->password()
                            ->required(fn (string $context): bool => $context === 'create')
                            ->dehydrated(false)
                            ->same('password')
                            ->placeholder('Masukkan kembali kata sandi untuk konfirmasi')
                            ->columnSpanFull()
                            ->validationMessages([
                                'required' => 'Konfirmasi kata sandi wajib diisi.',
                                'same' => 'Konfirmasi kata sandi harus sama dengan kata sandi.',
                            ]),

                        DateTimePicker::make('email_verified_at')
                            ->label('Email Terverifikasi Pada')
                            ->placeholder('Kapan email diverifikasi. Kosongkan untuk belum diverifikasi.')
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
                        CheckboxCards::make('roles')
                            ->label('Tetapkan Peran')
                            ->relationship('roles', 'name')
                            ->options(function () {
                                $availableRoles = [
                                    'super_admin' => 'Super Admin - Akses penuh ke sistem',
                                    'ketua_perpustakaan' => 'Ketua Perpustakaan - Kontrol administratif penuh',
                                    'petugas' => 'Petugas Perpustakaan - Pengelolaan operasional harian',
                                    'siswa' => 'Siswa - Akses sumber daya perpustakaan',
                                ];

                                // Only show roles that current user can assign
                                if (! auth()->user()->hasRole('super_admin')) {
                                    unset($availableRoles['super_admin']);
                                }

                                return $availableRoles;
                            })
                            ->descriptions(function () {
                                $availableRoles = [
                                    'super_admin' => 'Akses penuh ke semua fitur sistem',
                                    'ketua_perpustakaan' => 'Kontrol administratif, kelola pengguna & laporan',
                                    'petugas' => 'Kelola buku, transaksi peminjaman/pengembalian',
                                    'siswa' => 'Akses katalog buku dan lihat transaksi pribadi',
                                ];

                                // Only show roles that current user can assign
                                if (! auth()->user()->hasRole('super_admin')) {
                                    unset($availableRoles['super_admin']);
                                }

                                return $availableRoles;
                            })
                            ->bulkToggleable()
                            ->required()
                            ->columns(1)
                            ->icons(['heroicon-o-user', 'heroicon-o-shield-check', 'heroicon-o-cog', 'heroicon-o-academic-cap'])
                            ->reactive()
                            ->dehydrateStateUsing(fn ($state) => is_array($state) ? $state : [])
                            ->afterStateUpdated(function ($state, $record) {
                                if ($record && $record->exists) {
                                    $record->syncRoles($state);
                                }
                            })
                            ->saveRelationshipsUsing(function ($record, $state) {
                                if ($record && $record->exists) {
                                    $record->syncRoles($state);
                                }
                            })
                            ->validationMessages([
                                'required' => 'Peran pengguna wajib dipilih minimal satu.',
                                'array' => 'Peran pengguna harus berupa pilihan yang valid.',
                            ]),
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
                            ->numeric()
                            ->maxLength(20)
                            ->rules(['regex:/^[0-9+\-\s]+$/'])
                            ->validationMessages([
                                'numeric' => 'Nomor telepon hanya boleh berisi angka.',
                                'regex' => 'Nomor telepon hanya boleh berisi angka, +, -, dan spasi.',
                                'max' => 'Nomor telepon maksimal 20 karakter.',
                            ]),

                        TextInput::make('UserDetail.nik')
                            ->label('NIK (Nomor Induk Kependudukan)')
                            ->maxLength(16)
                            ->numeric()
                            ->rules(['regex:/^[0-9]+$/'])
                            ->placeholder('Opsional')
                            ->validationMessages([
                                'numeric' => 'NIK hanya boleh berisi angka.',
                                'regex' => 'NIK hanya boleh berisi angka tanpa spasi atau karakter lain.',
                                'max' => 'NIK maksimal 16 digit.',
                            ]),

                        TextInput::make('UserDetail.birth_place')
                            ->label('Tempat Lahir')
                            ->maxLength(100)
                            ->validationMessages([
                                'max' => 'Tempat lahir maksimal 100 karakter.',
                                'string' => 'Tempat lahir harus berupa teks.',
                            ]),

                        Select::make('UserDetail.gender')
                            ->label('Jenis Kelamin')
                            ->options([
                                'L' => 'Laki-laki',
                                'P' => 'Perempuan',
                            ])
                            ->searchable()
                            ->validationMessages([
                                'required' => 'Jenis kelamin wajib dipilih.',
                                'in' => 'Jenis kelamin tidak valid.',
                            ]),

                        DateTimePicker::make('UserDetail.birth_date')
                            ->label('Tanggal Lahir')
                            ->date()
                            ->maxDate(now())
                            ->columnSpanFull()
                            ->validationMessages([
                                'date' => 'Format tanggal lahir tidak valid.',
                                'max' => 'Tanggal lahir tidak boleh melebihi hari ini.',
                                'required' => 'Tanggal lahir wajib diisi.',
                            ]),

                        Textarea::make('UserDetail.address')
                            ->label('Alamat')
                            ->rows(3)
                            ->maxLength(500)
                            ->columnSpanFull()
                            ->validationMessages([
                                'max' => 'Alamat maksimal 500 karakter.',
                                'string' => 'Alamat harus berupa teks.',
                            ]),

                        // Student Information
                        TextInput::make('UserDetail.nis')
                            ->label('NIS (Nomor Induk Siswa)')
                            ->maxLength(20)
                            ->numeric()
                            ->rules(['regex:/^[0-9]+$/'])
                            ->placeholder('Nomor identitas siswa')
                            ->validationMessages([
                                'numeric' => 'NIS hanya boleh berisi angka.',
                                'regex' => 'NIS hanya boleh berisi angka tanpa spasi atau karakter lain.',
                                'max' => 'NIS maksimal 20 digit.',
                            ]),

                        TextInput::make('UserDetail.nisn')
                            ->label('NISN (Nomor Induk Siswa Nasional)')
                            ->maxLength(10)
                            ->numeric()
                            ->rules(['regex:/^[0-9]+$/'])
                            ->placeholder('NISN 10 digit')
                            ->validationMessages([
                                'numeric' => 'NISN hanya boleh berisi angka.',
                                'regex' => 'NISN hanya boleh berisi angka tanpa spasi atau karakter lain.',
                                'max' => 'NISN harus tepat 10 digit.',
                                'min' => 'NISN harus tepat 10 digit.',
                            ]),

                        TextInput::make('UserDetail.class')
                            ->label('Kelas')
                            ->maxLength(10)
                            ->placeholder('contoh: 12A')
                            ->validationMessages([
                                'max' => 'Kelas maksimal 10 karakter.',
                                'string' => 'Kelas harus berupa teks.',
                            ]),

                        Select::make('UserDetail.membership_status')
                            ->label('Status Keanggotaan Perpustakaan')
                            ->options([
                                'active' => 'Aktif',
                                'suspended' => 'Ditangguhkan',
                                'expired' => 'Kadaluarsa',
                            ])
                            ->default('active')
                            ->required()
                            ->validationMessages([
                                'required' => 'Status keanggotaan wajib dipilih.',
                                'in' => 'Status keanggotaan tidak valid.',
                            ]),
                    ])
                    ->columns(2),

            ]);
    }
}
