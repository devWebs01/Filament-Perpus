<?php

use Illuminate\Support\Facades\Auth;
use App\Models\Book;
use App\Services\BorrowBookService;
use Jantinnerezo\LivewireAlert\Facades\LivewireAlert;
use function Livewire\Volt\state;

state(['book']);

$borrow_transaction = function () {
    $alertDuration = config('app.library.alert_duration', 3000);

    if (!Auth::check()) {
        LivewireAlert::title('Gagal')->position('center')->timer($alertDuration)->text('Kamu harus login dulu untuk meminjam buku')->error()->show();
        $this->dispatch('redirect-after-alert', url('/admin'));
        return;
    }

    try {
        // Inisialisasi service
        $borrowBookService = app(BorrowBookService::class);

        // Validasi kelayakan peminjaman terlebih dahulu
        $validation = $borrowBookService->validateBorrowEligibility($this->book);

        if (!$validation['is_valid']) {
            $errorMessage = implode('. ', $validation['errors']);
            LivewireAlert::title('Gagal')->position('center')->timer($alertDuration)->text($errorMessage)->error()->show();
            $this->dispatch('close-borrow-modal');
            $this->dispatch('redirect-after-alert', route('book-detail', ['book' => $this->book->id]));
            return;
        }

        // Proses peminjaman dengan service yang sudah diperbaiki
        $result = $borrowBookService->processBorrow($this->book);

        if ($result['success']) {
            $message = $result['message'];
            if (isset($result['data']['remaining_stock'])) {
                $message .= " (Stok tersedia: {$result['data']['remaining_stock']})";
            }

            LivewireAlert::title('Berhasil')->position('center')->timer($alertDuration)->text($message)->success()->show();

            // Tutup modal otomatis
            $this->dispatch('close-borrow-modal');
            $this->dispatch('refresh-book-detail'); // Untuk refresh tampilan jika diperlukan
        } else {
            LivewireAlert::title('Gagal')->position('center')->timer($alertDuration)->text($result['message'])->error()->show();
            $this->dispatch('close-borrow-modal');
        }

        $this->dispatch('redirect-after-alert', route('my-bookmarks'));
    } catch (\Throwable $th) {
        report($th);

        // Tutup modal otomatis
        $this->dispatch('close-borrow-modal');
        LivewireAlert::title('Gagal')->position('center')->timer($alertDuration)->text('Terjadi kesalahan sistem. Silakan coba lagi nanti')->error()->show();
    }
};
?>

<div>
    <x-modal id="borrow_book" title="Konfirmasi Peminjaman">
        <div class="text-center">
            <img src="{{ asset('images/thinking_illustration.png') }}" class="w-44 mx-auto mb-4"
                alt="thinking illustration">

            <p class="py-4 text-gray-700 dark:text-gray-100">
                Yakin kamu ingin meminjam buku
                <strong>{{ $book->title }}</strong>?
            </p>

            <x-slot:actions>
                <x-button label="Kembali" type="button" onclick="borrow_book.close()"
                    class="flex-1 border hover:bg-gray-100 dark:bg-gray-500 dark:hover:bg-gray-600 text-black dark:text-white" />

                <x-button label="Ya, Yakin" wire:click="borrow_transaction" spinner
                    class="flex-1 bg-primary-600 hover:bg-primary-700 dark:bg-primary-500 dark:hover:bg-primary-600 text-white" />
            </x-slot:actions>
        </div>
    </x-modal>

    {{-- Tutup modal otomatis setelah berhasil --}}
    <script>
        (function() {
            'use strict';

            // Define handlers once
            const closeBorrowModalHandler = () => {
                const modal = document.getElementById('borrow_book');
                if (modal && typeof modal.close === 'function') {
                    try {
                        modal.close();
                    } catch (e) {
                        console.error('Error closing modal:', e);
                    }
                }
            };

            const redirectAfterAlertHandler = (event) => {
                const url = event.detail;
                if (url && typeof url === 'string') {
                    setTimeout(() => {
                        try {
                            window.location.href = url;
                        } catch (e) {
                            console.error('Error redirecting:', e);
                        }
                    }, 3000);
                }
            };

            // Add event listeners
            window.addEventListener('close-borrow-modal', closeBorrowModalHandler);
            window.addEventListener('redirect-after-alert', redirectAfterAlertHandler);

            // Cleanup on Livewire updates
            if (typeof Livewire !== 'undefined') {
                Livewire.hook('message.failed', () => {
                    window.removeEventListener('close-borrow-modal', closeBorrowModalHandler);
                    window.removeEventListener('redirect-after-alert', redirectAfterAlertHandler);
                });
            }
        })();
    </script>
</div>