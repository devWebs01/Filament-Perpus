<?php

namespace App\Observers;

use App\Models\UserDetail;
use App\Services\BarcodeService;
use Illuminate\Support\Facades\Log;

/**
 * UserDetailObserver
 *
 * Observer untuk menangani event pada model UserDetail.
 * Fungsi utama: generate QR code/barcode unik secara otomatis
 * saat create atau update jika belum ada.
 */
class UserDetailObserver
{
    /**
     * Constructor: inject BarcodeService
     */
    public function __construct(
        /**
         * Instance BarcodeService
         */
        protected BarcodeService $barcodeService
    ) {}

    /**
     * Handle the UserDetail "creating" event.
     *
     * Event ini dipicu SEBELUM record disimpan ke database.
     * Generate QR code hanya jika field qr_code kosong.
     *
     * @param  UserDetail  $userDetail  Instance UserDetail yang sedang dibuat
     */
    public function creating(UserDetail $userDetail): void
    {
        // Hanya generate jika qr_code kosong atau null
        if (empty($userDetail->qr_code)) {
            try {
                $userDetail->qr_code = $this->barcodeService->generateUserBarcode(
                    userId: $userDetail->user_id ?? null
                );

                Log::info('QR Code generated for new user', [
                    'user_id' => $userDetail->user_id,
                    'qr_code' => $userDetail->qr_code,
                ]);
            } catch (\Exception $e) {
                // Log error tapi jangan gagalkan proses create
                Log::error('Failed to generate QR code for user', [
                    'user_id' => $userDetail->user_id,
                    'error' => $e->getMessage(),
                ]);

                // Re-throw jika ingin proses create gagal saat barcode gagal
                throw $e;
            }
        }
    }

    /**
     * Handle the UserDetail "created" event.
     *
     * Event ini dipicu SETELAH record berhasil disimpan.
     * Bisa digunakan untuk post-processing seperti logging, cache, dll.
     *
     * @param  UserDetail  $userDetail  Instance UserDetail yang baru dibuat
     */
    public function created(UserDetail $userDetail): void
    {
        Log::info('UserDetail created', [
            'id' => $userDetail->id,
            'user_id' => $userDetail->user_id,
            'qr_code' => $userDetail->qr_code,
        ]);
    }

    /**
     * Handle the UserDetail "updating" event.
     *
     * Event ini dipicu SEBELUM record di-update di database.
     * Generate QR code hanya jika:
     * 1. QR code saat ini kosong/null
     * 2. QR code belum pernah di-set sebelumnya
     *
     * @param  UserDetail  $userDetail  Instance UserDetail yang sedang di-update
     */
    public function updating(UserDetail $userDetail): void
    {
        // Cek apakah qr_code akan diupdate menjadi null/kosong
        // ATAU qr_code saat ini kosong dan tidak ada perubahan explicit
        $originalQrCode = $userDetail->getOriginal('qr_code');
        $newQrCode = $userDetail->qr_code;

        // Generate jika:
        // 1. QR code asli kosong/null
        // 2. QR code baru juga kosong (tidak di-set manual)
        if (empty($originalQrCode) && empty($newQrCode)) {
            try {
                $userDetail->qr_code = $this->barcodeService->generateUserBarcode(
                    userId: $userDetail->user_id ?? null
                );

                Log::info('QR Code generated during user update', [
                    'id' => $userDetail->id,
                    'user_id' => $userDetail->user_id,
                    'qr_code' => $userDetail->qr_code,
                ]);
            } catch (\Exception $e) {
                Log::error('Failed to generate QR code during user update', [
                    'id' => $userDetail->id,
                    'user_id' => $userDetail->user_id,
                    'error' => $e->getMessage(),
                ]);

                throw $e;
            }
        }
    }

    /**
     * Handle the UserDetail "updated" event.
     *
     * Event ini dipricu SETELAH record berhasil di-update.
     *
     * @param  UserDetail  $userDetail  Instance UserDetail yang baru di-update
     */
    public function updated(UserDetail $userDetail): void
    {
        // Log hanya jika ada perubahan qr_code
        if ($userDetail->wasChanged('qr_code')) {
            Log::info('UserDetail QR Code updated', [
                'id' => $userDetail->id,
                'user_id' => $userDetail->user_id,
                'old_qr_code' => $userDetail->getOriginal('qr_code'),
                'new_qr_code' => $userDetail->qr_code,
            ]);
        }
    }

    /**
     * Handle the UserDetail "deleting" event.
     *
     * Dipicu SEBELUM soft delete atau hard delete.
     *
     * @param  UserDetail  $userDetail  Instance UserDetail yang akan dihapus
     */
    public function deleting(UserDetail $userDetail): void
    {
        Log::info('UserDetail being deleted', [
            'id' => $userDetail->id,
            'user_id' => $userDetail->user_id,
            'qr_code' => $userDetail->qr_code,
        ]);
    }

    /**
     * Handle the UserDetail "deleted" event.
     *
     * Dipicu SETELAH soft delete (jika menggunakan SoftDeletes)
     * atau hard delete.
     *
     * @param  UserDetail  $userDetail  Instance UserDetail yang dihapus
     */
    public function deleted(UserDetail $userDetail): void
    {
        Log::info('UserDetail deleted', [
            'id' => $userDetail->id,
            'user_id' => $userDetail->user_id,
            'qr_code' => $userDetail->qr_code,
        ]);
    }

    /**
     * Handle the UserDetail "restored" event.
     *
     * Dipicu saat soft-deleted record di-restore.
     *
     * @param  UserDetail  $userDetail  Instance UserDetail yang di-restore
     */
    public function restored(UserDetail $userDetail): void
    {
        Log::info('UserDetail restored', [
            'id' => $userDetail->id,
            'user_id' => $userDetail->user_id,
            'qr_code' => $userDetail->qr_code,
        ]);
    }

    /**
     * Handle the UserDetail "force deleted" event.
     *
     * Dipricu saat record di-hard delete dari soft delete.
     *
     * @param  UserDetail  $userDetail  Instance UserDetail yang di-force delete
     */
    public function forceDeleted(UserDetail $userDetail): void
    {
        Log::warning('UserDetail force deleted', [
            'id' => $userDetail->id,
            'user_id' => $userDetail->user_id,
            'qr_code' => $userDetail->qr_code,
        ]);
    }
}
