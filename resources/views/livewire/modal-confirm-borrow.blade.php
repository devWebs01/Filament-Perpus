<?php

use Illuminate\Support\Facades\Auth;
use App\Models\Book;
use App\Services\BorrowBookService;
use Jantinnerezo\LivewireAlert\Facades\LivewireAlert;
use function Livewire\Volt\state;

state(['book']);

$borrow_transaction = function () {
    if (!Auth::check()) {
        LivewireAlert::title('Gagal')->position('center')->timer(3000)->text('Kamu harus login dulu untuk meminjam buku')->error()->show();
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
            LivewireAlert::title('Gagal')->position('center')->timer(3000)->text($errorMessage)->error()->show();
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

            LivewireAlert::title('Berhasil')->position('center')->timer(3000)->text($message)->success()->show();

            // Tutup modal otomatis
            $this->dispatch('close-borrow-modal');
            $this->dispatch('refresh-book-detail'); // Untuk refresh tampilan jika diperlukan
        } else {
            LivewireAlert::title('Gagal')->position('center')->timer(3000)->text($result['message'])->error()->show();
            $this->dispatch('close-borrow-modal');
        }

        $this->dispatch('redirect-after-alert', route('my-books'));
    } catch (\Throwable $th) {
        report($th);

        // Tutup modal otomatis
        $this->dispatch('close-borrow-modal');
        LivewireAlert::title('Gagal')->position('center')->timer(3000)->text('Terjadi kesalahan sistem. Silakan coba lagi nanti')->error()->show();
    }
};
?>

<div>
    <dialog id="borrow_book" class="modal modal-bottom sm:modal-middle">
        <div class="modal-box text-gray-700 dark:text-gray-100 text-center">
            <img src="{{ asset('images/thinking_illustration.png') }}" class="w-44 mx-auto mb-4"
                alt="thinking illustration">

            <p class="py-4">
                Yakin kamu ingin meminjam buku
                <strong>{{ $book->title }}</strong>?
            </p>

            <div class="modal-action flex gap-4 w-full">
                <form method="dialog" class="flex w-full gap-4">
                    <button
                        class="btn px-4 border hover:bg-gray-100 dark:bg-gray-500 dark:hover:bg-gray-600 text-black dark:text-white flex-1">Kembali</button>
                    <button type="button" wire:click="borrow_transaction"
                        class="btn bg-primary-600 hover:bg-primary-700 dark:bg-primary-500 dark:hover:bg-primary-600 text-white flex-1">
                        <span wire:loading class="loading loading-spinner loading-md"></span>

                        Ya, Yakin
                    </button>
                </form>
            </div>
        </div>
    </dialog>

    {{-- Tutup modal otomatis setelah berhasil --}}
    <script>
        window.addEventListener('close-borrow-modal', () => {
            document.getElementById('borrow_book')?.close();
        });

        // Redirect setelah alert selesai ditampilkan
        window.addEventListener('redirect-after-alert', (event) => {
            setTimeout(() => {
                window.location.href = event.detail;
            }, 3000); // Delay 3 detik agar alert selesai ditampilkan
        });
    </script>
</div>
