<?php

namespace App\Filament\Resources\Transactions\Schemas;

use App\Models\Book;
use App\Models\Status;
use App\Models\User;
use Filament\Actions\Action;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Hidden;
use Filament\Forms\Components\Placeholder;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class TransactionForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                static::getBarcodeScanningSection(),
                static::getTransactionDetailsSection(),
            ]);
    }

    /**
     * Section untuk Barcode Scanning
     */
    protected static function getBarcodeScanningSection(): Section
    {
        return Section::make('Pindai Barcode')
            ->description('Scan barcode kartu anggota dan buku, atau pilih manual')
            ->schema([
                // Hidden fields untuk menyimpan hasil scan
                Hidden::make('scanned_user_id'),
                Hidden::make('scanned_book_id'),

                // Barcode Input untuk User
                TextInput::make('user_barcode')
                    ->label('Barcode Kartu Anggota')
                    ->placeholder('Scan QR Code kartu anggota atau ketik NIS/NISN')
                    ->suffixAction(
                        Action::make('scan_user_barcode')
                            ->icon('heroicon-o-arrow-path')
                            ->label('Scan')
                            ->color('primary')
                            ->requiresConfirmation(false)
                            ->action(function () {
                                // Ini akan di-handle via Livewire atau API call
                                return 'Scanned';
                            })
                    )
                    ->reactive()
                    ->afterStateUpdated(function ($state, callable $set) {
                        if (empty($state)) {
                            return;
                        }

                        // Cari user berdasarkan barcode
                        $userDetail = \App\Models\UserDetail::where('qr_code', $state)
                            ->orWhere('nis', $state)
                            ->orWhere('nisn', $state)
                            ->first();

                        if ($userDetail) {
                            $set('user_id', $userDetail->user_id);
                            $set('user_name', $userDetail->user->name);
                        } else {
                            $set('user_id', null);
                            $set('user_name', 'User tidak ditemukan');
                        }
                    }),

                // Display user info setelah scan
                Placeholder::make('user_name')
                    ->label('Anggota')
                    ->content('Belum ada kartu yang dipindai'),

                // Barcode Input untuk Book
                TextInput::make('book_barcode')
                    ->label('Barcode Buku')
                    ->placeholder('Scan barcode buku atau ketik ISBN')
                    ->suffixAction(
                        Action::make('scan_book_barcode')
                            ->icon('heroicon-o-arrow-path')
                            ->label('Scan')
                            ->color('primary')
                    )
                    ->reactive()
                    ->afterStateUpdated(function ($state, callable $set) {
                        if (empty($state)) {
                            return;
                        }

                        // Cari buku berdasarkan barcode
                        $book = \App\Models\Book::where('barcode', $state)
                            ->orWhere('isbn', $state)
                            ->first();

                        if ($book) {
                            $set('book_id', $book->id);
                            $set('book_title', $book->title);
                            $set('book_available', $book->isAvailable() ? 'Tersedia' : 'Tidak Tersedia');
                        } else {
                            $set('book_id', null);
                            $set('book_title', 'Buku tidak ditemukan');
                            $set('book_available', '-');
                        }
                    }),

                // Display book info setelah scan
                Placeholder::make('book_title')
                    ->label('Buku')
                    ->content('Belum ada buku yang dipindai'),

                Placeholder::make('book_available')
                    ->label('Ketersediaan')
                    ->content('-'),
            ])
            ->columns(2);
    }

    /**
     * Section untuk Detail Transaksi
     */
    protected static function getTransactionDetailsSection(): Section
    {
        return Section::make('Detail Transaksi')
            ->schema([
                Select::make('user_id')
                    ->label('Anggota')
                    ->options(function () {
                        return \App\Models\User::with('userDetail')
                            ->get()
                            ->pluck('name', 'id')
                            ->toArray();
                    })
                    ->searchable()
                    ->required()
                    ->live()
                    ->afterStateUpdated(function ($state, callable $set) {
                        if ($state) {
                            $user = \App\Models\User::find($state);
                            $userDetail = $user?->userDetail;
                            $set('user_nis', $userDetail?->nis ?? '-');
                            $set('user_class', $userDetail?->class ?? '-');
                        }
                    }),

                // Display user detail info
                TextInput::make('user_nis')
                    ->label('NIS')
                    ->disabled()
                    ->dehydrated(false),

                TextInput::make('user_class')
                    ->label('Kelas')
                    ->disabled()
                    ->dehydrated(false),

                Select::make('book_id')
                    ->label('Buku')
                    ->options(function () {
                        return \App\Models\Book::pluck('title', 'id')
                            ->toArray();
                    })
                    ->searchable()
                    ->required()
                    ->live()
                    ->afterStateUpdated(function ($state, callable $set) {
                        if ($state) {
                            $book = \App\Models\Book::find($state);
                            $set('book_author', $book?->author ?? '-');
                            $set('book_available_count', $book?->getAvailableCount() ?? 0);
                        }
                    }),

                // Display book detail info
                TextInput::make('book_author')
                    ->label('Penulis')
                    ->disabled()
                    ->dehydrated(false),

                TextInput::make('book_available_count')
                    ->label('Stok Tersedia')
                    ->disabled()
                    ->dehydrated(false),

                DatePicker::make('borrow_date')
                    ->label('Tanggal Pinjam')
                    ->default(now())
                    ->required()
                    ->native(false),

                DatePicker::make('due_date')
                    ->label('Jatuh Tempo')
                    ->default(now()->addDays(7))
                    ->required()
                    ->native(false)
                    ->minDate(now()),

                Select::make('status_id')
                    ->label('Status')
                    ->relationship('status', 'name')
                    ->default(function () {
                        return Status::where('name', 'Menunggu Persetujuan')->first()?->id;
                    })
                    ->required()
                    ->disabled(fn (string $context): bool => $context === 'edit'),

                TextInput::make('penalty_total')
                    ->label('Denda (Rp)')
                    ->numeric()
                    ->default(0)
                    ->disabled()
                    ->dehydrated(),
            ])
            ->columns(2);
    }
}
