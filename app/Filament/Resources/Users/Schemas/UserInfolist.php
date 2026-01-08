<?php

namespace App\Filament\Resources\Users\Schemas;

use Filament\Infolists\Components\ImageEntry;
use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class UserInfolist
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
                                TextEntry::make('name')
                                    ->label('Nama Lengkap'),

                                TextEntry::make('email')
                                    ->label('Alamat Email')
                                    ->copyable()
                                    ->icon('heroicon-o-envelope'),
                            ]),
                        Grid::make(2)
                            ->schema([
                                ImageEntry::make('userDetail.profile_photo')
                                    ->label('Foto Profil')
                                    ->disk('public')
                                    ->extraImgAttributes(['style' => 'max-width: 100px; height: auto;'])
                                    ->defaultImageUrl(
                                        fn ($record) => 'https://api.dicebear.com/9.x/lorelei/svg?seed='.urlencode($record->name)
                                    ),

                                ImageEntry::make('userDetail.barcode_image')
                                    ->label(fn ($record) => 'Kode Barcode '.($record->userDetail?->barcode ?? 'N/A'))
                                    ->disk('public')
                                    ->extraImgAttributes(['style' => 'max-width: 100px; height: auto;'])
                                    ->placeholder('-'),
                            ]),
                    ])
                    ->columnSpanFull(),

                Section::make('Detail Pengguna')
                    ->description('Informasi detail pengguna')
                    ->schema([
                        Grid::make(2)
                            ->schema([
                                TextEntry::make('roles')
                                    ->label('Peran')
                                    ->badge()
                                    ->formatStateUsing(
                                        fn ($state) => collect($state)
                                            ->pluck('name')
                                            ->map(fn ($role) => str_replace('_', ' ', ucfirst($role)))
                                            ->join(', ')
                                    )
                                    ->color('primary'),
                                TextEntry::make('userDetail.nik')
                                    ->label('NIK')
                                    ->default('-'),

                                TextEntry::make('userDetail.nis')
                                    ->label('NIS')
                                    ->default('-'),

                                TextEntry::make('userDetail.nisn')
                                    ->label('NISN')
                                    ->default('-'),

                                TextEntry::make('userDetail.phone_number')
                                    ->label('Nomor Telepon')
                                    ->default('-'),

                                TextEntry::make('userDetail.class')
                                    ->label('Kelas')
                                    ->default('-'),

                                TextEntry::make('userDetail.gender')
                                    ->label('Jenis Kelamin')
                                    ->formatStateUsing(fn (?string $state) => match ($state) {
                                        'male' => 'Laki-laki',
                                        'female' => 'Perempuan',
                                        default => '-',
                                    }),

                                TextEntry::make('userDetail.religion')
                                    ->label('Agama')
                                    ->formatStateUsing(fn (?string $state) => match ($state) {
                                        'islam' => 'Islam',
                                        'christian' => 'Kristen Protestan',
                                        'catholic' => 'Katolik',
                                        'hindu' => 'Hindu',
                                        'buddhist' => 'Buddha',
                                        'confucian' => 'Konghucu',
                                        'other' => 'Lainnya',
                                        default => '-',
                                    }),

                                TextEntry::make('userDetail.membership_status')
                                    ->label('Status Keanggotaan')
                                    ->badge()
                                    ->color(fn (?string $state) => match ($state) {
                                        'active' => 'success',
                                        'suspended' => 'warning',
                                        'expired' => 'danger',
                                        default => 'gray',
                                    }),
                            ]),

                        Grid::make(2)
                            ->schema([
                                TextEntry::make('userDetail.birth_place')
                                    ->label('Tempat Lahir')
                                    ->default('-'),

                                TextEntry::make('userDetail.birth_date')
                                    ->label('Tanggal Lahir')
                                    ->formatStateUsing(fn ($state) => (is_string($state) && ! empty($state)) ? $state : (($state && ! is_string($state)) ? $state->format('d M Y') : '-'))
                                    ->default('-'),

                                TextEntry::make('userDetail.join_date')
                                    ->label('Tanggal Bergabung')
                                    ->formatStateUsing(fn ($state) => (is_string($state) && ! empty($state)) ? $state : (($state && ! is_string($state)) ? $state->format('d M Y') : '-'))
                                    ->default('-'),
                            ]),

                        TextEntry::make('userDetail.address')
                            ->label('Alamat')
                            ->columnSpanFull()
                            ->markdown()
                            ->default('Tidak ada alamat'),
                    ])->columnSpanFull(),
            ]);
    }
}
