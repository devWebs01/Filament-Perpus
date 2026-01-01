<?php

namespace App\Filament\Resources\Transactions\Schemas;

use App\Models\Book;
use App\Models\Status;
use App\Models\User;
use App\Services\BarcodeScannerService;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Placeholder;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Notifications\Notification;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Utilities\Get;
use Filament\Schemas\Components\Utilities\Set;
use Illuminate\Support\HtmlString;
use JeffersonGoncalves\Filament\QrCodeField\Forms\Components\QrCodeInput;

class TransactionForm
{
    /**
     * Konfigurasi schema untuk form transaksi
     */
    public static function configure(\Filament\Schemas\Schema $schema): \Filament\Schemas\Schema
    {
        return $schema->components([
            static::getMemberScanningSection(),
            static::getBookScanningSection(),
            static::getTransactionDetailsSection(),
        ]);
    }

    /**
     * Section untuk Scan Kartu Anggota
     */
    protected static function getMemberScanningSection(): Section
    {
        return Section::make('Scan Kartu Anggota')
            ->description('Scan QR code kartu anggota perpustakaan')
            ->collapsible()
            ->schema([
                Grid::make(2)->schema([
                    // Input QR Code Anggota
                    QrCodeInput::make('member_qrcode_scanner')
                        ->label('Scan Kartu Anggota')
                        ->placeholder('Arahkan kamera ke QR code kartu')
                        ->live(debounce: 300)
                        ->afterStateUpdated(function (Get $get, Set $set, ?string $state) {
                            if (! $state) {
                                return;
                            }

                            static::processMemberScan(trim($state), $get, $set);
                        })
                        ->columnSpanFull(),

                    // Info anggota dari scan
                    Placeholder::make('scanned_member_info')
                        ->label('Info Anggota')
                        ->content(fn (Get $get) => static::getMemberInfoPlaceholder($get))
                        ->visible(fn (Get $get) => ! empty($get('user_id'))),
                ]),
            ])
            ->columns(1);
    }

    /**
     * Section untuk Scan Barcode Buku
     */
    protected static function getBookScanningSection(): Section
    {
        return Section::make('Scan Barcode Buku')
            ->description('Scan barcode buku yang akan dipinjam')
            ->collapsible()
            ->schema([
                Grid::make(2)->schema([
                    // Input Barcode Buku
                    QrCodeInput::make('book_barcode_scanner')
                        ->label('Barcode Buku')
                        ->placeholder('Scan atau masukkan kode barcode buku')
                        ->live(debounce: 300)
                        ->afterStateUpdated(function (Get $get, Set $set, ?string $state) {
                            if (! $state) {
                                return;
                            }

                            static::processBookScan(trim($state), $get, $set);
                        })
                        ->columnSpanFull(),

                    // Info buku dari scan
                    Placeholder::make('scanned_book_info')
                        ->label('Info Buku')
                        ->content(fn (Get $get) => static::getBookInfoPlaceholder($get))
                        ->visible(fn (Get $get) => ! empty($get('book_id'))),
                ]),
            ])
            ->columns(1);
    }

    /**
     * Section untuk Detail Transaksi
     */
    protected static function getTransactionDetailsSection(): Section
    {
        return Section::make('Detail Transaksi')
            ->description('Informasi lengkap peminjaman buku')
            ->schema([
                // User Selection
                Select::make('user_id')
                    ->label('Anggota')
                    ->placeholder('Pilih anggota atau scan kartu')
                    ->searchable()
                    ->preload()
                    ->allowHtml()
                    ->getSearchResultsUsing(function (string $search): array {
                        return User::where('name', 'like', "%{$search}%")
                            ->orWhere('email', 'like', "%{$search}%")
                            ->limit(10)
                            ->pluck('name', 'id')
                            ->toArray();
                    })
                    ->getOptionLabelUsing(fn ($value) => User::find($value)?->name ?? '-')
                    ->required()
                    ->live(debounce: 300)
                    ->afterStateUpdated(function (Get $get, Set $set, ?string $state) {
                        if (! $state) {
                            $set('user_nis', '');
                            $set('user_class', '');

                            return;
                        }

                        $user = User::find($state);
                        if ($user) {
                            $userDetail = $user->userDetail;
                            $set('user_nis', $userDetail?->nis ?? '-');
                            $set('user_class', $userDetail?->class ?? '-');
                        }
                    }),

                // User NIS
                TextInput::make('user_nis')
                    ->label('NIS')
                    ->disabled()
                    ->dehydrated(false)
                    ->columnSpan(1),

                // User Class
                TextInput::make('user_class')
                    ->label('Kelas')
                    ->disabled()
                    ->dehydrated(false)
                    ->columnSpan(1),

                // Book Selection
                Select::make('book_id')
                    ->label('Buku')
                    ->placeholder('Pilih buku atau scan barcode')
                    ->searchable()
                    ->preload()
                    ->allowHtml()
                    ->getSearchResultsUsing(function (string $search): array {
                        return Book::where('title', 'like', "%{$search}%")
                            ->orWhere('isbn', 'like', "%{$search}%")
                            ->whereColumn('book_count', '>', 0)
                            ->limit(10)
                            ->pluck('title', 'id')
                            ->toArray();
                    })
                    ->getOptionLabelUsing(function ($value) {
                        $book = Book::find($value);

                        return $book ? "{$book->title} ({$book->author})" : '-';
                    })
                    ->required()
                    ->live(debounce: 300)
                    ->afterStateUpdated(function (Get $get, Set $set, ?string $state) {
                        if (! $state) {
                            $set('book_author', '');
                            $set('book_available_count', 0);

                            return;
                        }

                        $book = Book::find($state);
                        if ($book) {
                            $set('book_author', $book->author ?? '-');
                            $set('book_available_count', $book->getAvailableCount());
                        }
                    }),

                // Book Author
                TextInput::make('book_author')
                    ->label('Penulis')
                    ->disabled()
                    ->dehydrated(false)
                    ->columnSpan(1),

                // Book Available Count
                TextInput::make('book_available_count')
                    ->label('Stok Tersedia')
                    ->numeric()
                    ->disabled()
                    ->dehydrated(false)
                    ->columnSpan(1),

                // Borrow Date
                DatePicker::make('borrow_date')
                    ->label('Tanggal Pinjam')
                    ->default(now())
                    ->required()
                    ->native(false)
                    ->columnSpan(1),

                // Due Date
                DatePicker::make('due_date')
                    ->label('Jatuh Tempo')
                    ->default(fn () => now()->addDays(7))
                    ->required()
                    ->native(false)
                    ->minDate(now())
                    ->columnSpan(1),

                // Status
                Select::make('status_id')
                    ->label('Status')
                    ->relationship('status', 'name')
                    ->options(function () {
                        return Status::pluck('name', 'id')->toArray();
                    })
                    ->default(function () {
                        return Status::where('name', 'Menunggu Persetujuan')
                            ->first()?->id;
                    })
                    ->required()
                    ->disabled(fn ($context) => $context === 'edit'),

                // Penalty Total
                TextInput::make('penalty_total')
                    ->label('Denda (Rp)')
                    ->numeric()
                    ->default(0)
                    ->minValue(0)
                    ->disabled(fn ($context) => $context === 'create')
                    ->dehydrated(),

                // Notes
                TextInput::make('notes')
                    ->label('Catatan')
                    ->placeholder('Catatan tambahan jika ada')
                    ->maxLength(255)
                    ->columnSpanFull(),
            ])
            ->columns(2)
            ->columnSpanFull();
    }

    /**
     * Proses hasil scan kartu anggota
     */
    protected static function processMemberScan(string $qrcode, Get $get, Set $set): void
    {
        if (empty($qrcode)) {
            return;
        }

        $scanner = app(BarcodeScannerService::class);
        $result = $scanner->scanUserBarcode($qrcode);

        // Always clear scanner field
        $set('member_qrcode_scanner', '');

        if ($result['success']) {
            $userDetail = $result['user'];
            $set('user_id', $userDetail->user_id);
            $set('user_nis', $userDetail->nis ?? '-');
            $set('user_class', $userDetail->class ?? '-');

            Notification::make()
                ->title('Anggota Ditemukan')
                ->body("Anggota: {$userDetail->user->name}")
                ->success()
                ->send();
        } else {
            Notification::make()
                ->title('Anggota Tidak Ditemukan')
                ->body($result['message'])
                ->warning()
                ->send();
        }
    }

    /**
     * Proses hasil scan barcode buku
     */
    protected static function processBookScan(string $barcode, Get $get, Set $set): void
    {
        if (empty($barcode)) {
            return;
        }

        $scanner = app(BarcodeScannerService::class);
        $result = $scanner->scanBookBarcode($barcode);

        // Always clear scanner field
        $set('book_barcode_scanner', '');

        if ($result['success']) {
            $book = $result['book'];
            $available = $result['available']; // Assuming scanBookBarcode returns 'available' boolean

            // Check availability
            if (! $available && $book->getAvailableCount() <= 0) {
                Notification::make()
                    ->title('Stok Buku Habis')
                    ->body("Buku \"{$book->title}\" saat ini tidak tersedia untuk dipinjam.")
                    ->danger()
                    ->send();

                return;
            }

            $set('book_id', $book->id);
            $set('book_author', $book->author ?? '-');
            $set('book_available_count', $book->getAvailableCount());

            Notification::make()
                ->title('Buku Ditemukan')
                ->body("Buku: {$book->title} (Stok: {$book->getAvailableCount()})")
                ->success()
                ->send();
        } else {
            Notification::make()
                ->title('Buku Tidak Ditemukan')
                ->body($result['message'])
                ->warning()
                ->send();
        }
    }

    /**
     * Generate placeholder text untuk info anggota
     */
    protected static function getMemberInfoPlaceholder(Get $get): HtmlString
    {
        $userId = $get('user_id');
        if (! $userId) {
            return new HtmlString('Belum ada anggota yang dipilih');
        }

        $user = User::find($userId);
        if (! $user) {
            return new HtmlString('Data anggota tidak ditemukan');
        }

        $userDetail = $user->userDetail;
        $nis = $userDetail?->nis ?? '-';
        $class = $userDetail?->class ?? '-';
        $barcode = $userDetail?->barcode ?? '-';

        $available = $userDetail?->membership_status === 'active' ? '✓ Aktif' : '✗ Tidak Aktif';

        return new HtmlString(<<<HTML
            <div class="fi-ta-placeholder-text space-y-2">
                <div class="grid grid-cols-2 gap-2">
                    <div>
                        <span class="font-semibold">Nama:</span>
                        <span class="ml-1">{$user->name}</span>
                    </div>
                    <div>
                        <span class="font-semibold">Status:</span>
                        <span class="ml-1">{$available}</span>
                    </div>
                    <div>
                        <span class="font-semibold">NIS:</span>
                        <span class="ml-1">{$nis}</span>
                    </div>
                    <div>
                        <span class="font-semibold">Kelas:</span>
                        <span class="ml-1">{$class}</span>
                    </div>
                </div>
                <div>
                    <span class="font-semibold">Kode Barcode:</span>
                    <span class="ml-1 text-primary-600 dark:text-primary-400">{$barcode}</span>
                </div>
            </div>
            HTML);
    }

    /**
     * Generate placeholder text untuk info buku
     */
    protected static function getBookInfoPlaceholder(Get $get): HtmlString
    {
        $bookId = $get('book_id');
        if (! $bookId) {
            return new HtmlString('Belum ada buku yang dipilih');
        }

        $book = Book::find($bookId);
        if (! $book) {
            return new HtmlString('Data buku tidak ditemukan');
        }

        $isbn = $book->isbn ?? '-';
        $author = $book->author ?? '-';
        $publisher = $book->publisher ?? '-';
        $year = $book->year_published ?? '-';
        $availableCount = $book->getAvailableCount();

        if ($availableCount > 0) {
            $status = '<span class="text-success-600 dark:text-success-400">✓ Tersedia ('.$availableCount.')</span>';
        } else {
            $status = '<span class="text-danger-600 dark:text-danger-400">✗ Habis</span>';
        }

        $barcode = $book->barcode ?? '-';

        return new HtmlString(<<<HTML
            <div class="fi-ta-placeholder-text space-y-2">
                <div>
                    <span class="font-semibold block text-lg">{$book->title}</span>
                </div>
                <div class="grid grid-cols-2 gap-2">
                    <div>
                        <span class="font-semibold">Penulis:</span>
                        <span class="ml-1">{$author}</span>
                    </div>
                    <div>
                        <span class="font-semibold">Penerbit:</span>
                        <span class="ml-1">{$publisher}</span>
                    </div>
                    <div>
                        <span class="font-semibold">ISBN:</span>
                        <span class="ml-1">{$isbn}</span>
                    </div>
                    <div>
                        <span class="font-semibold">Tahun:</span>
                        <span class="ml-1">{$year}</span>
                    </div>
                </div>
                <div class="grid grid-cols-2 gap-2">
                    <div>
                        <span class="font-semibold">Kode Barcode:</span>
                        <span class="ml-1 text-primary-600 dark:text-primary-400">{$barcode}</span>
                    </div>
                    <div>
                        <span class="font-semibold">Stok:</span>
                        <span class="ml-1">{$status}</span>
                    </div>
                </div>
            </div>
            HTML);
    }
}
