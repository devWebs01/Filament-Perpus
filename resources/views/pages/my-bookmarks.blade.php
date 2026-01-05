<?php

use function Laravel\Folio\name;
use function Livewire\Volt\{state, with, computed};
use App\Services\BookmarkService;

name('my-bookmarks');

state(['search' => '']);

// Validate search input to prevent abuse
$updatedSearch = function ($value) {
    $maxLength = config('app.library.max_search_length', 100);
    if (strlen($value) > $maxLength) {
        $this->search = substr($value, 0, $maxLength);
    }
};

$bookmarks = computed(function () {
    if (!auth()->check()) {
        return collect();
    }

    $bookmarkService = app(BookmarkService::class);
    return $bookmarkService->getUserBookmarksSearch(auth()->user(), $this->search);
});


?>

<x-guest-layout>
    <x-slot name="title">Buku Tersimpan</x-slot>

    @volt
        <div>
            <section class="max-w-7xl mx-auto px-4 py-8">
                <!-- Header -->
                <div data-aos="fade-up" class="mb-12">
                    <h1 class="text-4xl font-bold text-gray-900 dark:text-gray-100 mb-3">Buku Tersimpan</h1>
                    <p class="text-lg text-gray-600 dark:text-gray-400">Koleksi buku yang telah Anda simpan untuk dibaca
                        nanti</p>
                </div>

                <!-- Search -->
                <div data-aos="fade-up"
                    class="w-full bg-white dark:bg-gray-900 shadow-md p-6 mb-8 rounded-xl border border-gray-200 dark:border-gray-700">
                    <div class="w-full">
                        <x-input name="search" wire:model.live="search" placeholder="Cari judul atau penulis..."
                            label="Pencarian" icon="o-magnifying-glass" class="w-full" size="medium" maxlength="100" />
                    </div>
                </div>

                <!-- Bookmarks Grid -->
                <div data-aos="fade-up">
                    @if ($this->bookmarks->count() > 0)
                        <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4 xl:grid-cols-5 gap-6">
                            @foreach ($this->bookmarks as $bookmark)
                                <div
                                    class="bg-white dark:bg-gray-800 shadow-sm border border-gray-200 dark:border-gray-700 rounded-lg p-3 flex flex-col h-full transition-colors duration-300">
                                    <!-- Bagian isi -->
                                    <a href="javascript:void(0)" class="block flex-1">
                                        <div class="aspect-[12/11] bg-gray-100 dark:bg-gray-700 rounded-lg p-4">
                                            <img src="{{ Storage::url($bookmark->book->image) }}"
                                                alt="{{ $bookmark->book->title }}" class="w-full h-full object-contain" />
                                        </div>

                                        <h5 class="text-base font-semibold text-slate-900 dark:text-gray-100 mt-3">
                                            {{ Str::limit($bookmark->book->title, 25, '...') }}
                                        </h5>
                                    </a>

                                    <!-- Spacer otomatis dorong tombol ke bawah -->
                                    <div class="flex-1"></div>

                                    <!-- Tombol Bookmark & Detail -->
                                    <div class="mt-6 flex gap-2">
                                        <div class="flex-shrink-0">
                                            <livewire:bookmark-button :bookId="$bookmark->book->id" :compact="true" :key="'bookmark-' . $bookmark->book->id" />
                                        </div>
                                        <a href="{{ route('book-detail', ['book' => $bookmark->book->id]) }}" type="button"
                                            class="btn flex-1 justify-between bg-primary-600 hover:bg-primary-700 dark:bg-primary-500 dark:hover:bg-primary-600 text-white">
                                            <strong>Detail</strong>
                                            <i class="iconoir-arrow-right text-white dark:text-gray-100 text-xl"></i>
                                        </a>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @else
                        <!-- Empty State -->
                        <x-card class="p-16 text-center">
                            <div class="flex justify-center mb-6">
                                <div class="relative">
                                    <div
                                        class="w-32 h-32 bg-gray-100 dark:bg-gray-700 rounded-full flex items-center justify-center">
                                        <i class="iconoir-bookmark-book text-gray-400 dark:text-gray-500 text-5xl"></i>
                                    </div>
                                    <div
                                        class="absolute -bottom-2 -right-2 w-10 h-10 bg-gray-200 dark:bg-gray-600 rounded-full flex items-center justify-center">
                                        <i class="iconoir-search text-gray-500 dark:text-gray-400 text-lg"></i>
                                    </div>
                                </div>
                            </div>

                            <h3 class="text-2xl font-bold text-gray-900 dark:text-gray-100 mb-3">Belum ada buku tersimpan
                            </h3>

                            <p class="text-lg text-gray-600 dark:text-gray-400 mb-8 max-w-md mx-auto leading-relaxed">
                                Mulai simpan buku favorit Anda untuk akses mudah di kemudian hari
                            </p>

                            <a>

                                <x-button icon="o-book-open" link="{{ route('catalog') }}">
                                    Jelajahi Katalog
                                </x-button>
                            </a>
                        </x-card>
                    @endif
                </div>
            </section>
        </div>
    @endvolt
</x-guest-layout>
