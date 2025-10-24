<?php

namespace Database\Seeders;

use App\Models\Book;
use App\Models\Status;
use App\Models\Transaction;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class TransactionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Pastikan data yang dibutuhkan sudah ada
        $users = User::whereHas('userDetail')->get();
        $books = Book::all();
        $statuses = Status::all()->keyBy('name');

        if ($users->isEmpty()) {
            $this->command->error('No users with details found. Please run LibraryUsersSeeder first.');

            return;
        }

        if ($books->isEmpty()) {
            $this->command->error('No books found. Please run BookSeeder first.');

            return;
        }

        if ($statuses->isEmpty()) {
            $this->command->error('No statuses found. Please run StatusSeeder first.');

            return;
        }

        // Status mapping
        $borrowedStatus = $statuses->get('Dipinjam');
        $returnedStatus = $statuses->get('Dikembalikan');
        $overdueStatus = $statuses->get('Terlambat');

        // Create sample transactions
        $transactions = [];

        // Active borrowings (Dipinjam)
        for ($i = 0; $i < 8; $i++) {
            $user = $users->random();
            $book = $books->random();
            $borrowDate = now()->subDays(rand(1, 14));

            $transactions[] = [
                'code' => 'TRX-'.date('Ymd', $borrowDate->timestamp).'-'.strtoupper(Str::random(4)),
                'user_id' => $user->id,
                'book_id' => $book->id,
                'status_id' => $borrowedStatus->id,
                'borrow_date' => $borrowDate->format('Y-m-d'),
                'return_date' => null,
                'penalty_total' => 0,
                'created_at' => $borrowDate,
                'updated_at' => $borrowDate,
            ];
        }

        // Overdue books (Terlambat)
        for ($i = 0; $i < 5; $i++) {
            $user = $users->random();
            $book = $books->random();
            $borrowDate = now()->subDays(rand(15, 30));

            $transactions[] = [
                'code' => 'TRX-'.date('Ymd', $borrowDate->timestamp).'-'.strtoupper(Str::random(4)),
                'user_id' => $user->id,
                'book_id' => $book->id,
                'status_id' => $overdueStatus->id,
                'borrow_date' => $borrowDate->format('Y-m-d'),
                'return_date' => null,
                'penalty_total' => 5000, // Fixed penalty for overdue
                'created_at' => $borrowDate,
                'updated_at' => now()->subDays(rand(1, 5)),
            ];
        }

        // Returned books (Dikembalikan)
        for ($i = 0; $i < 12; $i++) {
            $user = $users->random();
            $book = $books->random();
            $borrowDate = now()->subDays(rand(20, 60));
            $returnDate = $borrowDate->copy()->addDays(rand(1, 14));

            // Determine if returned on time or late
            $isLate = $returnDate > $borrowDate->copy()->addDays(7);
            $penalty = $isLate ? 5000 : 0;
            $status = $isLate ? $overdueStatus : $returnedStatus;

            $transactions[] = [
                'code' => 'TRX-'.date('Ymd', $borrowDate->timestamp).'-'.strtoupper(Str::random(4)),
                'user_id' => $user->id,
                'book_id' => $book->id,
                'status_id' => $status->id,
                'borrow_date' => $borrowDate->format('Y-m-d'),
                'return_date' => $returnDate->format('Y-m-d'),
                'penalty_total' => $penalty,
                'created_at' => $borrowDate,
                'updated_at' => $returnDate,
            ];
        }

        // Insert all transactions
        foreach ($transactions as $transactionData) {
            try {
                $transaction = Transaction::create($transactionData);

                $statusName = $statuses->firstWhere('id', $transactionData['status_id'])->name;
                $userName = User::find($transactionData['user_id'])->name;
                $bookTitle = Book::find($transactionData['book_id'])->title;

                $this->command->info(
                    "✓ Transaksi ditambahkan: {$transaction->code} - {$userName} meminjam '{$bookTitle}' ({$statusName})"
                );
            } catch (\Exception $e) {
                $this->command->error('✗ Gagal menambahkan transaksi: '.$e->getMessage());
            }
        }

        // Create some specific examples for demonstration
        $this->createDemoTransactions();

        $this->command->info("\nSeeder transaksi selesai dijalankan.");
        $this->command->info('Total transaksi yang dibuat: '.count($transactions));
    }

    /**
     * Create some specific demo transactions for testing
     */
    private function createDemoTransactions(): void
    {
        // Get specific users for demo
        $demoUser = User::where('email', 'budi.santoso@siswa.sch.id')->first();
        $demoBook = Book::where('title', 'Pemrograman Web dengan Laravel')->first();

        if (! $demoUser || ! $demoBook) {
            return;
        }

        $borrowedStatus = Status::where('name', 'Dipinjam')->first();
        $returnedStatus = Status::where('name', 'Dikembalikan')->first();
        $overdueStatus = Status::where('name', 'Terlambat')->first();

        // Demo 1: Active borrowing
        Transaction::create([
            'code' => 'DEMO-001',
            'user_id' => $demoUser->id,
            'book_id' => $demoBook->id,
            'status_id' => $borrowedStatus->id,
            'borrow_date' => now()->subDays(3)->format('Y-m-d'),
            'return_date' => null,
            'penalty_total' => 0,
        ]);

        // Demo 2: Recently returned
        $otherBook = Book::where('title', 'Algoritma dan Struktur Data')->first();
        if ($otherBook) {
            Transaction::create([
                'code' => 'DEMO-002',
                'user_id' => $demoUser->id,
                'book_id' => $otherBook->id,
                'status_id' => $returnedStatus->id,
                'borrow_date' => now()->subDays(14)->format('Y-m-d'),
                'return_date' => now()->subDays(1)->format('Y-m-d'),
                'penalty_total' => 0,
            ]);
        }

        // Demo 3: Overdue transaction
        $overdueBook = Book::where('title', 'Machine Learning untuk Pemula')->first();
        $overdueUser = User::where('email', 'siti.aminah@siswa.sch.id')->first();

        if ($overdueBook && $overdueUser) {
            Transaction::create([
                'code' => 'DEMO-003',
                'user_id' => $overdueUser->id,
                'book_id' => $overdueBook->id,
                'status_id' => $overdueStatus->id,
                'borrow_date' => now()->subDays(20)->format('Y-m-d'),
                'return_date' => null,
                'penalty_total' => 5000,
            ]);
        }

        $this->command->info('✓ Transaksi demo telah ditambahkan untuk pengujian.');
    }
}
