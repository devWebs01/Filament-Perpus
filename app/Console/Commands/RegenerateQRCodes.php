<?php

namespace App\Console\Commands;

use App\Models\UserDetail;
use App\Services\BarcodeService;
use Illuminate\Console\Command;

class RegenerateQRCodes extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'lib:regenerate-qr-codes
                            {--user-id= : Regenerate QR code for specific user ID}
                            {--book-id= : Regenerate QR code for specific book ID}
                            {--force : Force regenerate even if exists}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Regenerate QR codes (defaults to missing only, use --force to overwrite)';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('🔄 Checking QR codes...');

        $force = $this->option('force');

        if ($this->option('user-id')) {
            $this->regenerateSingleUserQRCode((int) $this->option('user-id'), $force);
        } elseif ($this->option('book-id')) {
            $this->regenerateSingleBookQRCode((int) $this->option('book-id'), $force);
        } else {
            $this->regenerateAllQRCodes($force);
        }

        $this->info('✅ Operation completed!');
        $this->displayStorageInfo();
    }

    /**
     * Regenerate all QR codes
     */
    private function regenerateAllQRCodes(bool $force): void
    {
        // 1. Process Users
        $userDetails = UserDetail::with('user')->get();
        $this->info('👥 Processing '.$userDetails->count().' users...');

        $userCount = 0;
        foreach ($userDetails as $userDetail) {
            if ($this->processUserQRCode($userDetail, $force)) {
                $userCount++;
            }
        }

        // 2. Process Books
        $books = \App\Models\Book::all();
        $this->info('📚 Processing '.$books->count().' books...');

        $bookCount = 0;
        foreach ($books as $book) {
            if ($this->processBookQRCode($book, $force)) {
                $bookCount++;
            }
        }

        $this->newLine();
        $this->info("Summary: Generated {$userCount} user codes and {$bookCount} book codes.");
    }

    /**
     * Regenerate QR code for a specific user
     */
    private function regenerateSingleUserQRCode(int $userId, bool $force): void
    {
        $userDetail = UserDetail::with('user')->where('user_id', $userId)->first();

        if (! $userDetail) {
            $this->error("❌ User with ID {$userId} not found!");

            return;
        }

        $this->processUserQRCode($userDetail, $force);
    }

    /**
     * Regenerate QR code for a specific book
     */
    private function regenerateSingleBookQRCode(int $bookId, bool $force): void
    {
        $book = \App\Models\Book::find($bookId);

        if (! $book) {
            $this->error("❌ Book with ID {$bookId} not found!");

            return;
        }

        $this->processBookQRCode($book, $force);
    }

    /**
     * Process User Logic (Generate if missing or force)
     * Returns true if generated
     */
    private function processUserQRCode(UserDetail $userDetail, bool $force): bool
    {
        // Check if exists
        if (! $force && $userDetail->barcode && $userDetail->barcode_image && app(BarcodeService::class)->barcodeExists($userDetail->barcode_image)) {
            // $this->line("   ⏩ Skipped {$userDetail->user?->name} (already exists)");
            return false;
        }

        try {
            // Delete old if exists (only if strictly regenerating)
            $oldImagePath = $userDetail->barcode_image;
            if ($oldImagePath && ! str_starts_with($oldImagePath, 'data:')) {
                app(BarcodeService::class)->deleteBarcode($oldImagePath);
            }

            // Generate
            $result = app(BarcodeService::class)->generateUserBarcode($userDetail->user_id);

            // Update
            $userDetail->update([
                'barcode' => $result['code'],
                'barcode_image' => $result['image_path'],
            ]);

            $this->info("   ✅ User generated: {$userDetail->user?->name}");

            return true;
        } catch (\Exception $e) {
            $this->error("   ❌ Failed User {$userDetail->user_id}: {$e->getMessage()}");

            return false;
        }
    }

    /**
     * Process Book Logic
     * Returns true if generated
     */
    private function processBookQRCode(\App\Models\Book $book, bool $force): bool
    {
        // Check if exists
        if (! $force && $book->barcode && $book->barcode_image && app(BarcodeService::class)->barcodeExists($book->barcode_image)) {
            return false;
        }

        try {
            // Delete old
            $oldImagePath = $book->barcode_image;
            if ($oldImagePath) {
                app(BarcodeService::class)->deleteBarcode($oldImagePath);
            }

            // Generate
            $result = app(BarcodeService::class)->generateBookBarcodeWithCode($book->id);

            // Update
            $book->update([
                'barcode' => $result['code'],
                'barcode_image' => $result['image_path'],
            ]);

            $this->info("   ✅ Book generated: {$book->title}");

            return true;
        } catch (\Exception $e) {
            $this->error("   ❌ Failed Book {$book->id}: {$e->getMessage()}");

            return false;
        }
    }

    /**
     * Display storage information
     */
    private function displayStorageInfo(): void
    {
        $this->newLine();
        $this->info('📁 Storage Information:');
        $this->info('═══════════════════════════════════════════════════════════');

        $userQrDir = storage_path('app/public/user_barcode');
        $bookQrDir = storage_path('app/public/book_barcode');

        $userFiles = is_dir($userQrDir) ? count(glob($userQrDir.'/*.png')) : 0;
        $bookFiles = is_dir($bookQrDir) ? count(glob($bookQrDir.'/*.png')) : 0;

        $this->info("   📱 User QR codes: {$userFiles} files in user_barcode/");
        $this->info("   📚 Book QR codes: {$bookFiles} files in book_barcode/");
        $this->info('═══════════════════════════════════════════════════════════');
    }
}
