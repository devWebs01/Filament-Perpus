<?php

namespace App\Services;

use App\Models\Bookmark;
use App\Models\User;
use Illuminate\Database\Eloquent\Collection;

class BookmarkService
{
    /**
     * Get all bookmarks for a user with book relationship loaded
     */
    public function getUserBookmarks(User $user): Collection
    {
        return Bookmark::where('user_id', $user->id)
            ->with('book')
            ->latest()
            ->get();
    }

    /**
     * Add a bookmark for a user
     */
    public function addBookmark(User $user, int $bookId): Bookmark
    {
        return Bookmark::firstOrCreate(
            [
                'user_id' => $user->id,
                'book_id' => $bookId,
            ]
        );
    }

    /**
     * Remove a bookmark for a user
     */
    public function removeBookmark(User $user, int $bookId): bool
    {
        return Bookmark::where('user_id', $user->id)
            ->where('book_id', $bookId)
            ->delete();
    }

    /**
     * Check if a book is bookmarked by a user
     */
    public function isBookmarked(User $user, int $bookId): bool
    {
        return Bookmark::where('user_id', $user->id)
            ->where('book_id', $bookId)
            ->exists();
    }

    /**
     * Get bookmark count for a user
     */
    public function getBookmarkCount(User $user): int
    {
        return Bookmark::where('user_id', $user->id)->count();
    }

    /**
     * Toggle bookmark for a user (add if not exists, remove if exists)
     */
    public function toggle(User $user, int $bookId): bool
    {
        $existing = Bookmark::where('user_id', $user->id)
            ->where('book_id', $bookId)
            ->first();

        if ($existing) {
            $existing->delete();

            return false;
        }

        Bookmark::create([
            'user_id' => $user->id,
            'book_id' => $bookId,
        ]);

        return true;
    }
}
