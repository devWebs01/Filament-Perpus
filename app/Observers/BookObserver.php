<?php

namespace App\Observers;

use App\Models\Book;
use App\Services\BarcodeService;
use Illuminate\Support\Facades\Log;

/**
 * BookObserver
 *
 * Observer untuk menangani event pada model Book.
 * Fungsi:
 * 1. Generate barcode code unik saat create/update
 * 2. Generate barcode image dan simpan path ke field barcode (JSON)
 *
 * Format field barcode (JSON):
 * {
 *   "code": "BOK_44SVZVB2T36X",
 *   "image": "barcodes/book_1_BOK_44SVZVB2T36X.png"
 * }
 */
class BookObserver
{
    private BarcodeService $barcodeService;

    public function __construct(
        BarcodeService $barcodeService
    ) {
        $this->barcodeService = $barcodeService;
    }

    /**
     * Handle the Book "creating" event.
     *
     * Generate barcode code jika belum ada.
     *
     * @throws \Exception
     */
    public function creating(Book $book): void
    {
        // Cek apakah field barcode kosong atau belum memiliki code
        $barcodeData = BarcodeService::parseBarcode($book->barcode);

        if (empty($barcodeData['code'])) {
            try {
                $barcodeCode = 'BOK_'.uniqid();
                $barcode = $this->barcodeService->generateBookBarcode($book->id, $barcodeCode);

                Log::info('Barcode code generated for new book', [
                    'title' => $book->title,
                    'barcode' => $barcode,
                ]);

                // Set barcode code saja dulu (image akan dibuat di created hook)
                $book->barcode = json_encode(['code' => $barcode]);
            } catch (\Exception $e) {
                Log::error('Failed to generate barcode code for book', [
                    'title' => $book->title,
                    'error' => $e->getMessage(),
                ]);

                throw $e;
            }
        }
    }

    /**
     * Handle the Book "created" event.
     *
     * Generate barcode image setelah book disimpan.
     */
    public function created(Book $book): void
    {
        try {
            $barcodeData = BarcodeService::parseBarcode($book->barcode);

            if (! empty($barcodeData['code'])) {
                // Generate barcode image
                $barcodeImage = $this->barcodeService->generateBookBarcode(
                    $book->id,
                    $barcodeData['code']
                );

                // Update field barcode dengan code + image path
                $book->update([
                    'barcode' => json_encode($barcodeImage),
                ]);

                Log::info('Book created with barcode image', [
                    'id' => $book->id,
                    'title' => $book->title,
                    'barcode' => $barcodeImage['code'],
                    'barcode_image' => $barcodeImage['image'],
                ]);
            }
        } catch (\Exception $e) {
            Log::error('Failed to generate barcode image after book creation', [
                'id' => $book->id,
                'title' => $book->title,
                'error' => $e->getMessage(),
            ]);
        }
    }

    /**
     * Handle the Book "updating" event.
     *
     * Generate barcode code jika belum ada saat update.
     *
     * @throws \Exception
     */
    public function updating(Book $book): void
    {
        $barcodeData = BarcodeService::parseBarcode($book->barcode);
        $originalBarcodeData = BarcodeService::parseBarcode($book->getOriginal('barcode'));
        $barcodeWasChanged = $book->isDirty('barcode');

        // Generate barcode code jika original belum ada dan user tidak mengubahnya
        if (empty($originalBarcodeData['code']) && ! $barcodeWasChanged && empty($barcodeData['code'])) {
            try {
                $barcodeCode = 'BOK_'.uniqid();
                $barcode = $this->barcodeService->generateBookBarcode($book->id, $barcodeCode);

                Log::info('Barcode code generated during book update', [
                    'id' => $book->id,
                    'title' => $book->title,
                    'barcode' => $barcode,
                ]);

                $book->barcode = json_encode(['code' => $barcode]);
            } catch (\Exception $e) {
                Log::error('Failed to generate barcode code during book update', [
                    'id' => $book->id,
                    'title' => $book->title,
                    'error' => $e->getMessage(),
                ]);

                throw $e;
            }
        }
    }

    /**
     * Handle the Book "updated" event.
     *
     * Regenerate barcode image jika barcode code berubah.
     */
    public function updated(Book $book): void
    {
        if ($book->wasChanged('barcode')) {
            try {
                $originalBarcodeData = BarcodeService::parseBarcode($book->getOriginal('barcode'));
                $newBarcodeData = BarcodeService::parseBarcode($book->barcode);

                // Jika code berubah, regenerate image
                if ($originalBarcodeData['code'] !== $newBarcodeData['code']) {
                    $barcodeImage = $this->barcodeService->regenerate(
                        $originalBarcodeData['image'] ?? null,
                        $newBarcodeData['code'],
                        'book_'.$book->id.'_'.$newBarcodeData['code']
                    );

                    // Update dengan barcode baru (code + image)
                    $book->update([
                        'barcode' => json_encode($barcodeImage),
                    ]);

                    Log::info('Book barcode updated with new image', [
                        'id' => $book->id,
                        'old_code' => $originalBarcodeData['code'],
                        'new_code' => $newBarcodeData['code'],
                    ]);
                }
            } catch (\Exception $e) {
                Log::error('Failed to regenerate barcode image', [
                    'id' => $book->id,
                    'error' => $e->getMessage(),
                ]);
            }
        }
    }

    /**
     * Handle the Book "deleting" event.
     */
    public function deleting(Book $book): void
    {
        Log::info('Book being deleted', [
            'id' => $book->id,
            'title' => $book->title,
        ]);
    }

    /**
     * Handle the Book "deleted" event.
     *
     * Hapus barcode image saat book dihapus.
     */
    public function deleted(Book $book): void
    {
        try {
            $barcodeData = BarcodeService::parseBarcode($book->barcode);

            if (! empty($barcodeData['image'])) {
                $this->barcodeService->deleteBarcode($barcodeData['image']);
            }

            Log::info('Book deleted with barcode image removed', [
                'id' => $book->id,
                'title' => $book->title,
            ]);
        } catch (\Exception $e) {
            Log::warning('Failed to delete barcode image on book deletion', [
                'id' => $book->id,
                'error' => $e->getMessage(),
            ]);
        }
    }

    /**
     * Handle the Book "restored" event.
     */
    public function restored(Book $book): void
    {
        Log::info('Book restored', [
            'id' => $book->id,
            'title' => $book->title,
        ]);
    }

    /**
     * Handle the Book "force deleted" event.
     */
    public function forceDeleted(Book $book): void
    {
        try {
            $barcodeData = BarcodeService::parseBarcode($book->barcode);

            if (! empty($barcodeData['image'])) {
                $this->barcodeService->deleteBarcode($barcodeData['image']);
            }

            Log::warning('Book force deleted with barcode image removed', [
                'id' => $book->id,
            ]);
        } catch (\Exception $e) {
            Log::error('Failed to delete barcode image on book force deletion', [
                'id' => $book->id,
                'error' => $e->getMessage(),
            ]);
        }
    }
}
