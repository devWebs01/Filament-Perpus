<?php

use function Laravel\Folio\name;
use function Livewire\Volt\{state, computed};
use App\Models\{Book, Category};

name('catalog');

state(['search' => ''])->url();
state(['categories' => fn() => Category::get()]);
state(['category_id' => '']);

// Validate search input to prevent abuse
$updatedSearch = function ($value) {
    $maxLength = config('app.library.max_search_length', 100);
    if (strlen($value) > $maxLength) {
        $this->search = substr($value, 0, $maxLength);
    }
};

$books_catalog = computed(function () {
    // Dapatkan semua buku jika tidak ada search dan category
    if (!$this->search && !$this->category_id) {
        return Book::latest()->get();
    }

    // Dapatkan buku berdasarkan search
    elseif ($this->search && !$this->category_id) {
        return Book::where('title', 'like', '%' . $this->search . '%')
            ->latest()
            ->get();
    }

    // Dapatkan buku berdasarkan category
    elseif (!$this->search && $this->category_id) {
        return Book::where('category_id', $this->category_id)->latest()->get();
    }

    // Dapatkan buku berdasarkan search dan category
    else {
        return Book::where('title', 'like', '%' . $this->search . '%')
            ->where('category_id', $this->category_id)
            ->latest()
            ->get();
    }
});

?>

<x-guest-layout>
    <x-slot name="title">Katalog Buku</x-slot>

    @volt
    <div>
        <!-- Page Header Section with Modern Color Palette -->
        <section class="pt-20 pb-8">
            <div class="w-full max-w-7xl mx-auto px-4 sm:px-6">
                <!-- Page Title -->
                <div class="mb-8">
                    <h1 class="text-3xl font-bold text-neutral-900 sm:text-4xl">
                        Katalog Buku
                    </h1>
                    <p class="mt-3 max-w-2xl text-neutral-600 text-base leading-relaxed">
                        Jelajahi koleksi buku yang tersedia di perpustakaan kami.
                        Temukan berbagai judul menarik dari beragam kategori untuk
                        menambah wawasan dan pengetahuan Anda.
                    </p>
                </div>

                <!-- Search and Filter Bar -->
                <div class="flex flex-col md:flex-row items-center gap-4 mb-8 justify-between">
                    {{-- Search Input with Modern Styling --}}
                    <div class="w-full md:w-1/2">
                        <div class="relative group">
                            <i class="iconoir-search absolute left-4 top-1/2 -translate-y-1/2
                                               text-neutral-400 text-xl
                                               group-focus-within:text-primary-500
                                               transition-colors duration-200"></i>

                            <input type="text" wire:model.live="search" placeholder="Cari judul buku..." maxlength="100" class="w-full pl-12 pr-4 py-3 rounded-lg
                                           border border-neutral-300
                                           bg-white text-neutral-700
                                           placeholder-neutral-400
                                           focus:border-primary-500
                                           focus:ring-2 focus:ring-primary-500/20
                                           focus:outline-none
                                           hover:border-neutral-400
                                           transition-all duration-200
                                           shadow-sm hover:shadow-md" />
                        </div>
                    </div>

                    {{-- Category Dropdown with Modern Styling --}}
                    <div class="w-full md:w-1/3">
                        <div class="relative">
                            <select wire:model.live="category_id" class="w-full appearance-none rounded-lg
                                           border border-neutral-300
                                           bg-white text-neutral-700
                                           px-4 py-3 pr-10
                                           focus:border-primary-500
                                           focus:ring-2 focus:ring-primary-500/20
                                           focus:outline-none
                                           hover:border-neutral-400
                                           transition-all duration-200
                                           shadow-sm hover:shadow-md
                                           cursor-pointer">
                                <option value="">Semua Kategori</option>
                                @foreach ($categories as $category)
                                    <option value="{{ $category->id }}">
                                        {{ $category->name }}
                                    </option>
                                @endforeach
                            </select>

                            <!-- Custom Dropdown Icon -->
                            <i class="iconoir-nav-arrow-down absolute right-4 top-1/2
                                               -translate-y-1/2 text-neutral-400
                                               pointer-events-none"></i>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <!-- Books Grid Section -->
        <section class="pb-20">
            <div class="w-full max-w-7xl mx-auto px-4 sm:px-6">
                {{-- Loading State --}}
                <div wire:loading.flex class="justify-center py-20">
                    <div class="flex flex-col items-center gap-4">
                        <div class="text-neutral-600 text-base font-medium">
                            Memuat data buku...
                        </div>
                        <span class="loading loading-dots loading-md text-primary-600"></span>
                    </div>
                </div>

                {{-- Books Grid --}}
                <div wire:loading.remove>
                    @if ($this->books_catalog->count() === 0)
                        <!-- Empty State -->
                        <div class="text-center py-20 px-4">
                            <div class="inline-flex items-center justify-center
                                                                    w-20 h-20 rounded-full
                                                                    bg-neutral-100 mb-6">
                                <i class="iconoir-book-opened-text
                                                                       text-4xl text-neutral-400"></i>
                            </div>
                            <h3 class="text-xl font-semibold text-neutral-900 mb-2">
                                Tidak ada buku ditemukan
                            </h3>
                            <p class="text-neutral-600 max-w-md mx-auto">
                                Coba gunakan kata kunci lain atau pilih kategori berbeda.
                            </p>
                        </div>
                    @else
                        <!-- Books Grid with Modern Card Styling -->
                        <div class="grid grid-cols-2 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-3 sm:gap-4 md:gap-5">
                            @foreach ($this->books_catalog as $book)
                                <div class="group bg-white rounded-lg border border-neutral-200 shadow-sm hover:shadow-lg p-3 flex flex-col transition-all duration-300">
                                    <!-- CONTENT -->
                                    <a href="{{ route('book-detail', ['book' => $book->id]) }}" class="block flex-grow min-w-0">
                                        <div class="aspect-[12/11] bg-neutral-100 rounded-lg p-2 sm:p-3 overflow-hidden">
                                            <img src="{{ Storage::url($book->image) }}" alt="{{ $book->title }}" class="w-full h-full object-contain" />
                                        </div>

                                        <h5 class="text-sm sm:text-base font-semibold mt-2 line-clamp-2">
                                            {{ Str::limit($book->title, 25, '...') }}
                                        </h5>
                                    </a>

                                    <!-- FOOTER -->
                                    <div class="mt-3 sm:mt-4 flex gap-2 items-stretch">
                                        <!-- Bookmark Button -->
                                        <div class="flex-shrink-0 flex items-center">
                                            @livewire('bookmark-button', ['bookId' => $book->id, 'compact' => true])
                                        </div>

                                        <!-- Detail Button -->
                                        <a href="{{ route('book-detail', ['book' => $book->id]) }}"
                                           class="flex-1 inline-flex items-center justify-center gap-2 rounded-lg bg-primary-600 hover:bg-primary-700 text-white text-xs sm:text-sm font-semibold h-9 sm:h-10 px-3 transition-colors">
                                            Detail
                                            <i class="iconoir-arrow-right text-base sm:text-lg"></i>
                                        </a>
                                    </div>
                                </div>
                            @endforeach
                        </div>

                        <!-- Results Summary -->
                        @if($this->books_catalog->count() > 0)
                            <div class="mt-8 text-center">
                                <p class="text-sm text-neutral-500">
                                    Menampilkan
                                    <span class="font-semibold text-neutral-700">
                                        {{ $this->books_catalog->count() }}
                                    </span>
                                    buku
                                    @if($this->search || $this->category_id)
                                        <span class="text-neutral-400">dari pencarian Anda</span>
                                    @endif
                                </p>
                            </div>
                        @endif
                    @endif
                </div>
            </div>
        </section>
    </div>
    @endvolt
</x-guest-layout>