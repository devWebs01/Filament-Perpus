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
                <x-card class="group flex flex-col h-full hover:-translate-y-1 transition-all duration-300 p-3 shadow-sm hover:shadow-lg">
                    <!-- Bagian isi -->
                    <a href="{{ route('book-detail', ['book' => $book->id]) }}" class="block flex-1">
                        <div class="aspect-[12/11] bg-neutral-100 rounded-xl p-4
                                      overflow-hidden group-hover:bg-neutral-50
                                      transition-colors duration-300">
                            <img src="{{ Storage::url($book->image) }}" alt="{{ $book->title }}"
                                class="w-full h-full object-contain
                                       group-hover:scale-105
                                       transition-transform duration-300" />
                        </div>

                        <h5 class="text-base font-semibold text-neutral-900 mt-3
                                    group-hover:text-primary-600
                                    transition-colors duration-200
                                    line-clamp-2">
                            {{ Str::limit($book->title, 25, '...') }}
                        </h5>
                    </a>

                    <!-- Spacer otomatis dorong tombol ke bawah -->
                    <div class="flex-1"></div>

                    <!-- Tombol Detail -->
                    <div class="mt-6">
                        <x-button
                            icon="o-arrow-right"
                            label="Detail"
                            link="{{ route('book-detail', ['book' => $book->id]) }}"
                            class="w-full"
                            spinner
                        />
                    </div>
                </x-card>
            @endforeach
        </div>
    </div>
@endvolt
