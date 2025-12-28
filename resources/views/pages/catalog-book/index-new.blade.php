<?php

use function Laravel\Folio\name;
use function Livewire\Volt\{state, computed};
use App\Models\{Book, Category, Setting};

name('catalog');

state(['search'])->url();
state(['categories' => fn() => Category::get()]);
state(['category_id' => '']);

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
                    <div class="flex flex-col md:flex-row items-center gap-4 mb-8">
                        {{-- Search Input with Modern Styling --}}
                        <div class="w-full md:w-1/2">
                            <div class="relative group">
                                <i class="iconoir-search absolute left-4 top-1/2 -translate-y-1/2
                                               text-neutral-400 text-xl
                                               group-focus-within:text-primary-500
                                               transition-colors duration-200"></i>

                                <input
                                    type="text"
                                    wire:model.live="search"
                                    placeholder="Cari judul buku..."
                                    class="w-full pl-12 pr-4 py-3 rounded-xl
                                           border border-neutral-300
                                           bg-white text-neutral-700
                                           placeholder-neutral-400
                                           focus:border-primary-500
                                           focus:ring-2 focus:ring-primary-500/20
                                           focus:outline-none
                                           hover:border-neutral-400
                                           transition-all duration-200
                                           shadow-sm hover:shadow-md"
                                />
                            </div>
                        </div>

                        {{-- Category Dropdown with Modern Styling --}}
                        <div class="w-full md:w-1/3">
                            <div class="relative">
                                <select
                                    wire:model.live="category_id"
                                    class="w-full appearance-none rounded-xl
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
                            <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3
                                        lg:grid-cols-4 gap-6">
                                @foreach ($this->books_catalog as $book)
                                    <div class="group bg-white rounded-xl
                                                border border-neutral-200
                                                shadow-sm hover:shadow-lg
                                                hover:shadow-primary-sm/20
                                                p-4 flex flex-col h-full
                                                transition-all duration-300
                                                hover:-translate-y-1">

                                        <!-- Book Cover -->
                                        <a href="{{ route('book-detail', ['book' => $book->id]) }}"
                                           class="block flex-1">
                                            <div class="aspect-[12/11] bg-neutral-100
                                                          rounded-xl p-4
                                                          overflow-hidden
                                                          group-hover:bg-neutral-50
                                                          transition-colors duration-300">
                                                <img
                                                    src="{{ Storage::url($book->image) }}"
                                                    alt="{{ $book->title }}"
                                                    class="w-full h-full object-contain
                                                           group-hover:scale-105
                                                           transition-transform duration-300" />
                                            </div>

                                            <!-- Book Title -->
                                            <h5 class="text-base font-semibold text-neutral-900 mt-4
                                                        group-hover:text-primary-600
                                                        transition-colors duration-200
                                                        line-clamp-2">
                                                {{ Str::limit($book->title, 25, '...') }}
                                            </h5>
                                        </a>

                                        <!-- Spacer to push button to bottom -->
                                        <div class="flex-1"></div>

                                        <!-- Action Button -->
                                        <div class="mt-6">
                                            <a
                                                href="{{ route('book-detail', ['book' => $book->id]) }}"
                                                class="btn flex items-center justify-between gap-2
                                                       w-full bg-primary-600
                                                       hover:bg-primary-700
                                                       text-white
                                                       rounded-xl
                                                       px-4 py-2.5
                                                       font-medium text-sm
                                                       shadow-sm hover:shadow-primary-md
                                                       transition-all duration-300
                                                       group-hover:shadow-primary-md">
                                                <span>Detail</span>
                                                <i class="iconoir-arrow-right text-lg
                                                             group-hover:translate-x-1
                                                             transition-transform duration-200"></i>
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
