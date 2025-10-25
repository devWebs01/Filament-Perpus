<?php

namespace App\Filament\Resources;

use App\Filament\Resources\UserResource\Pages;
use App\Filament\Resources\UserResource\RelationManagers;
use App\Models\User;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules\Password;

class UserResource extends Resource
{
    protected static ?string $model = User::class;

    protected static ?string $navigationIcon = 'heroicon-o-users';

    protected static ?string $navigationGroup = 'Manajemen Pengguna';

    protected static ?int $navigationSort = 1;

    public static function getModelLabel(): string
    {
        return 'Pengguna';
    }

    public static function getPluralModelLabel(): string
    {
        return 'Pengguna';
    }

    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()->with(['userDetail', 'roles']);
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Informasi Pengguna')
                    ->description('Informasi dasar akun pengguna')
                    ->schema([
                        Forms\Components\Grid::make(2)
                            ->schema([
                                Forms\Components\TextInput::make('name')
                                    ->label('Nama Lengkap')
                                    ->required()
                                    ->maxLength(255),

                                Forms\Components\TextInput::make('email')
                                    ->label('Alamat Email')
                                    ->email()
                                    ->required()
                                    ->maxLength(255)
                                    ->unique(ignoreRecord: true),
                            ]),

                        Forms\Components\TextInput::make('password')
                            ->label('Kata Sandi')
                            ->password()
                            ->dehydrateStateUsing(fn ($state) => $state ? Hash::make($state) : null)
                            ->dehydrated(fn ($state) => filled($state))
                            ->required(fn (string $context): bool => $context === 'create')
                            ->rule(Password::default())
                            ->helperText(fn (string $context): string => $context === 'edit' ? 'Kosongkan untuk tetap menggunakan kata sandi saat ini' : 'Kata sandi harus minimal 8 karakter'),
                    ]),

                Forms\Components\Section::make('Detail Pengguna')
                    ->description('Informasi detail tentang pengguna')
                    ->schema([
                        Forms\Components\Grid::make(2)
                            ->schema([
                                Forms\Components\TextInput::make('nik')
                                    ->label('NIK (Nomor Induk Kependudukan)')
                                    ->maxLength(255),

                                Forms\Components\TextInput::make('nis')
                                    ->label('NIS (Nomor Induk Siswa)')
                                    ->maxLength(255),

                                Forms\Components\TextInput::make('nisn')
                                    ->label('NISN (Nomor Induk Siswa Nasional)')
                                    ->maxLength(255),

                                Forms\Components\TextInput::make('phone_number')
                                    ->label('Nomor Telepon')
                                    ->tel()
                                    ->maxLength(255),

                                Forms\Components\TextInput::make('class')
                                    ->label('Kelas')
                                    ->placeholder('contoh: 12A, 10B'),

                                Forms\Components\TextInput::make('major')
                                    ->label('Jurusan')
                                    ->placeholder('contoh: IPA, IPS'),

                                Forms\Components\TextInput::make('semester')
                                    ->label('Semester')
                                    ->numeric()
                                    ->minValue(1)
                                    ->maxValue(8),

                                Forms\Components\Select::make('gender')
                                    ->label('Jenis Kelamin')
                                    ->options([
                                        'male' => 'Laki-laki',
                                        'female' => 'Perempuan',
                                    ]),

                                Forms\Components\Select::make('religion')
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

                                Forms\Components\Select::make('membership_status')
                                    ->label('Status Keanggotaan')
                                    ->options([
                                        'active' => 'Aktif',
                                        'suspended' => 'Ditangguhkan',
                                        'expired' => 'Kadaluarsa',
                                    ])
                                    ->default('active')
                                    ->required(),
                            ]),

                        Forms\Components\Grid::make(2)
                            ->schema([
                                Forms\Components\DatePicker::make('birth_date')
                                    ->label('Tanggal Lahir'),

                                Forms\Components\TextInput::make('birth_place')
                                    ->label('Tempat Lahir')
                                    ->maxLength(255),

                                Forms\Components\DatePicker::make('join_date')
                                    ->label('Tanggal Bergabung')
                                    ->default(now())
                                    ->hidden()
                                    ->readOnly(),

                                Forms\Components\FileUpload::make('profile_photo')
                                    ->label('Foto Profil')
                                    ->image()
                                    ->imageEditor()
                                    ->directory('users')
                                    ->visibility('public')
                                    ->columnSpanFull(),
                            ]),

                        Forms\Components\Textarea::make('address')
                            ->label('Alamat')
                            ->rows(3)
                            ->columnSpanFull(),
                    ]),

                Forms\Components\Section::make('Penugasan Peran')
                    ->description('Tetapkan peran dan izin untuk pengguna')
                    ->schema([
                        Forms\Components\Select::make('roles')
                            ->label('Peran')
                            ->relationship('roles', 'name')
                            ->preload()
                            ->searchable()
                            ->helperText('Pilih satu atau beberapa peran untuk pengguna ini'),
                    ]),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('name')
                    ->label('Nama')
                    ->searchable()
                    ->sortable()
                    ->weight('semibold'),

                Tables\Columns\TextColumn::make('email')
                    ->label('Email')
                    ->searchable()
                    ->copyable()
                    ->copyMessage('Email disalin ke clipboard')
                    ->copyMessageDuration(1500),

                Tables\Columns\TextColumn::make('userDetail.user_type_display_name')
                    ->label('Tipe Pengguna')
                    ->badge()
                    ->color(fn ($record): string => match (true) {
                        $record->userDetail?->isLibraryHead() => 'danger',
                        $record->userDetail?->isStudent() => 'success',
                        $record->userDetail?->isStaff() => 'warning',
                        default => 'gray',
                    }),

                Tables\Columns\TextColumn::make('userDetail.membership_status')
                    ->label('Keanggotaan')
                    ->badge()
                    ->color(fn ($record): string => match ($record->userDetail?->membership_status) {
                        'active' => 'success',
                        'suspended' => 'warning',
                        'expired' => 'danger',
                        default => 'gray',
                    }),

                Tables\Columns\TextColumn::make('userDetail.class')
                    ->label('Kelas')
                    ->searchable()
                    ->toggleable(),

                Tables\Columns\TextColumn::make('userDetail.major')
                    ->label('Jurusan')
                    ->searchable()
                    ->toggleable(),

                Tables\Columns\TextColumn::make('email_verified_at')
                    ->label('Email Terverifikasi')
                    ->dateTime('j M Y')
                    ->sortable()
                    ->toggleable()
                    ->badge()
                    ->color(fn ($record): string => $record->email_verified_at ? 'success' : 'warning'),

                Tables\Columns\TextColumn::make('created_at')
                    ->label('Tanggal Bergabung')
                    ->dateTime('j M Y')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->defaultSort('created_at', 'desc')
            ->filters([
                Tables\Filters\SelectFilter::make('user_type')
                    ->label('Tipe Pengguna')
                    ->options([
                        'student' => 'Siswa',
                        'staff' => 'Staf Perpustakaan',
                        'library_head' => 'Kepala Perpustakaan',
                    ])
                    ->query(function (Builder $query, array $data): Builder {
                        return $query->whereHas('userDetail', function (Builder $query) use ($data) {
                            return match ($data['value']) {
                                'student' => $query->students(),
                                'staff' => $query->staff(),
                                'library_head' => $query->libraryHeads(),
                                default => $query,
                            };
                        });
                    }),

                Tables\Filters\SelectFilter::make('membership_status')
                    ->label('Status Keanggotaan')
                    ->options([
                        'active' => 'Aktif',
                        'suspended' => 'Ditangguhkan',
                        'expired' => 'Kadaluarsa',
                    ])
                    ->query(function (Builder $query, array $data): Builder {
                        return $query->whereHas('userDetail', function (Builder $query) use ($data) {
                            return $query->where('membership_status', $data['value']);
                        });
                    }),

                Tables\Filters\Filter::make('verified')
                    ->query(fn (Builder $query): Builder => $query->whereNotNull('email_verified_at'))
                    ->label('Email Terverifikasi'),

                Tables\Filters\Filter::make('unverified')
                    ->query(fn (Builder $query): Builder => $query->whereNull('email_verified_at'))
                    ->label('Email Belum Terverifikasi'),
            ])
            ->actions([
                Tables\Actions\ViewAction::make(),
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make()
                    ->requiresConfirmation()
                    ->modalHeading('Hapus Pengguna')
                    ->modalDescription('Apakah Anda yakin ingin menghapus pengguna ini? Tindakan ini tidak dapat dibatalkan.')
                    ->modalSubmitActionLabel('Ya, hapus pengguna'),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make()
                        ->requiresConfirmation()
                        ->modalHeading('Hapus Pengguna yang Dipilih')
                        ->modalDescription('Apakah Anda yakin ingin menghapus pengguna ini? Tindakan ini tidak dapat dibatalkan.')
                        ->modalSubmitActionLabel('Ya, hapus pengguna'),
                ]),
            ])
            ->emptyStateHeading('Tidak ada pengguna ditemukan')
            ->emptyStateDescription('Belum ada pengguna yang terdaftar.')
            ->emptyStateActions([
                Tables\Actions\CreateAction::make()
                    ->label('Buat pengguna pertama'),
            ]);
    }

    public static function getRelations(): array
    {
        return [
            RelationManagers\TransactionsRelationManager::class,
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListUsers::route('/'),
            'create' => Pages\CreateUser::route('/create'),
            'edit' => Pages\EditUser::route('/{record}/edit'),
        ];
    }
}
