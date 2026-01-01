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
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Utilities\Get;
use Filament\Schemas\Components\Utilities\Set;
use JeffersonGoncalves\Filament\QrCodeField\Forms\Components\QrCodeInput;

class TransactionForm
{
    /**
     * Konfigurasi schema untuk form transaksi
     */
    public static function configure(): array
    {
        return [
            static::getBarcodeScanningSection(),
            static::getTransactionDetailsSection(),
        ];
    }

    /**
     * Section untuk Barcode Scanning
     */
    protected static function getBarcodeScanningSection(): Section
    {
        return Section::make('Pindai Barcode')
            ->description('Scan barcode kartu anggota atau buku menggunakan kamera')
            ->collapsible()
            ->schema([
                // Input QR Code
                QrCodeInput::make('qrcode_scanner')
                    ->label('Scan QR Code')
                    ->placeholder('Arahkan kamera ke QR code')
                    ->helperText('Scan kartu anggota terlebih dahulu, kemudian scan buku')
                    ->live(debounce: 300)
                    ->afterStateUpdated(function (Get $get, Set $set, ?string $state) {
                        if (! $state) {
                            return;
                        }

                        // Proses hasil scan
                        static::processScanResult(trim($state), $get, $set);
                    }),

                // Info anggota dari scan
                Placeholder::make('scanned_member_info')
                    ->label('Info Anggota')
                    ->content(fn (Get $get) => static::getMemberInfoPlaceholder($get))
                    ->visible(fn (Get $get) => ! empty($get('user_id'))),

                // Info buku dari scan
                Placeholder::make('scanned_book_info')
                    ->label('Info Buku')
                    ->content(fn (Get $get) => static::getBookInfoPlaceholder($get))
                    ->visible(fn (Get $get) => ! empty($get('book_id'))),
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
                            ->where('available_count', '>', 0)
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
                            $set('book_available_count', $book->available_count ?? 0);
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
            ->columns(2);
    }

    /**
     * Proses hasil scan QR code menggunakan BarcodeScannerService
     */
    protected static function processScanResult(string $qrcode, Get $get, Set $set): void
    {
        if (empty($qrcode)) {
            return;
        }

        // Gunakan BarcodeScannerService untuk scan
        $scanner = app(BarcodeScannerService::class);

        // Tentukan tipe barcode
        $barcodeType = $scanner->parseBarcodeType($qrcode);

        if ($barcodeType['type'] === 'user') {
            // Scan user
            $result = $scanner->scanUserBarcode($qrcode);

            if ($result['success']) {
                $userDetail = $result['user'];
                $set('user_id', $userDetail->user_id);
                $set('user_nis', $userDetail->nis ?? '-');
                $set('user_class', $userDetail->class ?? '-');
                $set('qrcode_scanner', '');

                Notification::make()
                    ->title('User Ditemukan')
                    ->body("Anggota: {$userDetail->user->name}")
                    ->success()
                    ->send();
            } else {
                Notification::make()
                    ->title('User Tidak Ditemukan')
                    ->body($result['message'])
                    ->warning()
                    ->send();
            }
        } elseif ($barcodeType['type'] === 'book') {
            // Scan buku
            $result = $scanner->scanBookBarcode($qrcode);

            if ($result['success']) {
                $book = $result['book'];
                $set('book_id', $book->id);
                $set('book_author', $book->author ?? '-');
                $set('book_available_count', $book->available_count ?? 0);
                $set('qrcode_scanner', '');

                Notification::make()
                    ->title('Buku Ditemukan')
                    ->body("Buku: {$book->title} (Stok: {$book->available_count})")
                    ->success()
                    ->send();
            } else {
                Notification::make()
                    ->title('Buku Tidak Ditemukan')
                    ->body($result['message'])
                    ->warning()
                    ->send();
            }
        } else {
            Notification::make()
                ->title('QR Code Tidak Dikenali')
                ->body('Format QR code tidak valid atau tidak ditemukan di sistem')
                ->danger()
                ->send();
        }

        // Reset scanner
        $set('qrcode_scanner', '');
    }

    /**
     * Generate placeholder text untuk info anggota
     */
    protected static function getMemberInfoPlaceholder(Get $get): string
    {
        $userId = $get('user_id');
        if (! $userId) {
            return 'Belum ada anggota yang dipilih';
        }

        $user = User::find($userId);
        if (! $user) {
            return 'Data anggota tidak ditemukan';
        }

        $userDetail = $user->userDetail;
        $nis = $userDetail?->nis ?? '-';
        $class = $userDetail?->class ?? '-';
        $qrCode = $userDetail?->barcode ?? '-';

        // Parse QR code jika JSON
        if (is_string($qrCode) && str_starts_with($qrCode, '{')) {
            $parsed = json_decode($qrCode, true);
            $qrCodeDisplay = $parsed['code'] ?? '-';
        } else {
            $qrCodeDisplay = '-';
        }

        return "
            <div class='space-y-1'>
                <div><strong>Nama:</strong> {$user->name}</div>
                <div><strong>NIS:</strong> {$nis}</div>
                <div><strong>Kelas:</strong> {$class}</div>
                <div><strong>Kode QR:</strong> {$qrCodeDisplay}</div>
            </div>
        ";
    }

    /**
     * Generate placeholder text untuk info buku
     */
    protected static function getBookInfoPlaceholder(Get $get): string
    {
        $bookId = $get('book_id');
        if (! $bookId) {
            return 'Belum ada buku yang dipilih';
        }

        $book = Book::find($bookId);
        if (! $book) {
            return 'Data buku tidak ditemukan';
        }

        $isbn = $book->isbn ?? '-';
        $author = $book->author ?? '-';
        $status = $book->available_count > 0 ? '✓ Tersedia' : '✗ Habis';
        $barcode = $book->barcode ?? '-';

        // Parse barcode jika path file
        if (is_string($barcode) && str_starts_with($barcode, 'qrcodes/')) {
            $barcodeDisplay = substr($barcode, 0, 30).'...';
        } else {
            $barcodeDisplay = $barcode;
        }

        return "
            <div class='space-y-1'>
                <div><strong>Judul:</strong> {$book->title}</div>
                <div><strong>Penulis:</strong> {$author}</div>
                <div><strong>ISBN:</strong> {$isbn}</div>
                <div><strong>Barcode:</strong> {$barcodeDisplay}</div>
                <div><strong>Stok:</strong> {$book->available_count} {$status}</div>
            </div>
        ";
    }
}
