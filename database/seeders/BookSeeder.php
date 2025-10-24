<?php

namespace Database\Seeders;

use App\Models\Book;
use App\Models\Category;
use Illuminate\Database\Seeder;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Stichoza\GoogleTranslate\GoogleTranslate;

class BookSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $limitPerCategory = 10; // Batasi jumlah buku per kategori

        $categories = [
            'Computer-Science' => 'Ilmu Komputer',
            'Science-&-Mathematics' => 'Ilmu Pengetahuan & Matematika',
            'Economics-&-Finance' => 'Ekonomi & Keuangan',
            'Business-&-Management' => 'Bisnis & Manajemen',
            'Politics-&-Government' => 'Politik & Pemerintahan',
            'History' => 'Sejarah',
            'Philosophy' => 'Filsafat',
        ];

        $translator = new GoogleTranslate('id');

        foreach ($categories as $query => $categoryNameInIndonesian) {
            $this->command->info("Mengambil buku untuk kategori: {$categoryNameInIndonesian}...");

            // Lakukan pencarian API untuk setiap kategori tanpa limit
            $booksResponse = Http::get('https://www.dbooks.org/api/search/' . $query)->json();

            if (isset($booksResponse['books'])) {
                // Batasi jumlah buku yang akan diproses
                $books = array_slice($booksResponse['books'], 0, $limitPerCategory);

                foreach ($books as $book) {
                    if (isset($book['id'])) {
                        $bookDetails = Http::get('https://www.dbooks.org/api/book/' . $book['id'])->json();

                        if (isset($bookDetails['image'])) {
                            // Terjemahkan kategori jika perlu
                            try {
                                $categoryName = $translator->translate($categoryNameInIndonesian);
                            } catch (\Exception $e) {
                                $this->command->warn('Gagal menerjemahkan kategori: ' . $e->getMessage());
                                $categoryName = $categoryNameInIndonesian;
                            }

                            // Cek apakah kategori sudah ada di database, jika tidak buat baru
                            $category = Category::firstOrCreate([
                                'name' => $categoryName,
                                'slug' => Str::slug($categoryName),
                            ]);

                            $imageName = basename($bookDetails['image']);

                            // Menerjemahkan judul dan deskripsi
                            try {
                                $title = $translator->translate($bookDetails['title']);
                            } catch (\Exception $e) {
                                $this->command->warn('Gagal menerjemahkan judul: ' . $e->getMessage());
                                $title = $bookDetails['title'];
                            }

                            try {
                                $synopsis = $translator->translate($bookDetails['description']);
                            } catch (\Exception $e) {
                                $this->command->warn('Gagal menerjemahkan deskripsi: ' . $e->getMessage());
                                $synopsis = $bookDetails['description'];
                            }

                            // Simpan data buku
                            $bookData = [
                                'title' => $title,
                                'image' => 'images/' . $imageName,
                                'category_id' => $category->id,
                                'isbn' => $bookDetails['id'],
                                'author' => $bookDetails['authors'],
                                'year_published' => $bookDetails['year'],
                                'publisher' => $bookDetails['publisher'],
                                'synopsis' => $synopsis,
                                'book_count' => rand(1, 100),
                                'source' => 'Pembelian Langsung',
                                'bookshelf' => 'Rak ' . rand(1, 20),
                                'type' => Arr::random([
                                    'fiction',
                                    'non-fiction',
                                    'reference',
                                    'textbook',
                                    'journal',
                                    'other',
                                ]),
                                'price' => rand(25, 90) . 0000,
                            ];

                            $bookModel = Book::create($bookData);

                            // Simpan gambar ke storage
                            $imageUrl = $bookDetails['image'];
                            $imageData = file_get_contents($imageUrl);
                            Storage::put('books/' . $imageName, $imageData);

                            $this->command->info('Buku ditambahkan: ' . $bookModel->title . ' - Kategori: ' . $category->name);
                        } else {
                            $this->command->error('Struktur bookDetails tidak valid: gambar hilang');
                        }
                    } else {
                        $this->command->error('Struktur buku tidak valid: ID hilang');
                    }
                }
            } else {
                $this->command->error('Struktur booksResponse tidak valid: daftar buku hilang');
            }
        }
    }
}
