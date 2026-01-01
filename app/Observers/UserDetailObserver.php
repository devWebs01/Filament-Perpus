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
 *
 * Field terpisah:
 * - barcode: menyimpan kode QR (e.g., LIB_USER_XXX)
 * - barcode_image: menyimpan path gambar QR (e.g., user_barcode/user_1_LIB_USER_XXX.png)
 */
class UserDetailObserver
{
    /**
     * Handle the UserDetail "creating" event.
     *
     * Event ini dipicu SEBELUM record disimpan ke database.
     * Generate QR code hanya jika field barcode kosong.
     *
     * @param  UserDetail  $userDetail  Instance UserDetail yang sedang dibuat
     */
    public function creating(UserDetail $userDetail): void
    {
        // Hanya generate jika barcode kosong atau null
        if (empty($userDetail->barcode)) {
            try {
                $barcodeService = app(BarcodeService::class);
                $result = $barcodeService->generateUserBarcode(
                    userId: $userDetail->user_id ?? null
                );

                // Set field terpisah
                $userDetail->barcode = $result['code'];
                $userDetail->barcode_image = $result['image_path'];

                Log::info('QR Code generated for new user', [
                    'user_id' => $userDetail->user_id,
                    'barcode' => $userDetail->barcode,
                    'barcode_image' => $userDetail->barcode_image,
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
            'barcode' => $userDetail->barcode,
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
        // Cek apakah barcode akan diupdate menjadi null/kosong
        // ATAU barcode saat ini kosong dan tidak ada perubahan explicit
        $originalQrCode = $userDetail->getOriginal('barcode');
        $newQrCode = $userDetail->barcode;

        // Generate jika:
        // 1. QR code asli kosong/null
        // 2. QR code baru juga kosong (tidak di-set manual)
        if (empty($originalQrCode) && empty($newQrCode)) {
            try {
                $barcodeService = app(BarcodeService::class);
                $result = $barcodeService->generateUserBarcode(
                    userId: $userDetail->user_id ?? null
                );

                // Set field terpisah
                $userDetail->barcode = $result['code'];
                $userDetail->barcode_image = $result['image_path'];

                Log::info('QR Code generated during user update', [
                    'id' => $userDetail->id,
                    'user_id' => $userDetail->user_id,
                    'barcode' => $userDetail->barcode,
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
        // Log hanya jika ada perubahan barcode
        if ($userDetail->wasChanged('barcode')) {
            Log::info('UserDetail QR Code updated', [
                'id' => $userDetail->id,
                'user_id' => $userDetail->user_id,
                'old_barcode' => $userDetail->getOriginal('barcode'),
                'new_barcode' => $userDetail->barcode,
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
            'barcode' => $userDetail->barcode,
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
        // Hapus file QR code dari storage
        if (! empty($userDetail->barcode_image)) {
            try {
                $barcodeService = app(BarcodeService::class);
                $barcodeService->deleteBarcode($userDetail->barcode_image);

                Log::info('UserDetail deleted with QR code file removed from user_barcode/', [
                    'id' => $userDetail->id,
                    'user_id' => $userDetail->user_id,
                ]);
            } catch (\Exception $e) {
                Log::warning('Failed to delete QR code file on user deletion', [
                    'id' => $userDetail->id,
                    'error' => $e->getMessage(),
                ]);
            }
        }
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
            'barcode' => $userDetail->barcode,
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
            'barcode' => $userDetail->barcode,
        ]);
    }
}
