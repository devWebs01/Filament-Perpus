<?php

namespace App\Observers;

use App\Models\Book;
use App\Services\BarcodeService;
use Illuminate\Support\Facades\Log;

/**
 * BookObserver
 *
 * Observer untuk menangani event pada model Book.
 * Fungsi utama: generate barcode unik secara otomatis
 * saat create atau update jika belum ada.
 */
class BookObserver
{
    /**
     * Instance BarcodeService
     */
    protected BarcodeService $barcodeService;

    /**
     * Constructor: inject BarcodeService
     */
    public function __construct(BarcodeService $barcodeService)
    {
        $this->barcodeService = $barcodeService;
    }

    /**
     * Handle the Book "creating" event.
     *
     * Event ini dipicu SEBELUM record disimpan ke database.
     * Generate barcode hanya jika field barcode kosong.
     *
     * @param  Book  $book  Instance Book yang sedang dibuat
     */
    public function creating(Book $book): void
    {
        // Hanya generate jika barcode kosong atau null
        if (empty($book->barcode)) {
            try {
                $book->barcode = $this->barcodeService->generateBookBarcode(
                    bookId: $book->id ?? null
                );

                Log::info('Barcode generated for new book', [
                    'title' => $book->title,
                    'barcode' => $book->barcode,
                ]);
            } catch (\Exception $e) {
                // Log error tapi jangan gagalkan proses create
                Log::error('Failed to generate barcode for book', [
                    'title' => $book->title,
                    'error' => $e->getMessage(),
                ]);

                // Re-throw jika ingin proses create gagal saat barcode gagal
                throw $e;
            }
        }
    }

    /**
     * Handle the Book "created" event.
     *
     * Event ini dipicu SETELAH record berhasil disimpan.
     * Bisa digunakan untuk post-processing seperti logging, cache, dll.
     *
     * @param  Book  $book  Instance Book yang baru dibuat
     */
    public function created(Book $book): void
    {
        Log::info('Book created', [
            'id' => $book->id,
            'title' => $book->title,
            'barcode' => $book->barcode,
        ]);
    }

    /**
     * Handle the Book "updating" event.
     *
     * Event ini dipicu SEBELUM record di-update di database.
     * Generate barcode hanya jika:
     * 1. Barcode saat ini kosong/null
     * 2. Barcode belum pernah di-set sebelumnya
     *
     * @param  Book  $book  Instance Book yang sedang di-update
     */
    public function updating(Book $book): void
    {
        // Cek apakah barcode akan diupdate menjadi null/kosong
        // ATAU barcode saat ini kosong dan tidak ada perubahan explicit
        $originalBarcode = $book->getOriginal('barcode');
        $newBarcode = $book->barcode;

        // Generate jika:
        // 1. Barcode asli kosong/null
        // 2. Barcode baru juga kosong (tidak di-set manual)
        if (empty($originalBarcode) && empty($newBarcode)) {
            try {
                $book->barcode = $this->barcodeService->generateBookBarcode(
                    bookId: $book->id ?? null
                );

                Log::info('Barcode generated during book update', [
                    'id' => $book->id,
                    'title' => $book->title,
                    'barcode' => $book->barcode,
                ]);
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
     * Event ini dipricu SETELAH record berhasil di-update.
     *
     * @param  Book  $book  Instance Book yang baru di-update
     */
    public function updated(Book $book): void
    {
        // Log hanya jika ada perubahan barcode
        if ($book->wasChanged('barcode')) {
            Log::info('Book barcode updated', [
                'id' => $book->id,
                'title' => $book->title,
                'old_barcode' => $book->getOriginal('barcode'),
                'new_barcode' => $book->barcode,
            ]);
        }
    }

    /**
     * Handle the Book "deleting" event.
     *
     * Dipicu SEBELUM soft delete atau hard delete.
     *
     * @param  Book  $book  Instance Book yang akan dihapus
     */
    public function deleting(Book $book): void
    {
        Log::info('Book being deleted', [
            'id' => $book->id,
            'title' => $book->title,
            'barcode' => $book->barcode,
        ]);
    }

    /**
     * Handle the Book "deleted" event.
     *
     * Dipicu SETELAH soft delete (jika menggunakan SoftDeletes)
     * atau hard delete.
     *
     * @param  Book  $book  Instance Book yang dihapus
     */
    public function deleted(Book $book): void
    {
        Log::info('Book deleted', [
            'id' => $book->id,
            'title' => $book->title,
            'barcode' => $book->barcode,
        ]);
    }

    /**
     * Handle the Book "restored" event.
     *
     * Dipicu saat soft-deleted record di-restore.
     *
     * @param  Book  $book  Instance Book yang di-restore
     */
    public function restored(Book $book): void
    {
        Log::info('Book restored', [
            'id' => $book->id,
            'title' => $book->title,
            'barcode' => $book->barcode,
        ]);
    }

    /**
     * Handle the Book "force deleted" event.
     *
     * Dipricu saat record di-hard delete dari soft delete.
     *
     * @param  Book  $book  Instance Book yang di-force delete
     */
    public function forceDeleted(Book $book): void
    {
        Log::warning('Book force deleted', [
            'id' => $book->id,
            'title' => $book->title,
            'barcode' => $book->barcode,
        ]);
    }
}
