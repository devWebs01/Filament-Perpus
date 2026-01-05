<?php

use App\Models\Transaction;
use App\Services\BorrowBookService;
use Jantinnerezo\LivewireAlert\Facades\LivewireAlert;
use function Livewire\Volt\state;

state(['transactionId']);

$cancel_borrow = function () {
    $alertDuration = config('app.library.alert_duration', 3000);

    try {
        // Ambil transaksi
        $transaction = Transaction::find($this->transactionId);

        if (!$transaction) {
            LivewireAlert::title('Gagal')->position('center')->timer($alertDuration)->text('Transaksi tidak ditemukan')->error()->show();
            $this->dispatch('close-cancel-modal');
            return;
        }

        // Inisialisasi service
        $borrowBookService = app(BorrowBookService::class);

        // Proses pembatalan peminjaman
        $result = $borrowBookService->cancelBorrow($transaction);

        if ($result['success']) {
            LivewireAlert::title('Berhasil')->position('center')->timer($alertDuration)->text($result['message'])->success()->show();

            // Tutup modal otomatis
            $this->dispatch('close-cancel-modal');
            $this->dispatch('refresh-transactions'); // Untuk refresh daftar transaksi
        } else {
            LivewireAlert::title('Gagal')->position('center')->timer($alertDuration)->text($result['message'])->error()->show();
            $this->dispatch('close-cancel-modal');
        }
    } catch (\Throwable $th) {
        report($th);

        // Tutup modal otomatis
        $this->dispatch('close-cancel-modal');
        LivewireAlert::title('Gagal')->position('center')->timer($alertDuration)->text('Terjadi kesalahan sistem. Silakan coba lagi nanti')->error()->show();
    }
};
?>

<div>
    <x-modal id="cancel_borrow" title="Konfirmasi Pembatalan">
        <div class="text-center">
            <img src="{{ asset('images/thinking_illustration.png') }}" class="w-44 mx-auto mb-4"
                alt="thinking illustration">

            <p class="py-4 text-gray-700 dark:text-gray-100">
                Yakin kamu ingin membatalkan permintaan peminjaman buku ini?
            </p>

            <x-slot:actions>
                <x-button label="Kembali" type="button" onclick="cancel_borrow.close()"
                    class="flex-1 border hover:bg-gray-100 dark:bg-gray-500 dark:hover:bg-gray-600 text-black dark:text-white" />

                <x-button label="Ya, Batalkan" wire:click="cancel_borrow" spinner
                    class="flex-1 bg-primary-600 hover:bg-primary-700 dark:bg-primary-500 dark:hover:bg-primary-600 text-white" />
            </x-slot:actions>
        </div>
    </x-modal>

    {{-- Tutup modal otomatis setelah berhasil --}}
    <script>
        (function() {
            'use strict';

            // Define handlers once
            const closeCancelModalHandler = () => {
                const modal = document.getElementById('cancel_borrow');
                if (modal && typeof modal.close === 'function') {
                    try {
                        modal.close();
                    } catch (e) {
                        console.error('Error closing modal:', e);
                    }
                }
            };

            const refreshTransactionsHandler = () => {
                try {
                    location.reload();
                } catch (e) {
                    console.error('Error refreshing:', e);
                }
            };

            // Add event listeners
            window.addEventListener('close-cancel-modal', closeCancelModalHandler);
            window.addEventListener('refresh-transactions', refreshTransactionsHandler);

            // Cleanup on Livewire updates
            if (typeof Livewire !== 'undefined') {
                Livewire.hook('message.failed', () => {
                    window.removeEventListener('close-cancel-modal', closeCancelModalHandler);
                    window.removeEventListener('refresh-transactions', refreshTransactionsHandler);
                });
            }
        })();
    </script>
</div>
