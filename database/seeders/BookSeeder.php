<?php

namespace Database\Seeders;

use App\Models\Book;
use App\Models\Category;
use Illuminate\Database\Seeder;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class BookSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $this->command->info('📚 Memulai BookSeeder...');

        // Prioritaskan mode offline dengan fallback data lokal
        if ($this->useOfflineMode()) {
            $this->command->info('📶 Menggunakan mode offline (fallback data lokal)');
            $this->seedOfflineBooks();

            return;
        }

        $this->command->info('🌐 Mencoba mode online dengan API...');
        $this->seedOnlineBooks();
    }

    /**
     * Check if should use offline mode
     */
    private function useOfflineMode(): bool
    {
        // Use offline mode if:
        // 1. APP_ENV=testing
        // 2. Explicitly disabled via env
        // 3. Previous API failures detected
        return app()->environment('testing') ||
               config('app.disable_external_apis', false) ||
               $this->hasPreviousApiFailures();
    }

    /**
     * Check if there were previous API failures
     */
    private function hasPreviousApiFailures(): bool
    {
        return Storage::disk('local')->exists('api_failures.log');
    }

    /**
     * Seed books using online API with better error handling
     */
    private function seedOnlineBooks(): void
    {
        $limitPerCategory = 5; // Kurangi limit untuk menghindari timeout
        $totalSuccess = 0;
        $totalFailures = 0;

        $categories = [
            'Computer-Science' => 'Ilmu Komputer',
            'Business-&-Management' => 'Bisnis & Manajemen',
            'Science-&-Mathematics' => 'Ilmu Pengetahuan & Matematika',
        ];

        foreach ($categories as $query => $categoryNameInIndonesian) {
            $this->command->info("📖 Mengambil buku untuk kategori: {$categoryNameInIndonesian}...");

            try {
                // Timeout lebih singkat dan retry logic
                $booksResponse = Http::timeout(10)
                    ->retry(2, 1000) // 2 retry dengan 1 detik delay
                    ->get('https://www.dbooks.org/api/search/'.$query);

                if (! $booksResponse->successful()) {
                    $this->command->warn("⚠️  Gagal mengambil data untuk kategori: {$categoryNameInIndonesian}");
                    $totalFailures++;

                    continue;
                }

                $data = $booksResponse->json();
                if (! isset($data['books']) || empty($data['books'])) {
                    $this->command->warn("⚠️  Tidak ada buku ditemukan untuk kategori: {$categoryNameInIndonesian}");
                    $totalFailures++;

                    continue;
                }

                // Batasi jumlah buku yang akan diproses
                $books = array_slice($data['books'], 0, $limitPerCategory);

                foreach ($books as $book) {
                    if (! isset($book['id'])) {
                        continue;
                    }

                    if ($this->processBookFromApi($book, $categoryNameInIndonesian)) {
                        $totalSuccess++;
                    } else {
                        $totalFailures++;
                    }
                }

            } catch (\Exception $e) {
                $this->command->error("❌ Error processing category {$categoryNameInIndonesian}: ".$e->getMessage());
                $totalFailures++;

                // Log failure for future offline mode
                $this->logApiFailure($categoryNameInIndonesian, $e->getMessage());
            }
        }

        // Jika terlalu banyak failures, fallback ke offline mode
        if ($totalFailures > $totalSuccess) {
            $this->command->warn('⚠️  Terlalu banyak kegagalan API, beralih ke mode offline...');
            $this->seedOfflineBooks();

            return;
        }

        $this->command->info("✅ BookSeeder online mode selesai: {$totalSuccess} berhasil, {$totalFailures} gagal");
    }

    /**
     * Process individual book from API
     */
    private function processBookFromApi(array $book, string $categoryName): bool
    {
        try {
            // Timeout lebih singkat untuk detail buku
            $bookDetailsResponse = Http::timeout(5)
                ->retry(1, 500)
                ->get('https://www.dbooks.org/api/book/'.$book['id']);

            if (! $bookDetailsResponse->successful()) {
                return false;
            }

            $bookDetails = $bookDetailsResponse->json();
            if (! isset($bookDetails['title']) || ! isset($bookDetails['image'])) {
                return false;
            }

            // Buat atau dapatkan kategori
            $category = Category::firstOrCreate([
                'name' => $categoryName,
                'slug' => Str::slug($categoryName),
            ]);

            // Generate image name
            $imageName = 'book_'.Str::slug($bookDetails['title']).'.jpg';

            // Simpan data buku
            $bookData = [
                'title' => $this->cleanTitle($bookDetails['title']),
                'image' => $imageName,
                'category_id' => $category->id,
                'isbn' => $bookDetails['id'] ?? uniqid(),
                'author' => $this->cleanAuthors($bookDetails['authors'] ?? 'Unknown Author'),
                'year_published' => $bookDetails['year'] ?? date('Y'),
                'publisher' => $bookDetails['publisher'] ?? 'Unknown Publisher',
                'synopsis' => $this->cleanDescription($bookDetails['description'] ?? 'Tidak ada deskripsi tersedia.'),
                'book_count' => rand(1, 10),
                'source' => 'API External',
                'bookshelf' => 'Rak '.rand(1, 20),
                'type' => Arr::random([
                    'fiction', 'non-fiction', 'reference', 'textbook', 'other',
                ]),
                'price' => rand(25000, 150000),
            ];

            $bookModel = Book::create($bookData);

            // Coba unduh gambar dengan timeout singkat
            $this->downloadBookImage($bookDetails['image'], $imageName, $bookModel->title);

            return true;

        } catch (\Exception $e) {
            $this->command->warn('Gagal memproses buku '.$book['id'].': '.$e->getMessage());

            return false;
        }
    }

    /**
     * Download book image with error handling
     */
    private function downloadBookImage(string $imageUrl, string $imageName, string $bookTitle): void
    {
        try {
            // Pastikan direktori ada
            if (! Storage::disk('public')->exists('books')) {
                Storage::disk('public')->makeDirectory('books');
            }

            $imageResponse = Http::timeout(5)->get($imageUrl);
            if ($imageResponse->successful()) {
                Storage::disk('public')->put("books/{$imageName}", $imageResponse->body());
            } else {
                $this->command->warn("⚠️  Gagal mengunduh gambar untuk: {$bookTitle}");
            }
        } catch (\Exception $e) {
            // Silent fail untuk image download
        }
    }

    /**
     * Seed books using local data
     */
    private function seedOfflineBooks(): void
    {
        $this->command->info('📚 Menggunakan data buku lokal...');

        // Pastikan direktori untuk gambar buku ada
        if (! Storage::disk('public')->exists('books')) {
            Storage::disk('public')->makeDirectory('books');
        }

        // Data buku sample lokal
        $books = $this->getLocalBookData();

        foreach ($books as $bookData) {
            $this->createLocalBook($bookData);
        }

        $this->command->info('✅ Data buku lokal berhasil dibuat!');
    }

    /**
     * Get local book data
     */
    private function getLocalBookData(): array
    {
        return [
            [
                'title' => 'Algoritma dan Pemrograman',
                'author' => 'Dr. Budi Santoso, M.Kom',
                'isbn' => '978-602-123-456-1',
                'publisher' => 'Penerbit Informatika',
                'year_published' => 2023,
                'synopsis' => 'Buku panduan lengkap untuk memahami konsep algoritma dan pemrograman komputer dari dasar hingga tingkat lanjut.',
                'category' => 'Ilmu Komputer',
                'book_count' => 5,
                'source' => 'Pembelian Langsung',
                'bookshelf' => 'Rak A1',
                'type' => 'textbook',
                'price' => 85000,
            ],
            [
                'title' => 'Pemrograman Web dengan Laravel',
                'author' => 'Ahmad Fauzi, S.T.',
                'isbn' => '978-602-123-456-2',
                'publisher' => 'Tech Publisher',
                'year_published' => 2023,
                'synopsis' => 'Panduan praktis mengembangkan aplikasi web modern menggunakan framework Laravel.',
                'category' => 'Ilmu Komputer',
                'book_count' => 3,
                'source' => 'Pembelian Langsung',
                'bookshelf' => 'Rak A2',
                'type' => 'textbook',
                'price' => 95000,
            ],
            [
                'title' => 'Manajemen Keuangan Modern',
                'author' => 'Dr. Rizki Pratama, SE., M.M.',
                'isbn' => '978-602-234-567-1',
                'publisher' => 'Economic Publisher',
                'year_published' => 2023,
                'synopsis' => 'Panduan lengkap manajemen keuangan perusahaan modern dengan pendekatan praktis.',
                'category' => 'Bisnis & Manajemen',
                'book_count' => 6,
                'source' => 'Pembelian Langsung',
                'bookshelf' => 'Rak B1',
                'type' => 'textbook',
                'price' => 75000,
            ],
            [
                'title' => 'Strategi Bisnis Digital',
                'author' => 'Dr. Dewi Lestari, MBA',
                'isbn' => '978-602-345-678-1',
                'publisher' => 'Business Media',
                'year_published' => 2023,
                'synopsis' => 'Transformasi digital dan strategi bisnis di era industri 4.0.',
                'category' => 'Bisnis & Manajemen',
                'book_count' => 5,
                'source' => 'Pembelian Langsung',
                'bookshelf' => 'Rak C1',
                'type' => 'non-fiction',
                'price' => 80000,
            ],
            [
                'title' => 'Fisika Dasar untuk SMA',
                'author' => 'Prof. Dr. Bambang Suryanto, M.Si',
                'isbn' => '978-602-456-789-1',
                'publisher' => 'Education Press',
                'year_published' => 2023,
                'synopsis' => 'Buku fisika dasar lengkap untuk siswa SMA dengan contoh soal dan pembahasan.',
                'category' => 'Ilmu Pengetahuan & Matematika',
                'book_count' => 8,
                'source' => 'Pembelian Langsung',
                'bookshelf' => 'Rak D1',
                'type' => 'textbook',
                'price' => 55000,
            ],
        ];
    }

    /**
     * Create local book
     */
    private function createLocalBook(array $bookData): void
    {
        $category = Category::firstOrCreate([
            'name' => $bookData['category'],
            'slug' => Str::slug($bookData['category']),
        ]);

        $imageName = 'book_'.Str::slug($bookData['title']).'.jpg';

        Book::create([
            'title' => $bookData['title'],
            'image' => $imageName,
            'category_id' => $category->id,
            'isbn' => $bookData['isbn'],
            'author' => $bookData['author'],
            'year_published' => $bookData['year_published'],
            'publisher' => $bookData['publisher'],
            'synopsis' => $bookData['synopsis'],
            'book_count' => $bookData['book_count'],
            'source' => $bookData['source'],
            'bookshelf' => $bookData['bookshelf'],
            'type' => $bookData['type'],
            'price' => $bookData['price'],
        ]);

        $this->command->info("   ✅ Buku ditambahkan: {$bookData['title']} ({$bookData['category']})");
    }

    /**
     * Log API failure
     */
    private function logApiFailure(string $category, string $error): void
    {
        $logMessage = date('Y-m-d H:i:s')." - Category: {$category} - Error: {$error}\n";
        Storage::disk('local')->append('api_failures.log', $logMessage);
    }

    /**
     * Clean title from unwanted characters
     */
    private function cleanTitle(string $title): string
    {
        return html_entity_decode(strip_tags($title), ENT_QUOTES, 'UTF-8');
    }

    /**
     * Clean authors string
     */
    private function cleanAuthors(string $authors): string
    {
        return html_entity_decode(strip_tags($authors), ENT_QUOTES, 'UTF-8');
    }

    /**
     * Clean description
     */
    private function cleanDescription(string $description): string
    {
        $cleaned = html_entity_decode(strip_tags($description), ENT_QUOTES, 'UTF-8');

        return strlen($cleaned) > 500 ? substr($cleaned, 0, 497).'...' : $cleaned;
    }
}
