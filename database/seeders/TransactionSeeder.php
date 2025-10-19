<?php

namespace Database\Seeders;

use App\Models\Book;
use App\Models\Penalty;
use App\Models\Status;
use App\Models\Transaction;
use Illuminate\Database\Seeder;

class TransactionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Pastikan ada data buku, kategori, dan status sebelum membuat transaksi
        if (Book::count() == 0 || Status::count() == 0) {
            $this->command->error('Tidak ada data buku atau status. Jalankan BookSeeder dan StatusSeeder terlebih dahulu.');

            return;
        }

        // Ambil semua buku dan status yang tersedia
        $books = Book::all();
        $statuses = Status::all();

        // Buat 20 transaksi dengan data yang realistis
        for ($i = 0; $i < 20; $i++) {
            $book = $books->random();
            $status = $statuses->random();

            // Generate tanggal pinjam dan kembali yang realistis
            $borrowDate = now()->subDays(rand(1, 30));
            $returnDate = rand(0, 1) ? $borrowDate->copy()->addDays(rand(7, 14)) : null;

            // Hitung denda berdasarkan status
            $penaltyTotal = 0;
            if (in_array($status->name, ['Terlambat', 'Hilang', 'Rusak Ringan', 'Rusak Berat'])) {
                $penaltyTotal = $status->amount;
            }

            // Buat transaksi
            $transaction = Transaction::create([
                'code' => 'TRX-'.str_pad($i + 1, 4, '0', STR_PAD_LEFT),
                'book_id' => $book->id,
                'user_id' => 1, // Asumsikan user dengan ID 1 (admin/user pertama)
                'borrow_date' => $borrowDate,
                'return_date' => $returnDate,
                'status_id' => $status->id,
                'penalty_total' => $penaltyTotal,
            ]);

            // Buat penalty jika ada denda
            if ($penaltyTotal > 0) {
                Penalty::create([
                    'transaction_id' => $transaction->id,
                    'status' => rand(0, 1) ? 'Lunas' : 'Belum Lunas',
                    'image' => rand(0, 1) ? 'penalties/penalty_'.$transaction->id.'.jpg' : null,
                ]);
            }

            $this->command->info("Transaksi dibuat: {$transaction->code} - Buku: {$book->title} - Status: {$status->name}");
        }

        $this->command->info('TransactionSeeder selesai dijalankan.');
    }
}
