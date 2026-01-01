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
    protected $description = 'Regenerate all QR codes as base64 data URLs';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('🔄 Regenerating QR codes as base64 data URLs...');

        if ($this->option('user-id')) {
            $this->regenerateSingleUserQRCode((int) $this->option('user-id'));
        } else {
            $this->regenerateAllQRCodes();
        }

        $this->info('✅ QR code regeneration completed!');
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
                // Generate new QR code
                $uniqueQrCode = 'LIB_USER_'.strtoupper(substr(md5($userDetail->user_id.$userDetail->id.$userDetail->user?->email.now()->timestamp), 0, 12));
                $qrCodeData = app(BarcodeService::class)->generateUserQRCode($userDetail->user_id, $uniqueQrCode);

                // Update user detail
                $userDetail->update(['qr_code' => $qrCodeData]);

                $this->info("   ✅ {$userDetail->user?->name}: {$uniqueQrCode}");
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

            // Generate new QR code
            $uniqueQrCode = 'LIB_USER_'.strtoupper(substr(md5($userDetail->user_id.$userDetail->id.$userDetail->user?->email.now()->timestamp), 0, 12));
            $qrCodeData = app(BarcodeService::class)->generateUserQRCode($userDetail->user_id, $uniqueQrCode);

            // Update user detail
            $userDetail->update(['qr_code' => $qrCodeData]);

            $this->info("   ✅ QR Code generated: {$uniqueQrCode}");
        } catch (\Exception $e) {
            $this->error("❌ Failed: {$e->getMessage()}");
        }
    }
}
