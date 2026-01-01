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
    protected $signature = 'lib:regenerate-qr-codes {--user-id= : Regenerate QR code for specific user ID}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Regenerate all QR codes as files (users stored in user_barcode/, books in book_barcode/)';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('🔄 Regenerating QR codes as files...');

        if ($this->option('user-id')) {
            $this->regenerateSingleUserQRCode((int) $this->option('user-id'));
        } else {
            $this->regenerateAllQRCodes();
        }

        $this->info('✅ QR code regeneration completed!');
        $this->displayStorageInfo();
    }

    /**
     * Regenerate QR codes for all users
     */
    private function regenerateAllQRCodes(): void
    {
        $userDetails = UserDetail::with('user')->get();

        $this->info('📱 Regenerating QR codes for '.$userDetails->count().' users...');

        foreach ($userDetails as $userDetail) {
            try {
                // Delete old QR code file if exists
                $oldImagePath = $userDetail->barcode_image;
                if ($oldImagePath && ! str_starts_with($oldImagePath, 'data:')) {
                    app(BarcodeService::class)->deleteBarcode($oldImagePath);
                }

                // Generate new QR code (returns array with code and image_path)
                $result = app(BarcodeService::class)->generateUserBarcode($userDetail->user_id);

                // Update user detail with separate fields
                $userDetail->update([
                    'barcode' => $result['code'],
                    'barcode_image' => $result['image_path'],
                ]);

                $this->info("   ✅ {$userDetail->user?->name}: {$result['code']}");
                $this->info("      📁 {$result['image_path']}");
            } catch (\Exception $e) {
                $this->error("   ❌ Failed for {$userDetail->user?->name}: {$e->getMessage()}");
            }
        }
    }

    /**
     * Regenerate QR code for a specific user
     */
    private function regenerateSingleUserQRCode(int $userId): void
    {
        $userDetail = UserDetail::with('user')->find($userId);

        if (! $userDetail) {
            $this->error("❌ User with ID {$userId} not found!");

            return;
        }

        try {
            $this->info("🔄 Regenerating QR code for {$userDetail->user?->name}...");

            // Delete old QR code file if exists
            $oldImagePath = $userDetail->barcode_image;
            if ($oldImagePath && ! str_starts_with($oldImagePath, 'data:')) {
                app(BarcodeService::class)->deleteBarcode($oldImagePath);
            }

            // Generate new QR code (returns array with code and image_path)
            $result = app(BarcodeService::class)->generateUserBarcode($userDetail->user_id);

            // Update user detail with separate fields
            $userDetail->update([
                'barcode' => $result['code'],
                'barcode_image' => $result['image_path'],
            ]);

            $this->info("   ✅ QR Code generated: {$result['code']}");
            $this->info("      📁 {$result['image_path']}");
        } catch (\Exception $e) {
            $this->error("❌ Failed: {$e->getMessage()}");
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
