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
 * 2. Generate barcode image dan simpan path ke field barcode_image
 *
 * Field terpisah:
 * - barcode: menyimpan kode barcode (e.g., BOK_XXX)
 * - barcode_image: menyimpan path gambar barcode (e.g., book_barcode/book_1_BOK_XXX.png)
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
     * Note: ID belum tersedia saat creating, jadi hanya generate code.
     *
     * @throws \Exception
     */
    public function creating(Book $book): void
    {
        // Cek apakah field barcode sudah ada
        if (empty($book->barcode)) {
            try {
                $barcodeCode = 'BOK_'.strtolower(uniqid());

                // Store barcode code sementara (akan diupdate di created hook dengan ID)
                $book->barcode = $barcodeCode;

                Log::info('Barcode code generated for new book', [
                    'title' => $book->title,
                    'barcode' => $barcodeCode,
                ]);
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
     * Generate barcode image setelah book disimpan (ID sudah tersedia).
     */
    public function created(Book $book): void
    {
        try {
            // Gunakan barcode yang disimpan saat creating, atau generate baru
            $barcodeCode = $book->barcode ?? 'BOK_'.strtolower(uniqid());

            // Generate barcode image dengan ID yang sudah tersedia
            $barcodePath = $this->barcodeService->generateBookBarcode(
                $book->id,
                $barcodeCode
            );

            // Update field barcode_image dengan path image
            $book->update([
                'barcode' => $barcodeCode,
                'barcode_image' => $barcodePath,
            ]);

            Log::info('Book created with barcode image', [
                'id' => $book->id,
                'title' => $book->title,
                'barcode' => $barcodeCode,
                'barcode_path' => $barcodePath,
            ]);
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
        $barcodeWasChanged = $book->isDirty('barcode');
        $barcodeImageWasChanged = $book->isDirty('barcode_image');

        // Generate barcode jika belum ada dan user tidak mengubahnya
        if (empty($book->getOriginal('barcode')) && ! $barcodeWasChanged && ! $barcodeImageWasChanged) {
            try {
                $barcodeCode = 'BOK_'.strtolower(uniqid());
                $barcodePath = $this->barcodeService->generateBookBarcode($book->id, $barcodeCode);

                Log::info('Barcode generated during book update', [
                    'id' => $book->id,
                    'title' => $book->title,
                    'barcode_path' => $barcodePath,
                ]);

                $book->barcode = $barcodeCode;
                $book->barcode_image = $barcodePath;
            } catch (\Exception $e) {
                Log::error('Failed to generate barcode during book update', [
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
     * Barcode yang di-generate melalui observer sudah diset di updating hook.
     */
    public function updated(Book $book): void
    {
        // Barcode sudah di-generate di updating hook, tidak perlu additional logic
        if ($book->wasChanged('barcode') || $book->wasChanged('barcode_image')) {
            Log::info('Book barcode updated', [
                'id' => $book->id,
                'title' => $book->title,
            ]);
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
            if (! empty($book->barcode_image)) {
                $this->barcodeService->deleteBarcode($book->barcode_image);
            }

            Log::info('Book deleted with barcode image removed from book_barcode/', [
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
            if (! empty($book->barcode_image)) {
                $this->barcodeService->deleteBarcode($book->barcode_image);
            }

            Log::warning('Book force deleted with barcode image removed', [
                'id' => $book->id,
                'title' => $book->title,
            ]);
        } catch (\Exception $e) {
            Log::error('Failed to delete barcode image on book force deletion', [
                'id' => $book->id,
                'error' => $e->getMessage(),
            ]);
        }
    }
}
