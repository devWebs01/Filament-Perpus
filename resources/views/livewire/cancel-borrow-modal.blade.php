<?php

use App\Models\Transaction;
use App\Services\BorrowBookService;
use Jantinnerezo\LivewireAlert\Facades\LivewireAlert;
use function Livewire\Volt\state;

state(['transactionId']);

$cancel_borrow = function () {
    try {
        // Ambil transaksi
        $transaction = Transaction::find($this->transactionId);

        if (!$transaction) {
            LivewireAlert::title('Gagal')->position('center')->timer(3000)->text('Transaksi tidak ditemukan')->error()->show();
            $this->dispatch('close-cancel-modal');
            return;
        }

        // Inisialisasi service
        $borrowBookService = app(BorrowBookService::class);

        // Proses pembatalan peminjaman
        $result = $borrowBookService->cancelBorrow($transaction);

        if ($result['success']) {
            LivewireAlert::title('Berhasil')->position('center')->timer(3000)->text($result['message'])->success()->show();

            // Tutup modal otomatis
            $this->dispatch('close-cancel-modal');
            $this->dispatch('refresh-transactions'); // Untuk refresh daftar transaksi
        } else {
            LivewireAlert::title('Gagal')->position('center')->timer(3000)->text($result['message'])->error()->show();
            $this->dispatch('close-cancel-modal');
        }
    } catch (\Throwable $th) {
        report($th);

        // Tutup modal otomatis
        $this->dispatch('close-cancel-modal');
        LivewireAlert::title('Gagal')->position('center')->timer(3000)->text('Terjadi kesalahan sistem. Silakan coba lagi nanti')->error()->show();
    }
};
?>

<div>
    <dialog id="cancel_borrow" class="modal modal-bottom sm:modal-middle">
        <div class="modal-box text-gray-70 dark:text-gray-100 text-center">
            <img src="{{ asset('images/thinking_illustration.png') }}" class="w-44 mx-auto mb-4"
                alt="thinking illustration">

            <p class="py-4">
                Yakin kamu ingin membatalkan permintaan peminjaman buku ini?
            </p>

            <div class="modal-action flex gap-4 w-full">
                <form method="dialog" class="flex w-full gap-4">
                    <button
                        class="btn px-4 border hover:bg-gray-100 dark:bg-gray-500 dark:hover:bg-gray-600 text-black dark:text-white flex-1">Kembali</button>
                    <button type="button" wire:click="cancel_borrow"
                        class="btn bg-primary-600 hover:bg-primary-700 dark:bg-primary-500 dark:hover:bg-primary-600 text-white flex-1">
                        <span wire:loading class="loading loading-spinner loading-md"></span>

                        Ya, Batalkan
                    </button>
                </form>
            </div>
        </div>
    </dialog>

    {{-- Tutup modal otomatis setelah berhasil --}}
    <script>
        window.addEventListener('close-cancel-modal', () => {
            document.getElementById('cancel_borrow')?.close();
        });

        // Refresh daftar transaksi setelah pembatalan
        window.addEventListener('refresh-transactions', () => {
            // Anda bisa menambahkan logika refresh di sini
            location.reload();
        });
    </script>
</div>
