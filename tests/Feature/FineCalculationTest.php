<?php

namespace Tests\Feature;

use App\Models\Book;
use App\Models\Status;
use App\Models\Transaction;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class FineCalculationTest extends TestCase
{
    use RefreshDatabase;

    public function test_transaksi_terlambat_dihitung_sebagai_borrowed_dan_overdue()
    {
        // Seed necessary data
        \App\Models\Setting::create(['name' => 'Test Lib', 'max_borrow' => 5]);

        $dipinjam = Status::firstOrCreate(['name' => 'Dipinjam'], ['amount' => 0]);
        $terlambat = Status::firstOrCreate(['name' => 'Terlambat'], ['amount' => 0]);

        $user = User::factory()->create();
        // Create user detail if needed for the observer check "isStudent"
        // If user factory doesn't create detail, we might need to.
        // Logic: $transaction->user->userDetail?->isStudent()
        // If userDetail is null, safe navigation returns null. Boolean check is false.
        // So we don't strictly need userDetail if we don't care about the borrowing limit check blocking us.
        // However, the observer checks limit. With max_borrow=5 and count=0, it should pass.

        $category = \App\Models\Category::create(['name' => 'Test Category', 'slug' => 'test-category']);

        $book = Book::create([
            'title' => 'Test Book',
            'author' => 'Test Author',
            'publisher' => 'Test Publisher',
            'year_published' => 2020,
            'book_count' => 10,
            'price' => 50000,
            'image' => 'dummy.jpg',
            'isbn' => '978-3-16-148410-0',
            'synopsis' => 'Test Synopsis',
            'type' => 'other',
            'category_id' => $category->id,
        ]);

        // Test Case 1: Status Terlambat
        $dueDate = Carbon::now()->subDays(5); // Due 5 days ago

        $transaction = new Transaction;
        $transaction->user_id = $user->id;
        $transaction->book_id = $book->id;
        $transaction->borrow_date = Carbon::now()->subDays(10);
        $transaction->due_date = $dueDate;
        $transaction->status_id = $terlambat->id;
        $transaction->save();

        // Assert isBorrowed includes Terlambat
        $this->assertTrue($transaction->isBorrowed(), 'Transaction with status Terlambat should be considered borrowed');

        // Assert isOverdue
        $this->assertTrue($transaction->isOverdue(), 'Transaction past due date should be overdue');

        // Assert Penalty
        // Force due_date to startOfDay to be precise for diffInDays
        $transaction->due_date = Carbon::now()->subDays(5)->startOfDay();
        $transaction->save();

        $expectedDays = 5;
        $expectedPenalty = 5000;

        $this->assertEquals($expectedDays, $transaction->getDaysOverdue(), "Days overdue should be {$expectedDays}, got {$transaction->getDaysOverdue()}");
        $this->assertEquals($expectedPenalty, $transaction->getPenalty(), "Penalty should be {$expectedPenalty}, got {$transaction->getPenalty()}");

        // Clean up not needed with RefreshDatabase
    }
}
