<?php

namespace App\Services;

use Illuminate\Support\Facades\Log;
use Milon\Barcode\DNS2D;

/**
 * BarcodeService
 *
 * Service untuk generate QR code untuk books dan users.
 * - Book QR: menyimpan path gambar ke field Books.barcode
 * - User QR: menyimpan path gambar ke field UserDetails.barcode
 */
class BarcodeService
{
    private const QRCODE_DIRECTORY = 'qrcodes';

    private const USER_QRCODE_DIRECTORY = 'user_barcode';

    private const BOOK_QRCODE_DIRECTORY = 'book_barcode';

    /**
     * Generate QR Code image untuk Book
     * Path gambar akan langsung disimpan ke field Books.barcode_image
     *
     * @param  int  $bookId  ID book
     * @param  string  $barcodeCode  Kode QR
     * @return string Path relatif gambar
     *
     * @throws \Exception
     */
    public function generateBookBarcode(int $bookId, string $barcodeCode): string
    {
        try {
            if (empty($barcodeCode)) {
                throw new \InvalidArgumentException('QR code tidak boleh kosong');
            }

            $filename = "book_{$bookId}_{$barcodeCode}.png";
            $relativePath = self::BOOK_QRCODE_DIRECTORY.'/'.$filename;
            $fullPath = storage_path('app/public/'.$relativePath);

            // Pastikan direktori ada
            $directory = dirname($fullPath);
            if (! is_dir($directory)) {
                @mkdir($directory, 0755, true);
            }

            // Generate QR Code image
            $dns2d = new DNS2D;

            // getBarcodePNG return base64 string, perlu di-decode untuk mendapatkan binary
            $base64Image = $dns2d->getBarcodePNG($barcodeCode, 'QRCODE', 3, 3);

            if ($base64Image === false) {
                throw new \Exception('Failed to generate QR code image');
            }

            // Decode base64 ke binary image
            $image = base64_decode($base64Image, true);

            if ($image === false) {
                throw new \Exception('Failed to decode QR code image');
            }

            // Simpan file gambar sebagai binary
            $bytes = file_put_contents($fullPath, $image);

            if ($bytes === false) {
                throw new \Exception('Failed to write QR code image to storage');
            }

            Log::info('Book QR code generated successfully', [
                'book_id' => $bookId,
                'code' => $barcodeCode,
                'path' => $relativePath,
                'file_size' => $bytes,
            ]);

            return $relativePath;
        } catch (\Exception $e) {
            Log::error('Failed to generate book QR code', [
                'book_id' => $bookId,
                'code' => $barcodeCode,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);

            throw $e;
        }
    }

    /**
     * Generate QR Code untuk Book (dipanggil oleh BookObserver)
     * Generate kode dan gambar QR code, mengembalikan array dengan code dan image_path
     *
     * @param  int  $bookId  ID book
     * @return array{code: string, image_path: string}
     *
     * @throws \Exception
     */
    public function generateBookBarcodeWithCode(int $bookId): array
    {
        try {
            // Generate unique barcode code dengan format BOK_ + 12 UPPERCASE alphanumeric
            // Format konsisten dengan user barcode: LIB_USER_XXXXXXXXXXXX
            $barcodeCode = 'BOK_'.strtoupper(substr(md5($bookId.microtime()), 0, 12));

            // Generate QR image sebagai file
            $imagePath = $this->generateBookBarcode($bookId, $barcodeCode);

            Log::info('Book barcode generated', [
                'book_id' => $bookId,
                'code' => $barcodeCode,
                'path' => $imagePath,
            ]);

            // Return array dengan code dan image_path terpisah
            return [
                'code' => $barcodeCode,
                'image_path' => $imagePath,
            ];
        } catch (\Exception $e) {
            Log::error('Failed to generate book barcode', [
                'book_id' => $bookId,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);

            throw $e;
        }
    }

    /**
     * Generate QR Code untuk User
     * Kembalikan base64 data URL untuk digunakan langsung di <img src="">
     *
     * @param  int  $userId  ID user
     * @param  string  $qrcodeCode  Kode QR
     * @return string Base64 data URL format: "data:image/png;base64,..."
     *
     * @throws \Exception
     */
    public function generateUserQRCode(int $userId, string $qrcodeCode): string
    {
        try {
            if (empty($qrcodeCode)) {
                throw new \InvalidArgumentException('QR code tidak boleh kosong');
            }

            // Generate QR Code sebagai base64 string
            $dns2d = new DNS2D;
            $base64Image = $dns2d->getBarcodePNG($qrcodeCode, 'QRCODE', 3, 3);

            if ($base64Image === false) {
                throw new \Exception('Failed to generate QR code image');
            }

            Log::info('User QR code generated successfully', [
                'user_id' => $userId,
                'code' => $qrcodeCode,
            ]);

            // Kembalikan dalam format data URL
            return 'data:image/png;base64,'.$base64Image;
        } catch (\Exception $e) {
            Log::error('Failed to generate user QR code', [
                'user_id' => $userId,
                'code' => $qrcodeCode,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);

            throw $e;
        }
    }

    /**
     * Hapus barcode/QR image dari storage
     *
     * @param  string  $imagePath  Path relatif gambar
     * @return bool True jika berhasil dihapus
     */
    public function deleteBarcode(string $imagePath): bool
    {
        try {
            if (empty($imagePath)) {
                return false;
            }

            // Skip jika format data URL (base64)
            if (str_starts_with($imagePath, 'data:')) {
                return true;
            }

            $fullPath = storage_path('app/public/'.$imagePath);

            if (file_exists($fullPath)) {
                $deleted = unlink($fullPath);

                if ($deleted) {
                    Log::info('Barcode image deleted', ['path' => $imagePath]);
                }

                return $deleted;
            }

            return false;
        } catch (\Exception $e) {
            Log::error('Failed to delete barcode image', [
                'path' => $imagePath,
                'error' => $e->getMessage(),
            ]);

            return false;
        }
    }

    /**
     * Cek apakah barcode image ada di storage
     *
     * @param  string  $imagePath  Path relatif gambar
     */
    public function barcodeExists(string $imagePath): bool
    {
        if (empty($imagePath)) {
            return false;
        }

        // Data URL (base64) selalu dianggap ada
        if (str_starts_with($imagePath, 'data:')) {
            return true;
        }

        return file_exists(storage_path('app/public/'.$imagePath));
    }

    /**
     * Get barcode/QR image URL dari path
     *
     * @param  string  $imagePath  Path relatif gambar atau base64 data URL
     * @return string URL publik atau data URL
     */
    public function getBarcodeUrl(string $imagePath): string
    {
        if (empty($imagePath)) {
            return '';
        }

        // Jika base64 data URL, return langsung
        if (str_starts_with($imagePath, 'data:')) {
            return $imagePath;
        }

        // Jika path file, return asset URL
        return asset('storage/'.$imagePath);
    }

    /**
     * Helper untuk parse barcode data (JSON atau path)
     *
     * @param  array|string|null  $data  Data dari field barcode/barcode
     * @return array{code: string|null, image: string|null}
     */
    public static function parseBarcode($data): array
    {
        if (empty($data)) {
            return ['code' => null, 'image' => null];
        }

        // Jika string
        if (is_string($data)) {
            // Coba decode sebagai JSON
            $decoded = json_decode($data, true);
            if (is_array($decoded)) {
                return [
                    'code' => $decoded['code'] ?? null,
                    'image' => $decoded['image'] ?? null,
                ];
            }

            // Jika bukan JSON, assume sebagai image path atau data URL
            return ['code' => null, 'image' => $data];
        }

        // Jika sudah array
        if (is_array($data)) {
            return [
                'code' => $data['code'] ?? null,
                'image' => $data['image'] ?? null,
            ];
        }

        return ['code' => null, 'image' => null];
    }

    /**
     * Regenerate barcode image (hapus old, buat baru)
     *
     * @param  string|null  $oldImagePath  Path gambar lama
     * @param  int  $bookId  ID buku
     * @param  string  $barcodeCode  Kode barcode baru
     * @return string Path gambar baru
     *
     * @throws \Exception
     */
    public function regenerateBookBarcode(?string $oldImagePath, int $bookId, string $barcodeCode): string
    {
        // Hapus barcode image lama jika ada
        if (! empty($oldImagePath)) {
            $this->deleteBarcode($oldImagePath);
        }

        // Generate yang baru
        return $this->generateBookBarcode($bookId, $barcodeCode);
    }

    /**
     * Generate QR Code untuk User (dipanggil oleh UserDetailObserver)
     * Generate kode dan gambar QR code, mengembalikan array dengan code dan image_path
     *
     * @param  int|null  $userId  ID user
     * @return array{code: string, image_path: string}
     *
     * @throws \Exception
     */
    public function generateUserBarcode(?int $userId): array
    {
        try {
            if ($userId === null) {
                throw new \InvalidArgumentException('User ID tidak boleh null');
            }

            // Generate unique QR code dengan format LIB_USER_XXXXXXXXXXXX
            $qrCodeString = 'LIB_USER_'.strtoupper(substr(md5($userId.microtime()), 0, 12));

            // Generate QR image sebagai file
            $imagePath = $this->generateUserQRCodeAsFile($userId, $qrCodeString);

            Log::info('User barcode generated', [
                'user_id' => $userId,
                'code' => $qrCodeString,
                'path' => $imagePath,
            ]);

            // Return array dengan code dan image_path terpisah
            return [
                'code' => $qrCodeString,
                'image_path' => $imagePath,
            ];
        } catch (\Exception $e) {
            Log::error('Failed to generate user barcode', [
                'user_id' => $userId,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);

            throw $e;
        }
    }

    /**
     * Generate QR Code image untuk User sebagai file
     *
     * @param  int  $userId  ID user
     * @param  string  $qrcodeCode  Kode QR
     * @return string Path relatif gambar
     *
     * @throws \Exception
     */
    public function generateUserQRCodeAsFile(int $userId, string $qrcodeCode): string
    {
        try {
            if (empty($qrcodeCode)) {
                throw new \InvalidArgumentException('QR code tidak boleh kosong');
            }

            $filename = "user_{$userId}_{$qrcodeCode}.png";
            $relativePath = self::USER_QRCODE_DIRECTORY.'/'.$filename;
            $fullPath = storage_path('app/public/'.$relativePath);

            // Pastikan direktori ada
            $directory = dirname($fullPath);
            if (! is_dir($directory)) {
                @mkdir($directory, 0755, true);
            }

            // Generate QR Code image
            $dns2d = new DNS2D;
            $base64Image = $dns2d->getBarcodePNG($qrcodeCode, 'QRCODE', 3, 3);

            if ($base64Image === false) {
                throw new \Exception('Failed to generate QR code image');
            }

            // Decode base64 ke binary image
            $image = base64_decode($base64Image, true);

            if ($image === false) {
                throw new \Exception('Failed to decode QR code image');
            }

            // Simpan file gambar sebagai binary
            $bytes = file_put_contents($fullPath, $image);

            if ($bytes === false) {
                throw new \Exception('Failed to write QR code image to storage');
            }

            Log::info('User QR code image saved as file', [
                'user_id' => $userId,
                'code' => $qrcodeCode,
                'path' => $relativePath,
                'file_size' => $bytes,
            ]);

            return $relativePath;
        } catch (\Exception $e) {
            Log::error('Failed to generate user QR code image', [
                'user_id' => $userId,
                'code' => $qrcodeCode,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);

            throw $e;
        }
    }

    /**
     * Regenerate QR code untuk user sebagai file
     *
     * @param  int  $userId  ID user
     * @param  string  $qrcodeCode  Kode QR baru
     * @return string Path relatif gambar
     *
     * @throws \Exception
     */
    public function regenerateUserQRCode(int $userId, string $qrcodeCode): string
    {
        return $this->generateUserQRCodeAsFile($userId, $qrcodeCode);
    }
}
