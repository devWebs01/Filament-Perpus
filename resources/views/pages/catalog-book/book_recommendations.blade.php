<?php

use function Livewire\Volt\{state};
use App\Models\{Book};

state([
    'books_populer' => Book::inRandomOrder()->limit(8)->get(),
]);

?>

@volt
    <div>
        @include('components.partials.aos')

        <div class="grid grid-cols-2 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-3 sm:gap-4 md:gap-5">
            @foreach ($books_populer as $book)
                <div
                    class="group bg-white rounded-lg border border-neutral-200 shadow-sm hover:shadow-lg p-3 flex flex-col h-full transition-all duration-300 hover:-translate-y-1">
                    <!-- Bagian isi -->
                    <a href="{{ route('book-detail', ['book' => $book->id]) }}" class="block flex-1 min-w-0">
                        <div
                            class="aspect-[12/11] bg-neutral-100 rounded-lg p-2 sm:p-3 overflow-hidden group-hover:bg-neutral-50 transition-colors duration-300">
                            <img src="{{ Storage::url($book->image) }}" alt="{{ $book->title }}"
                                class="w-full h-full object-contain group-hover:scale-105 transition-transform duration-300" />
                        </div>

                        <h5
                            class="text-sm sm:text-base font-semibold text-neutral-900 mt-2 sm:mt-3 group-hover:text-primary-600 transition-colors duration-200 line-clamp-2 min-h-0">
                            {{ Str::limit($book->title, 25, '...') }}
                        </h5>
                    </a>

                    <!-- Spacer otomatis dorong tombol ke bawah -->
                    <div class="flex-1"></div>

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
    </div>
@endvolt
