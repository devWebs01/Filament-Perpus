<?php

namespace Database\Seeders;

use App\Models\Book;
use App\Models\Category;
use Illuminate\Database\Seeder;

class BookSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Pastikan categories sudah ada
        $categories = Category::all()->keyBy('name');

        if ($categories->isEmpty()) {
            $this->command->error('No categories found. Please run CategorySeeder first.');

            return;
        }

        $books = [
            // Computer Science Books
            [
                'title' => 'Pemrograman Web dengan Laravel',
                'author' => 'Ahmad Susanto',
                'isbn' => '978-602-6115-12-5',
                'publisher' => 'Informatika Bandung',
                'year_published' => 2023,
                'synopsis' => 'Buku panduan lengkap mengembangkan aplikasi web modern menggunakan framework Laravel. Mencakup instalasi, konfigurasi, routing, database, authentication, dan deployment.',
                'category' => 'Ilmu Komputer',
                'type' => 'Paket',
                'book_count' => 5,
                'price' => 125000,
            ],
            [
                'title' => 'Algoritma dan Struktur Data',
                'author' => 'Rinaldi Munir',
                'isbn' => '978-602-0234567-8',
                'publisher' => 'Informatika Bandung',
                'year_published' => 2022,
                'synopsis' => 'Pembahasan mendalam tentang algoritma dan struktur data yang penting untuk mahasiswa teknik informatika. Dilengkapi dengan contoh implementasi dalam bahasa C++.',
                'category' => 'Ilmu Komputer',
                'type' => 'Paket',
                'book_count' => 8,
                'price' => 98000,
            ],
            [
                'title' => 'Machine Learning untuk Pemula',
                'author' => 'Budi Santoso',
                'isbn' => '978-602-3456789-0',
                'publisher' => 'Elex Media Komputindo',
                'year_published' => 2023,
                'synopsis' => 'Pengenalan konsep machine learning dengan contoh praktis menggunakan Python. Cocok untuk pemula yang ingin mempelajari AI dan machine learning.',
                'category' => 'Ilmu Komputer',
                'type' => 'Paket',
                'book_count' => 3,
                'price' => 150000,
            ],

            // Mathematics Books
            [
                'title' => 'Matematika Dasar untuk SMA Kelas X',
                'author' => 'Tim Pengajar MGMP',
                'isbn' => '978-602-4123456-7',
                'publisher' => 'Erlangga',
                'year_published' => 2022,
                'synopsis' => 'Buku pegangan siswa SMA kelas X untuk mata pelajaran matematika. Mencakup materi logika matematika, fungsi, persamaan linear, dan geometri dasar.',
                'category' => 'Ilmu Pengetahuan & Matematika',
                'type' => 'Paket',
                'book_count' => 20,
                'price' => 85000,
            ],
            [
                'title' => 'Kalkulus dan Aplikasinya',
                'author' => 'Prof. Dr. Sutanto',
                'isbn' => '978-602-5678901-2',
                'publisher' => 'Gadjah Mada University Press',
                'year_published' => 2021,
                'synopsis' => 'Pembahasan lengkap kalkulus diferensial dan integral beserta aplikasinya dalam bidang teknik dan sains.',
                'category' => 'Ilmu Pengetahuan & Matematika',
                'type' => 'Paket',
                'book_count' => 6,
                'price' => 110000,
            ],

            // Economics Books
            [
                'title' => 'Pengantar Ekonomi Mikro',
                'author' => 'Dr. Irham Halim',
                'isbn' => '978-602-2345678-9',
                'publisher' => 'Fakultas Ekonomi UI',
                'year_published' => 2023,
                'synopsis' => 'Dasar-dasar ekonomi mikro yang mencakup teori permintaan dan penawaran, elastisitas, struktur pasar, dan kebijakan pemerintah.',
                'category' => 'Ekonomi & Keuangan',
                'type' => 'Paket',
                'book_count' => 10,
                'price' => 95000,
            ],
            [
                'title' => 'Manajemen Keuangan Syariah',
                'author' => 'Muhammad Syafi\'i Antonio',
                'isbn' => '978-602-3456789-1',
                'publisher' => 'Gema Insani',
                'year_published' => 2022,
                'synopsis' => 'Konsep dan praktek manajemen keuangan berbasis syariah, mencakup perbankan syariah, investasi, dan pembiayaan.',
                'category' => 'Ekonomi & Keuangan',
                'type' => 'Umum',
                'book_count' => 4,
                'price' => 135000,
            ],

            // Business Books
            [
                'title' => 'Kewirausahaan: Teori dan Praktik',
                'author' => 'Dr. H. Hendro Tjitraprawata',
                'isbn' => '978-602-4567890-1',
                'publisher' => 'Andi Publisher',
                'year_published' => 2023,
                'synopsis' => 'Panduan lengkap memulai dan mengembangkan usaha kecil dan menengah. Dilengkapi dengan studi kasus nyata.',
                'category' => 'Bisnis & Manajemen',
                'type' => 'Umum',
                'book_count' => 7,
                'price' => 88000,
            ],
            [
                'title' => 'Digital Marketing Strategi',
                'author' => 'Ryan Kristijulianto',
                'isbn' => '978-602-5678901-3',
                'publisher' => 'Elex Media Komputindo',
                'year_published' => 2022,
                'synopsis' => 'Strategi pemasaran digital yang efektif untuk era modern. Mencakup SEO, social media marketing, dan content marketing.',
                'category' => 'Bisnis & Manajemen',
                'type' => 'Umum',
                'book_count' => 5,
                'price' => 105000,
            ],

            // History Books
            [
                'title' => 'Sejarah Indonesia Modern',
                'author' => 'Prof. Dr. Anhar Gonggong',
                'isbn' => '978-602-1234567-8',
                'publisher' => 'Kompas Gramedia',
                'year_published' => 2021,
                'synopsis' => 'Sejarah Indonesia dari masa penjajahan hingga era reformasi dengan analisis mendalam tentang peristiwa-peristiwa penting.',
                'category' => 'Sejarah',
                'type' => 'Paket',
                'book_count' => 12,
                'price' => 95000,
            ],
            [
                'title' => 'Sejarah Pergerakan Nasional Indonesia',
                'author' => 'Dr. Sartono Kartodirdjo',
                'isbn' => '978-602-2345678-0',
                'publisher' => 'Pustaka Jaya',
                'year_published' => 2020,
                'synopsis' => 'Kisah perjuangan bangsa Indonesia mencapai kemerdekaan dari berbagai aspek politik, sosial, dan budaya.',
                'category' => 'Sejarah',
                'type' => 'Umum',
                'book_count' => 6,
                'price' => 78000,
            ],

            // Philosophy Books
            [
                'title' => 'Filsafat Ilmu Pengetahuan',
                'author' => 'Prof. Dr. Franz Magnis Suseno',
                'isbn' => '978-602-3456789-2',
                'publisher' => 'Gramedia Pustaka Utama',
                'year_published' => 2022,
                'synopsis' => 'Pembahasan filsafat ilmu yang membahas hakikat ilmu, metode ilmiah, dan etika dalam penelitian.',
                'category' => 'Filsafat',
                'type' => 'Umum',
                'book_count' => 3,
                'price' => 115000,
            ],
        ];

        foreach ($books as $bookData) {
            // Cari category berdasarkan nama
            $category = $categories->get($bookData['category']);

            if (! $category) {
                $this->command->warn("Category '{$bookData['category']}' not found. Skipping book: {$bookData['title']}");

                continue;
            }

            // Hapus key category dari bookData
            unset($bookData['category']);

            // Tambahkan data tambahan
            $bookData['category_id'] = $category->id;
            $bookData['source'] = 'Pembelian Langsung';
            $bookData['bookshelf'] = 'Rak '.rand(1, 20);
            $bookData['image'] = 'books/default-book-cover.jpg'; // Placeholder

            try {
                $book = Book::create($bookData);
                $this->command->info("✓ Buku ditambahkan: {$book->title} ({$book->book_count} eksemplar)");
            } catch (\Exception $e) {
                $this->command->error("✗ Gagal menambahkan buku '{$bookData['title']}': ".$e->getMessage());
            }
        }

        $this->command->info("\nSeeder buku selesai dijalankan.");
    }
}
