<?php

namespace Database\Seeders;

use App\Models\Book;
use App\Models\Category;
use Illuminate\Database\Seeder;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
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

    private function useOfflineMode(): bool
    {
        return app()->environment('testing') ||
            config('app.disable_external_apis', false) ||
            $this->hasPreviousApiFailures();
    }

    private function hasPreviousApiFailures(): bool
    {
        return Storage::disk('local')->exists('api_failures.log');
    }

    /**
     * Helper: terjemahkan teks ke Bahasa Indonesia dengan cache & fallback
     */
    private function translateToId(?string $text): ?string
    {
        if (! $text) {
            return $text;
        }

        // gunakan hash sebagai key cache supaya unik terhadap isi
        $cacheKey = 'translate:id:'.md5($text);

        return Cache::remember($cacheKey, now()->addDays(7), function () use ($text) {
            try {
                // target 'id' (Bahasa Indonesia). GoogleTranslate mendeteksi source otomatis.
                $tr = new GoogleTranslate('id');
                // trim & safe
                $result = trim($tr->translate($text));

                // jika kosong, fallback ke original
                return $result !== '' ? $result : $text;
            } catch (\Exception $e) {
                Log::warning('Translate failed: '.$e->getMessage());

                // fallback ke teks asli agar seeder tetap berjalan
                return $text;
            }
        });
    }

    private function seedOnlineBooks(): void
    {
        $limitPerCategory = 5;
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
                $booksResponse = Http::timeout(10)
                    ->retry(2, 1000)
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
                $this->logApiFailure($categoryNameInIndonesian, $e->getMessage());
            }
        }

        if ($totalFailures > $totalSuccess) {
            $this->command->warn('⚠️  Terlalu banyak kegagalan API, beralih ke mode offline...');
            $this->seedOfflineBooks();

            return;
        }

        $this->command->info("✅ BookSeeder online mode selesai: {$totalSuccess} berhasil, {$totalFailures} gagal");
    }

    private function processBookFromApi(array $book, string $categoryName): bool
    {
        try {
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

            $category = Category::firstOrCreate([
                'name' => $categoryName,
                'slug' => Str::slug($categoryName),
            ]);

            // Generate image name & full path (consistent)
            $imageName = 'book_'.Str::slug($bookDetails['title']).'.jpg';
            $imagePath = "books/{$imageName}";

            // Ambil dan bersihkan teks asli
            $originalTitle = $this->cleanTitle($bookDetails['title']);
            $originalSynopsis = $this->cleanDescription($bookDetails['description'] ?? '');

            // Terjemahkan dengan cache & fallback
            $translatedTitle = $this->translateToId($originalTitle);
            $translatedSynopsis = $this->translateToId($originalSynopsis);

            // Simpan data buku ke DB menggunakan $imagePath (bukan hanya filename)
            $bookData = [
                // simpan judul & sinopsis yang sudah diterjemahkan
                'title' => $translatedTitle ?: $originalTitle,
                'image' => $imagePath, // simpan path relatif 'books/...'
                'category_id' => $category->id,
                'isbn' => $bookDetails['id'] ?? uniqid(),
                'author' => $this->cleanAuthors($bookDetails['authors'] ?? 'Unknown Author'),
                'year_published' => $bookDetails['year'] ?? date('Y'),
                'publisher' => $bookDetails['publisher'] ?? 'Unknown Publisher',
                'synopsis' => $translatedSynopsis ?: $originalSynopsis,
                'book_count' => rand(1, 10),
                'source' => 'API External',
                'bookshelf' => 'Rak '.rand(1, 20),
                'type' => Arr::random([
                    'fiction',
                    'non-fiction',
                    'reference',
                    'textbook',
                    'other',
                ]),
                'price' => rand(25000, 150000),
            ];

            $bookModel = Book::create($bookData);

            // PENTING: pass $imagePath (bukan $imageName) agar file disimpan di storage/app/public/books/...
            $this->downloadBookImage($bookDetails['image'], $imagePath, $bookModel->title);

            return true;
        } catch (\Exception $e) {
            $this->command->warn('Gagal memproses buku '.($book['id'] ?? 'unknown').': '.$e->getMessage());

            return false;
        }
    }

    /**
     * Download book image with error handling
     * $storagePath harus berformat 'books/filename.jpg' atau path relatif lain di dalam disk 'public'
     */
    private function downloadBookImage(string $imageUrl, string $storagePath, string $bookTitle): void
    {
        try {
            // pastikan direktori ada (dirname('books/xxx.jpg') => 'books')
            $dir = dirname($storagePath);
            if ($dir !== '.' && ! Storage::disk('public')->exists($dir)) {
                Storage::disk('public')->makeDirectory($dir);
            }

            $imageResponse = Http::timeout(10)->retry(2, 1000)->get($imageUrl);
            if ($imageResponse->successful() && strlen($imageResponse->body()) > 50) {
                Storage::disk('public')->put($storagePath, $imageResponse->body());
                $this->command->info("   ✅ Gambar berhasil diunduh: {$storagePath}");
            } else {
                // Jika gagal ambil gambar, buat placeholder di path yang sama
                $this->createPlaceholderImage($storagePath, $bookTitle);
                $this->command->warn("⚠️  Gagal mengunduh gambar, menggunakan placeholder untuk: {$bookTitle}");
            }
        } catch (\Exception $e) {
            $this->createPlaceholderImage($storagePath, $bookTitle);
            $this->command->warn("⚠️  Error saat mengunduh gambar, menggunakan placeholder untuk: {$bookTitle} - ".$e->getMessage());
        }
    }

    private function createPlaceholderImage(string $path, string $bookTitle): void
    {
        try {
            $image = imagecreate(400, 600);
            $bgColor = imagecolorallocate($image, 240, 240, 240);
            $textColor = imagecolorallocate($image, 100, 100, 100);
            imagefill($image, 0, 0, $bgColor);

            $fontPath = storage_path('fonts/DejaVuSans.ttf');
            if (file_exists($fontPath)) {
                $bbox = imagettfbbox(14, 0, $fontPath, $bookTitle);
                $textWidth = $bbox[2] - $bbox[0];
                $x = (400 - $textWidth) / 2;
                imagettftext($image, 14, 0, $x, 300, $textColor, $fontPath, $bookTitle);
            } else {
                $text = substr($bookTitle, 0, 20).(strlen($bookTitle) > 20 ? '...' : '');
                $textWidth = strlen($text) * 6;
                $x = (400 - $textWidth) / 2;
                imagestring($image, 5, $x, 290, $text, $textColor);
            }

            $label = 'Cover Buku';
            $labelWidth = strlen($label) * 6;
            $x = (400 - $labelWidth) / 2;
            imagestring($image, 4, $x, 320, $label, $textColor);

            $fullPath = storage_path("app/public/{$path}");
            // pastikan direktori file ada
            $dir = dirname($fullPath);
            if (! is_dir($dir)) {
                @mkdir($dir, 0755, true);
            }

            imagejpeg($image, $fullPath, 80);
            imagedestroy($image);
        } catch (\Exception $e) {
            $this->command->error('❌ Gagal membuat placeholder image: '.$e->getMessage());
        }
    }

    private function seedOfflineBooks(): void
    {
        $this->command->info('📚 Menggunakan data buku lokal...');

        if (! Storage::disk('public')->exists('books')) {
            Storage::disk('public')->makeDirectory('books');
        }

        $books = $this->getLocalBookData();

        foreach ($books as $bookData) {
            $this->createLocalBook($bookData);
        }

        $this->command->info('✅ Data buku lokal berhasil dibuat!');
    }

    private function getLocalBookData(): array
    {
        return [
            [
                'title' => 'Algoritma dan Pemrograman',
                'author' => 'Dr. Budi Santoso, M.Kom',
                'isbn' => '978-602-123-456-1',
                'publisher' => 'Penerbit Informatika',
                'year_published' => 2023,
                'synopsis' => 'Buku panduan lengkap...',
                'category' => 'Ilmu Komputer',
                'book_count' => 5,
                'source' => 'Pembelian Langsung',
                'bookshelf' => 'Rak A1',
                'type' => 'textbook',
                'price' => 85000,
            ],
            // ... tambah lainnya ...
        ];
    }

    private function createLocalBook(array $bookData): void
    {
        $category = Category::firstOrCreate([
            'name' => $bookData['category'],
            'slug' => Str::slug($bookData['category']),
        ]);

        $imageName = 'book_'.Str::slug($bookData['title']).'.jpg';
        $imagePath = "books/{$imageName}";

        // Terjemahkan title & synopsis lokal juga
        $titleTranslated = $this->translateToId($bookData['title']);
        $synopsisTranslated = $this->translateToId($bookData['synopsis'] ?? '');

        Book::create([
            'title' => $titleTranslated ?: $bookData['title'],
            'image' => $imagePath,
            'category_id' => $category->id,
            'isbn' => $bookData['isbn'],
            'author' => $bookData['author'],
            'year_published' => $bookData['year_published'],
            'publisher' => $bookData['publisher'],
            'synopsis' => $synopsisTranslated ?: $bookData['synopsis'],
            'book_count' => $bookData['book_count'],
            'source' => $bookData['source'],
            'bookshelf' => $bookData['bookshelf'],
            'type' => $bookData['type'],
            'price' => $bookData['price'],
        ]);

        if (! Storage::disk('public')->exists($imagePath)) {
            $this->createPlaceholderImage($imagePath, $bookData['title']);
            $this->command->info("   ✅ Placeholder gambar dibuat: {$imageName}");
        }

        $this->command->info("   ✅ Buku ditambahkan: {$bookData['title']} ({$bookData['category']})");
    }

    private function logApiFailure(string $category, string $error): void
    {
        $logMessage = date('Y-m-d H:i:s')." - Category: {$category} - Error: {$error}\n";
        Storage::disk('local')->append('api_failures.log', $logMessage);
    }

    private function cleanTitle(string $title): string
    {
        return html_entity_decode(strip_tags($title), ENT_QUOTES, 'UTF-8');
    }

    private function cleanAuthors(string $authors): string
    {
        return html_entity_decode(strip_tags($authors), ENT_QUOTES, 'UTF-8');
    }

    private function cleanDescription(string $description): string
    {
        $cleaned = html_entity_decode(strip_tags($description), ENT_QUOTES, 'UTF-8');

        return strlen($cleaned) > 500 ? substr($cleaned, 0, 497).'...' : $cleaned;
    }
}
