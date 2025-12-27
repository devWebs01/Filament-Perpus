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
            <section class="pt-20">
                <div class="w-full max-w-7xl mx-auto">
                    <div class="mb-10">
                        <h2 class="text-xl font-bold text-gray-900 sm:text-3xl">Katalog Buku</h2>

                        <p class="mt-4 max-w-md text-gray-500">
                            Jelajahi koleksi buku yang tersedia di perpustakaan kami. Temukan berbagai judul menarik dari
                            beragam kategori untuk menambah wawasan dan pengetahuan Anda. </p>
                    </div>
                    {{-- Filter Form --}}
                    <div class="flex flex-col md:flex-row items-center justify-between gap-4 mb-8">

                        {{-- Search Input --}}
                        <div class="w-full md:w-1/2">
                            <div class="relative">
                                <i
                                    class="iconoir-search absolute left-3 top-1/2 -translate-y-1/2 text-gray-400 text-xl"></i>

                                <input type="text" wire:model.live="search" placeholder="Cari judul buku..."
                                    class="w-full pl-12 pr-4 py-2.5 rounded-lg border border-gray-300
                          bg-white text-gray-800
                          placeholder-gray-400
                          focus:border-primary-500 focus:ring-2 focus:ring-primary-500/30 focus:outline-none transition" />
                            </div>
                        </div>

                        {{-- Category Dropdown --}}
                        <div class="w-full md:w-1/3">
                            <select wire:model.live="category_id"
                                class="w-full rounded-lg border border-gray-300
                       bg-white text-gray-800 px-4 py-2.5
                       focus:border-primary-500 focus:ring-2 focus:ring-primary-500/30 focus:outline-none transition">
                                <option value="">Semua Kategori</option>

                                @foreach ($categories as $category)
                                    <option value="{{ $category->id }}">{{ $category->name }}</option>
                                @endforeach
                            </select>
                        </div>

                    </div>

                    {{-- Collage Book Section --}}
                    <div wire:loading.flex class="justify-center py-20">
                        <div class="text-gray-500">Memuat data buku!</div>
                        <span class="loading loading-dots loading-sm ms-3"></span>
                    </div>

                    <div wire:loading.remove>
                        @if ($this->books_catalog->count() === 0)
                            <div class="text-center py-10 text-gray-500">
                                Tidak ada buku ditemukan.
                            </div>
                        @else
                            <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-6 items-stretch">
                                @foreach ($this->books_catalog as $book)
                                    <div
                                        class="bg-white border border-gray-200 rounded-lg shadow-sm p-4 flex flex-col h-full transition-colors duration-300">
                                        <a href="javascript:void(0)" class="flex-1 block">
                                            <div class="aspect-[12/11] bg-gray-100 rounded-lg p-4">
                                                <img src="{{ Storage::url($book->image) }}" alt="{{ $book->title }}"
                                                    class="w-full h-full object-contain" />
                                            </div>
                                            <h5 class="text-base font-semibold text-gray-900 mt-3">
                                                {{ Str::limit($book->title, 25, '...') }}
                                            </h5>
                                        </a>

                                        <div class="flex-1"></div>

                                        <!-- Tombol Bookmark & Detail -->
                                        <div class="mt-6 flex gap-2">

                                            <a href="{{ route('book-detail', ['book' => $book->id]) }}" type="button"
                                                class="btn flex-1 justify-between bg-primary-600 hover:bg-primary-700 text-white">
                                                <strong>Detail</strong>
                                                <i class="iconoir-arrow-right text-white text-xl"></i>
                                            </a>
                                        </div>

                                    </div>
                                @endforeach
                            </div>
                        @endif
                    </div>
                </div>

            </section>
        </div>
    @endvolt
</x-guest-layout>
