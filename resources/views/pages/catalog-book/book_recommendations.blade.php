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
                class="group bg-white rounded-xl border border-neutral-200 shadow-sm hover:shadow-lg p-3 flex flex-col h-full transition-all duration-300 hover:-translate-y-1">
                <!-- Bagian isi -->
                <a href="{{ route('book-detail', ['book' => $book->id]) }}" class="block flex-1 min-w-0">
                    <div
                        class="aspect-[12/11] bg-neutral-100 rounded-xl p-2 sm:p-3 overflow-hidden group-hover:bg-neutral-50 transition-colors duration-300">
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

                <!-- Tombol Bookmark & Detail -->
                <div class="mt-6 flex gap-2">
                    <div class="flex-shrink-0">
                        @livewire('bookmark-button', ['bookId' => $book->id, 'compact' => true], key('book-rec-' . $book->id))
                    </div>
                    <a href="{{ route('book-detail', ['book' => $book->id]) }}" type="button"
                        class="btn flex-1 justify-between bg-primary-600 hover:bg-primary-700 dark:bg-primary-500 dark:hover:bg-primary-600 text-white">
                        <strong>Detail</strong>
                        <i class="iconoir-arrow-right text-white dark:text-gray-100 text-xl"></i>
                    </a>
                </div>
            </div>
        @endforeach
    </div>
</div>
@endvolt