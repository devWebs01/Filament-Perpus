<?php

use function Livewire\Volt\{state, computed};
use App\Services\BookmarkService;
use Jantinnerezo\LivewireAlert\Facades\LivewireAlert;

state(['bookId', 'compact' => false]);

$isBookmarked = computed(function () {
    if (!auth()->check()) {
        return false;
    }

    $bookmarkService = app(BookmarkService::class);

    return $bookmarkService->isBookmarked(auth()->user(), $this->bookId);
});

$toggleBookmark = function () {
    if (!auth()->check()) {
        return redirect()->route('login');
    }

    $bookmarkService = app(BookmarkService::class);
    $isBookmarked = $bookmarkService->toggle(auth()->user(), $this->bookId);

    if ($isBookmarked) {
        $this->dispatch('bookmark-added');
        LivewireAlert::title('Berhasil')->position('center')->timer(3000)->text('Buku berhasil disimpan ke daftar tersimpan')->success()->show();
    } else {
        $this->dispatch('bookmark-removed');
        LivewireAlert::title('Berhasil')->position('center')->timer(3000)->text('Buku berhasil dihapus dari daftar tersimpan')->info()->show();
    }
};

?>

<div>
    @if ($compact)
        {{-- Compact mode - Icon only --}}
        <button wire:click="toggleBookmark" title="{{ $this->isBookmarked ? 'Hapus dari tersimpan' : 'Simpan buku' }}"
            class="btn px-5 btn-square border transition-colors
                        {{ $this->isBookmarked
            ? 'bg-red-100 border-red-300 hover:bg-red-200
                                                               dark:bg-red-900/30 dark:border-red-800 dark:hover:bg-red-900/50'
            : 'bg-gray-100 border-gray-300 hover:bg-gray-200
                                                               dark:bg-gray-700 dark:border-gray-600 dark:hover:bg-gray-600' }}">

            @if ($this->isBookmarked)
                <i class="iconoir-trash text-xl text-red-600 dark:text-red-400"></i>
            @else
                <i class="iconoir-bookmark-book text-xl text-gray-700 dark:text-gray-300"></i>
            @endif
        </button>
    @else
        {{-- Full mode - Icon + Text --}}
        <button wire:click="toggleBookmark"
            class="btn px-5 border flex-1 transition-colors
                        {{ $this->isBookmarked
            ? 'bg-red-100 border-red-300 hover:bg-red-200
                                                               dark:bg-red-900/30 dark:border-red-800 dark:hover:bg-red-900/50 text-red-700 dark:text-red-300'
            : 'bg-gray-100 border-gray-300 hover:bg-gray-200
                                                               dark:bg-gray-700 dark:border-gray-600 dark:hover:bg-gray-600 text-black dark:text-white' }}">

            @if ($this->isBookmarked)
                <i class="iconoir-trash text-xl text-red-600 dark:text-red-400"></i>
            @else
                <i class="iconoir-bookmark-book text-xl"></i>
            @endif

            <span>{{ $this->isBookmarked ? 'Tersimpan' : 'Simpan Buku' }}</span>
        </button>
    @endif
</div>