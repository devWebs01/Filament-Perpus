<?php

namespace App\Filament\Resources\Users\Schemas;

use App\Models\User;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Schema;
use Filament\Schemas\Components\Section;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Password;

class UserForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Informasi Pengguna')
                    ->description('Informasi dasar akun pengguna')
                    ->schema([
                        Grid::make(2)
                            ->schema([
                                TextInput::make('name')
                                    ->label('Nama Lengkap')
                                    ->required()
                                    ->maxLength(255),

                                TextInput::make('email')
                                    ->label('Alamat Email')
                                    ->email()
                                    ->required()
                                    ->maxLength(255)
                                    ->unique(ignoreRecord: true),
                            ]),

                        TextInput::make('password')
                            ->label('Kata Sandi')
                            ->password()
                            ->dehydrateStateUsing(fn(string $state): string => Hash::make($state))
                            ->dehydrated(fn(?string $state): bool => filled($state))
                            ->required(fn(string $operation): bool => $operation === 'create')
                            ->helperText(fn(string $context): string => $context === 'edit' ? 'Kosongkan untuk tetap menggunakan kata sandi saat ini' : 'Kata sandi harus minimal 8 karakter'),
                    ]),

                Section::make('Penugasan Peran')
                    ->description('Tetapkan peran untuk pengguna')
                    ->schema([
                        Select::make('role')
                            ->label('Peran')
                            ->options(User::getAvailableRoles())
                            ->required()
                            ->default('siswa')
                            ->helperText('Pilih peran untuk pengguna ini')
                            ->disabled(fn(string $context): bool => $context === 'edit' && auth()->user()?->role !== 'super_admin'),
                    ]),

                Section::make('Detail Pengguna')
                    ->description('Informasi detail tentang pengguna')
                    ->relationship('userDetail')
                    ->schema([
                        Grid::make(2)
                            ->schema([
                                TextInput::make('nik')
                                    ->label('NIK (Nomor Induk Kependudukan)')
                                    ->maxLength(255),

                                TextInput::make('nis')
                                    ->label('NIS (Nomor Induk Siswa)')
                                    ->maxLength(255),

                                TextInput::make('nisn')
                                    ->label('NISN (Nomor Induk Siswa Nasional)')
                                    ->maxLength(255),

                                TextInput::make('phone_number')
                                    ->label('Nomor Telepon')
                                    ->tel()
                                    ->maxLength(255),

                                TextInput::make('class')
                                    ->label('Kelas')
                                    ->placeholder('contoh: 12A, 10B'),

                                Select::make('gender')
                                    ->label('Jenis Kelamin')
                                    ->options([
                                        'male' => 'Laki-laki',
                                        'female' => 'Perempuan',
                                    ]),

                                Select::make('religion')
                                    ->label('Agama')
                                    ->options([
                                        'islam' => 'Islam',
                                        'christian' => 'Kristen Protestan',
                                        'catholic' => 'Katolik',
                                        'hindu' => 'Hindu',
                                        'buddhist' => 'Buddha',
                                        'confucian' => 'Konghucu',
                                        'other' => 'Lainnya',
                                    ]),

                                Select::make('membership_status')
                                    ->label('Status Keanggotaan')
                                    ->options([
                                        'active' => 'Aktif',
                                        'suspended' => 'Ditangguhkan',
                                        'expired' => 'Kadaluarsa',
                                    ])
                                    ->default('active')
                                    ->required()
                                    ->columnSpanFull(),
                            ]),

                        Grid::make(2)
                            ->schema([
                                DatePicker::make('birth_date')
                                    ->label('Tanggal Lahir'),

                                TextInput::make('birth_place')
                                    ->label('Tempat Lahir')
                                    ->maxLength(255),

                                DatePicker::make('join_date')
                                    ->label('Tanggal Bergabung')
                                    ->disabled()
                                    ->dehydrated(false)
                                    ->displayFormat('d M Y')
                                    ->columnSpanFull()
                                    ->visible(fn($livewire) => method_exists($livewire, 'getRecord') && $livewire->getRecord() !== null),

                                FileUpload::make('profile_photo')
                                    ->label('Foto Profil')
                                    ->image()
                                    ->imageEditor()
                                    ->directory('users')
                                    ->visibility('public')
                                    ->columnSpanFull(),
                            ]),

                        Textarea::make('address')
                            ->label('Alamat')
                            ->rows(3)
                            ->columnSpanFull(),
                    ]),
            ]);
    }
}
