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
    $alertDuration = config('app.library.alert_duration', 3000);

    if (!auth()->check()) {
        return redirect()->route('login');
    }

    $bookmarkService = app(BookmarkService::class);
    $isBookmarked = $bookmarkService->toggle(auth()->user(), $this->bookId);

    if ($isBookmarked) {
        $this->dispatch('bookmark-added');
        LivewireAlert::title('Berhasil')->position('center')->timer($alertDuration)->text('Buku berhasil disimpan ke daftar tersimpan')->success()->show();
    } else {
        $this->dispatch('bookmark-removed');
        LivewireAlert::title('Berhasil')->position('center')->timer($alertDuration)->text('Buku berhasil dihapus dari daftar tersimpan')->info()->show();
    }
};

?>

<div>
    @if ($compact)
        {{-- Compact mode - Icon only --}}
        <x-button
            wire:click="toggleBookmark"
            wire:loading.attr="disabled"
            :icon="$this->isBookmarked ? 'o-trash' : 'o-bookmark'"
            icon-only
            squared
            :tooltip="$this->isBookmarked ? 'Hapus dari tersimpan' : 'Simpan buku'"
            :class="$this->isBookmarked
                ? 'bg-red-100 hover:bg-red-200 dark:bg-red-900/30 dark:hover:bg-red-900/50 border-red-300 dark:border-red-800'
                : 'bg-gray-100 hover:bg-gray-200 dark:bg-gray-700 dark:hover:bg-gray-600 border-gray-300 dark:border-gray-600'"
            :icon-color="$this->isBookmarked ? 'text-red-600 dark:text-red-400' : 'text-gray-700 dark:text-gray-300'"
        />
    @else
        {{-- Full mode - Icon + Text --}}
        <x-button
            wire:click="toggleBookmark"
            wire:loading.attr="disabled"
            :icon="$this->isBookmarked ? 'o-trash' : 'o-bookmark'"
            :label="$this->isBookmarked ? 'Tersimpan' : 'Simpan Buku'"
            :class="$this->isBookmarked
                ? 'bg-red-100 hover:bg-red-200 dark:bg-red-900/30 dark:hover:bg-red-900/50 border-red-300 dark:border-red-800 text-red-700 dark:text-red-300'
                : 'bg-gray-100 hover:bg-gray-200 dark:bg-gray-700 dark:hover:bg-gray-600 border-gray-300 dark:border-gray-600 text-black dark:text-white'"
        />
    @endif
</div>