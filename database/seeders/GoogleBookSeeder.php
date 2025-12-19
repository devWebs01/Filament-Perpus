<?php

namespace Database\Seeders;

use App\Models\Book;
use App\Models\Category;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class GoogleBookSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Daftar kategori dalam bahasa Indonesia
        $categories = [
            'Matematika SMK',
            'Bahasa Inggris SMK',
            'Sejarah SMK',
            'Pelajaran SMK',
            'Teknik Komputer & Jaringan',
            'Akuntansi SMK',
            'Tata Boga SMK',
            'Desain Grafis SMK',
        ];

        // Pastikan folder storage/public/books ada
        if (! Storage::disk('public')->exists('books')) {
            Storage::disk('public')->makeDirectory('books');
        }

        foreach ($categories as $categoryName) {

            // Buat atau ambil kategori sekali saja
            $category = Category::firstOrCreate(
                ['slug' => Str::slug($categoryName)],
                ['name' => $categoryName]
            );

            $keyword = urlencode($categoryName);

            // Ambil data dari Google Books API (timeout + retry)
            $response = Http::timeout(10)->retry(2, 500)
                ->get("https://www.googleapis.com/books/v1/volumes?q={$keyword}&maxResults=10");

            $this->command->info('   ✅ Mengambil Buku Kategori: '.$category->name);

            if ($response->failed()) {
                Log::error("Gagal mengambil data dari Google Books API untuk kategori: {$keyword}");

                continue;
            }

            $books = $response->json('items', []);
            $books = array_slice($books, 0, 5); // ambil maksimal 5 buku

            foreach ($books as $item) {
                $volumeInfo = $item['volumeInfo'] ?? [];

                // Ambil data buku yang diperlukan (tahan terhadap variasi)
                $title = $volumeInfo['title'] ?? null;
                $imageUrl = $volumeInfo['imageLinks']['thumbnail'] ?? $volumeInfo['imageLinks']['smallThumbnail'] ?? null;
                // pastikan https untuk thumbnail
                if ($imageUrl) {
                    $imageUrl = preg_replace('/^http:/i', 'https:', $imageUrl);
                }

                // Ambil ISBN (prioritaskan ISBN_13 -> ISBN_10 -> fallback)
                $isbn = null;
                if (! empty($volumeInfo['industryIdentifiers'])) {
                    foreach ($volumeInfo['industryIdentifiers'] as $iden) {
                        if (isset($iden['type']) && $iden['type'] === 'ISBN_13') {
                            $isbn = $iden['identifier'];
                            break;
                        }
                    }
                    if (! $isbn) {
                        foreach ($volumeInfo['industryIdentifiers'] as $iden) {
                            if (isset($iden['type']) && $iden['type'] === 'ISBN_10') {
                                $isbn = $iden['identifier'];
                                break;
                            }
                        }
                    }
                    if (! $isbn) {
                        $isbn = $volumeInfo['industryIdentifiers'][0]['identifier'] ?? null;
                    }
                }

                $author = isset($volumeInfo['authors']) ? implode(', ', $volumeInfo['authors']) : null;

                // Parsing tahun publikasi (bisa berupa "YYYY" atau "YYYY-MM-DD")
                $publishedRaw = $volumeInfo['publishedDate'] ?? null;
                $yearPublished = null;
                if ($publishedRaw && preg_match('/\d{4}/', $publishedRaw, $m)) {
                    $yearPublished = (int) $m[0];
                }

                $publisher = $volumeInfo['publisher'] ?? null;
                $synopsis = $volumeInfo['description'] ?? 'Tidak ada sinopsis.';
                $price = rand(50000, 300000); // harga acak jika tak ada
                $bookCount = rand(1, 10);

                // Validasi minimal: title & author (lepas syarat ISBN agar tidak banyak ter-skip)
                if (! $title || ! $author) {
                    Log::warning('Skipping book karena missing title/author: '.json_encode($volumeInfo));

                    continue;
                }

                // Siapkan path gambar (disimpan di disk 'public' -> storage/app/public/books/...)
                $imagePath = null;
                if ($imageUrl) {
                    try {
                        // nama file lebih aman: slug(title) + uniqid
                        $safeBase = Str::slug(Str::limit($title, 60));
                        $imageName = "book_{$safeBase}_".uniqid().'.jpg';
                        $candidatePath = "books/{$imageName}";

                        $imgResp = Http::timeout(10)->retry(2, 500)->get($imageUrl);
                        if ($imgResp->successful() && strlen($imgResp->body()) > 200) {
                            Storage::disk('public')->put($candidatePath, $imgResp->body());
                            $imagePath = $candidatePath; // simpan 'books/xxxx.jpg'
                        } else {
                            Log::warning("Gagal unduh image (kosong/kecil) untuk: {$title} - URL: {$imageUrl}");
                        }
                    } catch (\Exception $e) {
                        Log::warning("Error download image untuk {$title}: ".$e->getMessage());
                        // biarkan $imagePath null -> dapat ditangani di accessor/view
                    }
                }

                // Simpan buku ke DB
                try {
                    $bookModel = Book::create([
                        'title' => $title,
                        'image' => $imagePath, // null atau 'books/xxx.jpg'
                        'category_id' => $category->id,
                        'isbn' => $isbn ?? uniqid('isbn_'),
                        'author' => $author,
                        'year_published' => $yearPublished,
                        'publisher' => $publisher,
                        'synopsis' => $synopsis,
                        'book_count' => $bookCount,
                        'source' => 'Google Books',
                        'bookshelf' => 'Rak 1',
                        'type' => 'other',
                        'price' => $price,
                    ]);

                    $this->command->info('   ✅ Buku ditambahkan: '.$bookModel->title.' - Kategori: '.$category->name);
                } catch (\Exception $e) {
                    Log::error("Gagal menyimpan buku: {$title}. Error: ".$e->getMessage());

                    continue;
                }
            }
        }
    }
}
