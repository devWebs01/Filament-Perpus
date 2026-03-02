<?php

use function Laravel\Folio\name;
use function Livewire\Volt\{state, with, usesPagination};
use App\Models\{Transaction, Book, Setting, Status};
use App\Services\BorrowBookService;
use Jantinnerezo\LivewireAlert\Facades\LivewireAlert;

name('my-books');
usesPagination();

state([
    'user' => auth()->user(),
    'setting' => fn() => Setting::first() ?? new Setting(['limit_day' => config('app.library.default_loan_days', 7)]),
    'search' => '',
    'filter' => 'all',
    'activeTab' => 'my_books',
]);

// Validate search input to prevent abuse
$updatedSearch = function ($value) {
    $maxLength = config('app.library.max_search_length', 100);
    if (strlen($value) > $maxLength) {
        $this->search = substr($value, 0, $maxLength);
    }
};

with([
    'transactions' => function () {
        $query = Transaction::where('user_id', auth()->id())->with(['book', 'status']);

        if ($this->filter !== 'all') {
            if ($this->filter === 'borrowed') {
                $query->whereHas('status', function ($q) {
                    $q->where('name', 'Dipinjam');
                });
            } elseif ($this->filter === 'returned') {
                $query->whereHas('status', function ($q) {
                    $q->where('name', 'Dikembalikan');
                });
            }
        }

        if ($this->search) {
            $query->whereHas('book', function ($q) {
                $q->where('title', 'like', '%' . $this->search . '%')->orWhere('author', 'like', '%' . $this->search . '%');
            });
        }

        return $query->latest()->paginate(10);
    },
    'stats' => function () {
        // Optimized: Database-level aggregation with caching
        $cacheKey = 'user_stats_' . auth()->id();

        return cache()->remember($cacheKey, now()->addMinutes(5), function () {
            $today = now()->startOfDay();

            // Get status IDs once to avoid multiple queries
            $dipinjamStatusId = Status::where('name', 'Dipinjam')->value('id');
            $dikembalikanStatusId = Status::where('name', 'Dikembalikan')->value('id');

            // Single database query with aggregation using date comparison
            $result = Transaction::where('user_id', auth()->id())
                ->leftJoin('statuses', 'transactions.status_id', '=', 'statuses.id')
                ->selectRaw(
                    '
                    COUNT(CASE WHEN transactions.status_id = ? AND transactions.due_date >= ? THEN 1 END) as active,
                    COUNT(CASE WHEN transactions.status_id = ? THEN 1 END) as returned,
                    COUNT(CASE WHEN transactions.status_id = ? AND transactions.due_date < ? THEN 1 END) as overdue
                ',
                    [$dipinjamStatusId, $today, $dikembalikanStatusId, $dipinjamStatusId, $today],
                )
                ->first();

            return [
                'active' => (int) ($result->active ?? 0),
                'returned' => (int) ($result->returned ?? 0),
                'overdue' => (int) ($result->overdue ?? 0),
            ];
        });
    },
]);

$refreshData = function () {
    $this->resetPage();
    // Clear stats cache when data is refreshed
    cache()->forget('user_stats_' . auth()->id());
};

$extendLoan = function ($transactionId) {
    $transaction = Transaction::find($transactionId);

    if ($transaction && $transaction->user_id === auth()->id() && $transaction->isBorrowed()) {
        // Add configured days to due date
        $extensionDays = config('app.library.loan_extension_days', 7);
        $transaction->due_date = $transaction->due_date->addDays($extensionDays);
        $transaction->save();

        // Clear stats cache after extending loan
        cache()->forget('user_stats_' . auth()->id());

        session()->flash('message', "Masa peminjaman berhasil diperpanjang {$extensionDays} hari!");
    }
};

$cancel_borrow = function ($transactionId) {
    $alertDuration = config('app.library.alert_duration', 3000);

    try {
        // Ambil transaksi
        $transaction = Transaction::find($transactionId);

        if (!$transaction) {
            LivewireAlert::title('Gagal')->position('center')->timer($alertDuration)->text('Transaksi tidak ditemukan')->error()->show();

            return;
        }

        // Inisialisasi service
        $borrowBookService = app(BorrowBookService::class);

        // Proses pembatalan peminjaman
        $result = $borrowBookService->cancelBorrow($transaction);

        if ($result['success']) {
            LivewireAlert::title('Berhasil')->position('center')->timer($alertDuration)->text($result['message'])->success()->show();
        } else {
            LivewireAlert::title('Gagal')->position('center')->timer($alertDuration)->text($result['message'])->error()->show();
        }

        // Refresh data dan cache
        $this->refreshData();
    } catch (\Throwable $th) {
        report($th);

        LivewireAlert::title('Gagal')->position('center')->timer($alertDuration)->text('Terjadi kesalahan sistem. Silakan coba lagi nanti')->error()->show();
    }
};

?>

<x-guest-layout>
    <x-slot name="title">Buku Saya</x-slot>

    @volt
        <div>
            <div x-data="{
                activeTab: 'my_books',
                switchTab(tab) {
                    this.activeTab = tab;
                }
            }">

                <section class="max-w-7xl mx-auto px-4 py-8">

                    <div class="w-full mt-6">
                        <ul class="flex">
                            <!-- TAB 1 -->
                            <li @click="switchTab('my_books')"
                                :class="activeTab === 'my_books' ? 'bg-gray-400 text-white font-bold' : 'font-medium'"
                                class="relative tab flex-1 btn mx-4 rounded-lg cursor-pointer flex items-center gap-2 text-gray-600 dark:text-gray-100">

                                <i class="iconoir-view-structure-down text-2xl"></i>
                                <span class="hidden lg:block">Buku Saya</span>
                            </li>

                            <!-- TAB 2 (punya indikator jika profile incomplete) -->
                            <li @click="switchTab('my_bookmarks')"
                                :class="activeTab === 'my_bookmarks' ? 'bg-gray-400 text-white font-bold' : 'font-medium'"
                                class="relative tab flex-1 btn mx-4 rounded-lg cursor-pointer flex items-center gap-2 text-gray-600 dark:text-gray-100">

                                <i class="iconoir-user-badge-check text-2xl"></i>
                                <span class="hidden lg:block">Buku Disimpan</span>

                            </li>

                        </ul>

                        {{-- TAB 1: Static content, TANPA Livewire --}}
                        <div x-show="activeTab === 'my_books'" x-transition:opacity class="p-6">
                            <!-- Header -->
                            <div data-aos="fade-up" class="mb-12">
                                <h1 class="text-4xl font-bold text-gray-900 dark:text-gray-100 mb-3">Buku Saya</h1>
                                <p class="text-lg text-gray-600 dark:text-gray-400">Kelola dan pantau buku-buku yang Anda
                                    pinjam</p>
                            </div>

                            {{-- Statistics Cards with Bladewind --}}
                            @include('pages.my-books.statistics_cards')

                            <!-- Search and Filter -->
                            <div data-aos="fade-up" class="mb-8">
                                <div class="flex flex-col md:flex-row gap-4">
                                    <!-- Search Input -->
                                    <div class="flex-1">
                                        <x-input wire:model.live="search" placeholder="Cari judul atau penulis buku..."
                                            icon="o-magnifying-glass" class="w-full" maxlength="100" />
                                    </div>

                                    <!-- Status Filter -->
                                    <div class="w-full md:w-64">
                                        <select wire:model.live="filter"
                                            class="w-full border border-gray-300 dark:border-gray-600 rounded-lg px-4 py-2.5 bg-white dark:bg-gray-800 text-gray-900 dark:text-gray-100 focus:ring-2 focus:ring-primary-500 focus:border-primary-500 transition">
                                            <option value="all">Semua Status</option>
                                            <option value="borrowed">Dipinjam</option>
                                            <option value="returned">Dikembalikan</option>
                                        </select>
                                    </div>
                                </div>
                            </div>

                            <!-- Books List -->
                            <div data-aos="fade-up">

                                @if ($transactions->count() > 0)
                                    <div class="space-y-6">

                                        @foreach ($transactions as $transaction)
                                            {{-- MAIN CARD --}}
                                            <x-card
                                                class="rounded-lg bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 hover:shadow-xl transition duration-300">

                                                <div class="flex flex-col md:flex-row gap-6">

                                                    {{-- Book Cover --}}
                                                    <div class="flex-shrink-0">
                                                        <img src="{{ Storage::url($transaction->book->image) }}"
                                                            alt="{{ $transaction->book->title }}"
                                                            class="w-36 h-48 object-cover rounded-md shadow-md">
                                                    </div>

                                                    {{-- Book Info --}}
                                                    <div class="flex-1 flex flex-col gap-6">

                                                        {{-- Title & Author --}}
                                                        <div>
                                                            <h3
                                                                class="text-2xl font-bold text-gray-900 dark:text-gray-100 leading-tight">
                                                                {{ Str::limit($transaction->book->title, 60) }}
                                                            </h3>
                                                            <p
                                                                class="text-base text-gray-600 dark:text-gray-400 font-medium">
                                                                {{ Str::limit($transaction->book->author, 60, '...') }}
                                                            </p>
                                                        </div>

                                                        {{-- DETAIL GRID --}}
                                                        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">

                                                            {{-- Tanggal Pinjam --}}
                                                            <x-card compact="true"
                                                                class="bg-gray-50 dark:bg-gray-700 border border-gray-200 dark:border-gray-600">
                                                                <div class="flex items-center mb-2">
                                                                    <i class="iconoir-calendar text-gray-500 mr-2"></i>
                                                                    <span
                                                                        class="text-xs uppercase text-gray-500 dark:text-gray-400">
                                                                        Tanggal Pinjam
                                                                    </span>
                                                                </div>
                                                                <p
                                                                    class="text-base font-semibold text-gray-900 dark:text-gray-100">
                                                                    {{ $transaction->created_at->format('d M Y') }}
                                                                </p>
                                                            </x-card>

                                                            {{-- Batas Kembali --}}
                                                            <x-card compact="true"
                                                                class="bg-gray-50 dark:bg-gray-700 border border-gray-200 dark:border-gray-600">
                                                                <div class="flex items-center mb-2">
                                                                    <i class="iconoir-clock text-gray-500 mr-2"></i>
                                                                    <span
                                                                        class="text-xs uppercase text-gray-500 dark:text-gray-400">
                                                                        Batas Kembali
                                                                    </span>
                                                                </div>
                                                                <p
                                                                    class="text-base font-semibold
                                                                                                        {{ $transaction->due_date < now() ? 'text-red-600' : 'text-gray-900 dark:text-gray-100' }}">
                                                                    {{ $transaction->due_date->format('d M Y') }}
                                                                </p>
                                                            </x-card>

                                                            {{-- Status --}}
                                                            <x-card compact="true"
                                                                class="bg-gray-50 dark:bg-gray-700 border border-gray-200 dark:border-gray-600">
                                                                <div class="flex items-center mb-2">
                                                                    <i class="iconoir-info-circle text-gray-500 mr-2"></i>
                                                                    <span
                                                                        class="text-xs uppercase text-gray-500 dark:text-gray-400">
                                                                        Status
                                                                    </span>
                                                                </div>

                                                                <p
                                                                    class="text-base font-semibold text-gray-900 dark:text-gray-100">
                                                                    @if ($transaction->isOverdue())
                                                                        <span class="text-red-500">Terlambat</span>
                                                                    @elseif($transaction->isBorrowed())
                                                                        <span
                                                                            class="text-gray-900 dark:text-gray-100">Dipinjam</span>
                                                                    @elseif($transaction->isReturned())
                                                                        <span
                                                                            class="text-green-600 dark:text-green-300">Dikembalikan</span>
                                                                    @else
                                                                        {{ $transaction->status?->name ?? 'Unknown' }}
                                                                    @endif
                                                                </p>
                                                            </x-card>

                                                        </div>

                                                        {{-- ACTION BUTTONS - NOW BELOW --}}
                                                        <div class="flex flex-col gap-3 mt-2">
                                                            @if ($transaction->isBorrowed())
                                                                @if ($transaction->due_date > now())
                                                                    <x-button icon="o-arrow-right" class="w-full"
                                                                        onclick="Livewire.dispatch('extendLoan', { id: {{ $transaction->id }} })">
                                                                        Perpanjang 7 Hari
                                                                    </x-button>
                                                                @endif
                                                            @elseif ($transaction->status->id === 1)
                                                                <button type="button"
                                                                    wire:click="cancel_borrow({{ $transaction->id }})"
                                                                    wire:loading.attr="disabled"
                                                                    class="btn flex-1 rounded-lg bg-red-600 text-white
                                   hover:bg-red-700 disabled:bg-neutral-400 disabled:cursor-not-allowed
                                   dark:bg-red-500 dark:hover:bg-red-600 py-2">
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
                                                                            xmlns="http://www.w3.org/2000/svg"
                                                                            fill="none" viewBox="0 0 24 24"
                                                                            aria-hidden="true">
                                                                            <circle class="opacity-25" cx="12"
                                                                                cy="12" r="10" stroke="currentColor"
                                                                                stroke-width="4" />
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

                                            </x-card>
                                        @endforeach
                                    </div>

                                    {{-- Pagination --}}
                                    <div class="mt-12 justify-between items-center">
                                        {{ $transactions->links() }}
                                    </div>
                                @else
                                    {{-- EMPTY STATE --}}
                                    <x-card class="p-16 text-center">

                                        <div class="flex justify-center mb-6">
                                            <div class="relative">
                                                <div
                                                    class="w-32 h-32 bg-gray-100 dark:bg-gray-700 rounded-full flex items-center justify-center">
                                                    <i
                                                        class="iconoir-open-book text-gray-400 dark:text-gray-500 text-5xl"></i>
                                                </div>
                                                <div
                                                    class="absolute -bottom-2 -right-2 w-10 h-10 bg-gray-200 dark:bg-gray-600 rounded-full flex items-center justify-center">
                                                    <i class="iconoir-search text-gray-500 dark:text-gray-400 text-lg"></i>
                                                </div>
                                            </div>
                                        </div>

                                        <h3 class="text-2xl font-bold text-gray-900 dark:text-gray-100 mb-3">Belum ada buku
                                            yang
                                            dipinjam</h3>

                                        <p
                                            class="text-lg text-gray-600 dark:text-gray-400 mb-8 max-w-md mx-auto leading-relaxed">
                                            Mulai eksplorasi katalog kami dan temukan buku-buku menarik untuk dipinjam
                                        </p>

                                        <x-button icon="o-book-open" href="{{ route('catalog') }}">
                                            Jelajahi Katalog
                                        </x-button>

                                    </x-card>
                                @endif

                            </div>

                        </div>

                        {{-- TAB 2: Livewire, hanya dirender saat tab aktif --}}
                        <div x-show="activeTab === 'my_bookmarks'" x-transition:opacity>
                            @include('pages.my-books.bookmarks')
                        </div>

                    </div>

                </section>
            </div>
        </div>
    @endvolt
</x-guest-layout>
