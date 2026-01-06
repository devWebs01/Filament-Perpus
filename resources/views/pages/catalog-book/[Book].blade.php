<?php

use function Laravel\Folio\name;
use function Livewire\Volt\{state, computed};
use App\Models\{Setting, Transaction};
use App\Services\BorrowBookService;
use Illuminate\Support\Facades\Log;
use Jantinnerezo\LivewireAlert\Facades\LivewireAlert;

name('book-detail');

state('book');
state([
    'setting' => fn() => Setting::first(),
]);

$pendingTransaction = computed(function () {
    if (!auth()->check()) {
        return null;
    }

    return Transaction::where('user_id', auth()->id())
        ->where('book_id', $this->book->id)
        ->whereHas('status', fn($q) => $q->where('name', 'Menunggu Persetujuan'))
        ->first();
});

$borrow_transaction = function () {
    $alertDuration = config('app.library.alert_duration', 3000);

    if (!auth()->check()) {
        return redirect('/admin/login');
    }

    $book = $this->book;

    Log::info('Borrow transaction started', [
        'book_id' => $book?->id,
        'book_title' => $book?->title,
    ]);

    $borrowService = app(BorrowBookService::class);
    $result = $borrowService->processBorrow($book);

    if ($result['success']) {
        LivewireAlert::title('Berhasil')->text($result['message'])->success()->position('center')->timer($alertDuration)->show();

        Log::info('Borrow transaction completed successfully', [
            'transaction_id' => $result['data']['transaction_id'] ?? null,
        ]);

        $this->redirectRoute('my-books');
    } else {
        LivewireAlert::title('Gagal')->text($result['message'])->error()->position('center')->timer($alertDuration)->show();

        Log::warning('Borrow transaction failed', [
            'message' => $result['message'],
        ]);
    }
};

$cancel_borrow = function () {
    $alertDuration = config('app.library.alert_duration', 3000);

    if (!auth()->check()) {
        return redirect('/admin/login');
    }

    $transaction = $this->pendingTransaction;

    if (!$transaction) {
        LivewireAlert::title('Gagal')->text('Tidak ada permintaan peminjaman yang dapat dibatalkan.')->error()->position('center')->timer($alertDuration)->show();
        return;
    }

    Log::info('Cancel borrow request', [
        'transaction_id' => $transaction->id,
    ]);

    $borrowService = app(BorrowBookService::class);
    $result = $borrowService->cancelBorrow($transaction);

    if ($result['success']) {
        LivewireAlert::title('Berhasil')->text($result['message'])->success()->position('center')->timer($alertDuration)->show();

        Log::info('Cancel borrow completed successfully', [
            'transaction_id' => $transaction->id,
        ]);

        $this->redirectRoute('book-detail', ['book' => $this->book]);
    } else {
        LivewireAlert::title('Gagal')->text($result['message'])->error()->position('center')->timer($alertDuration)->show();

        Log::warning('Cancel borrow failed', [
            'message' => $result['message'],
        ]);
    }
};

?>

<x-guest-layout>
    <div>
        @volt
            <div>
                <x-slot name="title">{{ $book->title }} - Detail Buku</x-slot>
                <div class="pt-20">
                    <div class="w-full max-w-7xl mx-auto px-4 sm:px-6">
                        <div class="grid items-start grid-cols-1 lg:grid-cols-5 gap-8 max-lg:gap-12 max-sm:gap-8">
                            <div class="w-full lg:sticky top-0 lg:col-span-2">
                                <div class="flex flex-col">
                                    <div class="bg-white rounded-lg shadow-lg p-8 border border-neutral-100">
                                        <div class="aspect-[3/4] bg-neutral-100 rounded-lg overflow-hidden mb-6">
                                            <img src="{{ Storage::url($book->image) }}" alt="{{ $book->title }}"
                                                class="w-full object-cover h-full" />
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="w-full lg:col-span-3">
                                <div>
                                    <h1 class="text-2xl lg:text-3xl font-bold text-neutral-900 mb-4">
                                        {{ $book->title }}
                                    </h1>

                                    @if ($book->category)
                                        <div class="mb-4">
                                            <span
                                                class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-primary-100 text-primary-800">
                                                {{ $book->category->name }}
                                            </span>
                                        </div>
                                    @endif

                                    <div class="space-y-4 mb-6">
                                        @if ($book->synopsis)
                                            <h3 class="text-lg font-bold text-neutral-900">Sinopsis</h3>
                                            <div class="prose prose-gray max-w-none">
                                                <p class="text-neutral-600 leading-relaxed">
                                                    {{ $book->synopsis }}
                                                </p>
                                            </div>
                                        @endif

                                    </div>

                                    <hr class="my-6 border-neutral-200" />

                                    <div class="mt-6 flex flex-row gap-4 w-full">
                                        @livewire('bookmark-button', ['bookId' => $book->id])

                                        @if ($this->pendingTransaction)
                                            <button type="button" wire:click="cancel_borrow" wire:loading.attr="disabled"
                                                class="btn flex-1 rounded-lg bg-red-600 text-white
                                   hover:bg-red-700 disabled:bg-neutral-400 disabled:cursor-not-allowed
                                   dark:bg-red-500 dark:hover:bg-red-600">
                                                <!-- Normal state -->
                                                <span wire:loading.remove wire:target="cancel_borrow"
                                                    class="flex items-center justify-center gap-2">
                                                    <i class="iconoir-xmark text-xl"></i>
                                                    <span>Batalkan Peminjaman</span>
                                                </span>

                                                <!-- Loading state -->
                                                <span wire:loading wire:target="cancel_borrow"
                                                    class="flex items-center justify-center gap-2">
                                                    <svg class="h-5 w-5 animate-spin text-white"
                                                        xmlns="http://www.w3.org/2000/svg" fill="none"
                                                        viewBox="0 0 24 24" aria-hidden="true">
                                                        <circle class="opacity-25" cx="12" cy="12" r="10"
                                                            stroke="currentColor" stroke-width="4" />
                                                        <path class="opacity-75" fill="currentColor"
                                                            d="M4 12a8 8 0 018-8V0
                                                                                                   C5.373 0 0 5.373 0 12h4
                                                                                                   zm2 5.291A7.962 7.962 0 014 12H0
                                                                                                   c0 3.042 1.135 5.824 3 7.938l3-2.647z" />
                                                    </svg>
                                                </span>
                                            </button>
                                        @else
                                            <button type="button" wire:click="borrow_transaction"
                                                wire:loading.attr="disabled"
                                                class="btn flex-1 rounded-lg bg-primary-600 text-white
                                   hover:bg-primary-700 disabled:bg-neutral-400 disabled:cursor-not-allowed
                                   dark:bg-primary-500 dark:hover:bg-primary-600">
                                                <!-- Normal state -->
                                                <span wire:loading.remove wire:target="borrow_transaction"
                                                    class="flex items-center justify-center gap-2">
                                                    <i class="iconoir-open-book text-xl"></i>
                                                    <span>Pinjam Buku</span>
                                                </span>

                                                <!-- Loading state -->
                                                <span wire:loading wire:target="borrow_transaction"
                                                    class="flex items-center justify-center gap-2">
                                                    <svg class="h-5 w-5 animate-spin text-white"
                                                        xmlns="http://www.w3.org/2000/svg" fill="none"
                                                        viewBox="0 0 24 24" aria-hidden="true">
                                                        <circle class="opacity-25" cx="12" cy="12" r="10"
                                                            stroke="currentColor" stroke-width="4" />
                                                        <path class="opacity-75" fill="currentColor"
                                                            d="M4 12a8 8 0 018-8V0
                                                                                                   C5.373 0 0 5.373 0 12h4
                                                                                                   zm2 5.291A7.962 7.962 0 014 12H0
                                                                                                   c0 3.042 1.135 5.824 3 7.938l3-2.647z" />
                                                    </svg>
                                                </span>
                                            </button>
                                        @endif

                                    </div>

                                </div>
                            </div>
                        </div>
                    </div>

                    <div
                        class="w-full max-w-7xl mx-auto mt-12 border px-6 py-12 bg-white rounded-lg shadow-lg border-neutral-200">
                        <div class="xl:max-w-screen-xl max-w-screen-lg mx-auto">
                            <div class="grid grid-cols-1 gap-8">

                                <!-- Additional Information -->
                                <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                                    <div class="space-y-6">
                                        <h3 class="text-lg font-bold text-neutral-900">
                                            Informasi Buku
                                        </h3>

                                        <div class="space-y-4">
                                            <div class="flex items-start">
                                                <i
                                                    class="iconoir-user-badge-check
 w-5 h-5 text-neutral-400 text-xl mr-3 mt-0.5"></i>
                                                <div>
                                                    <p class="text-neutral-900 text-sm font-semibold">
                                                        Penulis
                                                    </p>
                                                    <p class="text-sm text-neutral-600">
                                                        {{ $book->author }}
                                                    </p>
                                                </div>
                                            </div>
                                            <div class="flex items-start">
                                                <i
                                                    class="iconoir-book-lock
 w-5 h-5 text-neutral-400 text-xl mr-3 mt-0.5"></i>
                                                <div>
                                                    <p class="text-neutral-900 text-sm font-semibold">
                                                        Penerbit
                                                    </p>
                                                    <p class="text-sm text-neutral-600">
                                                        {{ $book->publisher }}
                                                    </p>
                                                </div>
                                            </div>
                                            <div class="flex items-start">
                                                <i
                                                    class="iconoir-calendar w-5 h-5 text-neutral-400 text-xl mr-3 mt-0.5"></i>
                                                <div>
                                                    <p class="text-neutral-900 text-sm font-semibold">
                                                        Tahun Terbit
                                                    </p>
                                                    <p class="text-sm text-neutral-600">
                                                        {{ $book->year_published }}
                                                    </p>
                                                </div>
                                            </div>
                                            <div class="flex items-start">
                                                <i
                                                    class="iconoir-numbered-list-left
 w-5 h-5 text-neutral-400 text-xl mr-3 mt-0.5"></i>
                                                <div>
                                                    <p class="text-neutral-900 text-sm font-semibold">
                                                        ISBN
                                                    </p>
                                                    <p class="text-sm text-neutral-600">
                                                        {{ $book->isbn }}
                                                    </p>
                                                </div>
                                            </div>
                                            <div class="flex items-start">
                                                <i
                                                    class="iconoir-number-3-square
 w-5 h-5 text-neutral-400 text-xl mr-3 mt-0.5"></i>
                                                <div>
                                                    <p class="text-neutral-900 text-sm font-semibold">
                                                        Persediaan Buku
                                                    </p>
                                                    <p class="text-sm text-neutral-600">
                                                        {{ $book->book_count }}
                                                    </p>
                                                </div>
                                            </div>
                                            <div class="flex items-start">
                                                <i class="iconoir-loft-3d w-5 h-5 text-neutral-400 text-xl mr-3 mt-0.5"></i>
                                                <div>
                                                    <p class="text-neutral-900 text-sm font-semibold">
                                                        Lokasi Rak
                                                    </p>
                                                    <p class="text-sm text-neutral-600">
                                                        {{ $book->bookshelf }}
                                                    </p>
                                                </div>
                                            </div>
                                            <div class="flex items-start">
                                                <i class="iconoir-book w-5 h-5 text-neutral-400 text-xl mr-3 mt-0.5"></i>
                                                <div>
                                                    <p class="text-neutral-900 text-sm font-semibold">
                                                        Jenis Buku
                                                    </p>
                                                    <p class="text-sm text-neutral-600">
                                                        {{ $book->type }}
                                                    </p>
                                                </div>
                                            </div>

                                        </div>
                                    </div>

                                    <div class="space-y-6">
                                        <h3 class="text-lg font-bold text-neutral-900">Informasi Peminjaman
                                        </h3>

                                        <div class="space-y-4">
                                            <div class="flex items-start">
                                                <i class="iconoir-timer w-5 h-5 text-neutral-400 text-xl mr-3 mt-0.5"></i>
                                                <div>
                                                    <p class="text-neutral-900 text-sm font-semibold">Durasi
                                                        Peminjaman</p>
                                                    <p class="text-sm text-neutral-600">Maksimal
                                                        {{ $setting->limit_day ?? 7 }} hari
                                                    </p>
                                                </div>
                                            </div>

                                            <div class="flex items-start">
                                                <i
                                                    class="iconoir-refresh w-5 h-5 text-neutral-400 text-xl mr-3 mt-0.5"></i>
                                                <div>
                                                    <p class="text-neutral-900 text-sm font-semibold">
                                                        Perpanjangan</p>
                                                    <p class="text-sm text-neutral-600">Dapat diperpanjang
                                                        dengan syarat dan ketentuan perpustakaan</p>
                                                </div>
                                            </div>
                                            @include('pages.catalog-book.rules')
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <section class="w-full items-center max-w-6xl mx-auto px-4 py-20">
                    <!-- Card buku populer -->
                    <div data-aos="fade-up" class="mx-auto lg:max-w-7xl md:max-w-4xl sm:max-w-xl max-sm:max-w-sm">
                        <div class="flex items-center justify-between w-full mb-8">
                            <h1 class="text-2xl font-semibold text-neutral-900">
                                Buku Populer
                            </h1>

                            <a href="{{ route('catalog') }}"
                                class="text-lg font-semibold text-primary-600 hover:text-primary-700 underline underline-offset-4 transition-colors duration-200">
                                Lainnya
                            </a>

                        </div>
                        @include('pages.catalog-book.book_recommendations')

                    </div>

                </section>
            </div>
        @endvolt
    </div>
</x-guest-layout>
