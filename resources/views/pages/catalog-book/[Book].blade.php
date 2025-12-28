<?php

use function Laravel\Folio\name;
use function Livewire\Volt\{state};
use App\Models\{Book, Setting, Status};

name('book-detail');

state([
    'book' => fn($book) => $book,
    'setting' => fn() => Setting::select('limit_day')->first() ?? (new Setting(['limit_day' => config('app.library.default_loan_days', 7)])),
    'rules' => fn() => Status::where('amount', '>', 0)->get(),
]);

?>

<x-guest-layout>
    @volt
    <div>
        <x-slot name="title">{{ $book->title }} - Detail Buku</x-slot>
        @livewire('modal-confirm-borrow', ['book' => $book])
        <div class="pt-20">
            <div class="w-full max-w-7xl mx-auto px-4 sm:px-6">
                <div class="grid items-start grid-cols-1 lg:grid-cols-5 gap-8 max-lg:gap-12 max-sm:gap-8">
                    <div class="w-full lg:sticky top-0 lg:col-span-2">
                        <div class="flex flex-col">
                            <div class="bg-white rounded-xl shadow-lg p-8 border border-neutral-100">
                                <div class="aspect-[3/4] bg-neutral-100 rounded-xl overflow-hidden mb-6">
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

                                <button onclick="borrow_book.showModal()"
                                    class="btn bg-primary-600 hover:bg-primary-700 dark:bg-primary-500 dark:hover:bg-primary-600 text-white flex-1">
                                    <i class="iconoir-open-book text-xl"></i>
                                    Pinjam Buku
                                </button>
                            </div>

                        </div>
                    </div>
                </div>
            </div>

            <div
                class="w-full max-w-7xl mx-auto mt-12 border px-6 py-12 bg-white rounded-xl shadow-lg border-neutral-200">
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
                                        <i class="iconoir-user-badge-check
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
                                        <i class="iconoir-book-lock
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
                                        <i class="iconoir-calendar w-5 h-5 text-neutral-400 text-xl mr-3 mt-0.5"></i>
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
                                        <i class="iconoir-numbered-list-left
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
                                        <i class="iconoir-number-3-square
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
                                                {{ $setting->limit_day }} hari
                                            </p>
                                        </div>
                                    </div>

                                    <div class="flex items-start">
                                        <i class="iconoir-refresh w-5 h-5 text-neutral-400 text-xl mr-3 mt-0.5"></i>
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
</x-guest-layout>