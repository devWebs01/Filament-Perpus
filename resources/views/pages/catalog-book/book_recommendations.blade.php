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

        <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4 max-xl:gap-4 gap-6">
            @foreach ($books_populer as $book)
                <div
                    class="bg-white shadow-sm border border-gray-200 rounded-lg p-3 flex flex-col h-full transition-colors duration-300">
                    <!-- Bagian isi -->
                    <a href="javascript:void(0)" class="block flex-1">
                        <div class="aspect-[12/11] bg-gray-100 rounded-lg p-4">
                            <img src="{{ Storage::url($book->image) }}" alt="{{ $book->title }}"
                                class="w-full h-full object-contain" />
                        </div>

                        <h5 class="text-base font-semibold text-slate-900 mt-3">
                            {{ Str::limit($book->title, 25, '...') }}
                        </h5>
                    </a>

                    <!-- Spacer otomatis dorong tombol ke bawah -->
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
    </div>
@endvolt
