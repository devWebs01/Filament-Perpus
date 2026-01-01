<?php

namespace App\Services;

use Illuminate\Support\Facades\Log;
use Milon\Barcode\DNS1D;

/**
 * BarcodeImageService
 *
 * Service untuk generate barcode sebagai gambar dan menyimpan path-nya
 * langsung ke field barcode (Books) atau qr_code (UserDetail) dalam format JSON.
 *
 * Format penyimpanan di database:
 * - Books.barcode: {"code": "BOK_44SVZVB2T36X", "image": "barcodes/book_1_BOK_44SVZVB2T36X.png"}
 * - UserDetails.qr_code: {"code": "USR_A3F7B2D9E4C1", "image": "barcodes/user_1_USR_A3F7B2D9E4C1.png"}
 */
class BarcodeService
{
    /**
     * Direktori penyimpanan barcode
     */
    private const BARCODE_DIRECTORY = 'barcodes';

    /**
     * Format gambar barcode
     */
    private const IMAGE_FORMAT = 'png';

    /**
     * Generate barcode image dan return sebagai array untuk disimpan JSON
     *
     * @param  string  $barcodeCode  Kode barcode (contoh: BOK_44SVZVB2T36X)
     * @param  string|null  $filename  Nama file tanpa extension (jika null gunakan barcode code)
     * @return array{code: string, image: string} Data untuk disimpan ke database JSON
     *
     * @throws \Exception Jika gagal generate atau save barcode
     */
    public function generateAndSave(string $barcodeCode, ?string $filename = null): array
    {
        try {
            // Validasi barcode code
            if (empty($barcodeCode)) {
                throw new \InvalidArgumentException('Barcode code tidak boleh kosong');
            }

            // Gunakan barcode code sebagai filename jika tidak diberikan
            $filename = $filename ?? $barcodeCode;

            // Sanitize filename
            $filename = preg_replace('/[^a-zA-Z0-9_-]/', '_', $filename);

            // Path relatif untuk storage public
            $relativePath = self::BARCODE_DIRECTORY.'/'.$filename.'.'.self::IMAGE_FORMAT;
            $fullPath = storage_path('app/public/'.$relativePath);

            // Pastikan direktori ada
            $directory = dirname($fullPath);
            if (! is_dir($directory)) {
                @mkdir($directory, 0755, true);
            }

            // Generate barcode image menggunakan DNS1D dengan pendekatan yang lebih stabil
            $dns1d = new DNS1D;

            // Set storage path untuk menghindari masalah path
            $dns1d->setStorPath(storage_path('app/public/barcodes/'));

            // Gunakan getBarcodePNGPath untuk menghindari infinite loop
            $barcodeImagePath = $dns1d->getBarcodePNGPath(
                $barcodeCode,
                'C128',  // Barcode type (Code128)
                2,       // Scale
                25,      // Height
                [0, 0, 0],  // RGB color (black)
                false    // Show text (false untuk menghindari masalah)
            );

            // Jika path dihasilkan, salin file ke lokasi yang diinginkan
            if ($barcodeImagePath) {
                $sourcePath = storage_path('app/public/barcodes/'.basename($barcodeImagePath));

                if (file_exists($sourcePath)) {
                    // Salin file ke lokasi target
                    copy($sourcePath, $fullPath);
                    // Hapus file sementara
                    unlink($sourcePath);

                    $barcodeImage = file_get_contents($fullPath);
                } else {
                    // Fallback ke PNG base64
                    $barcodeImage = $dns1d->getBarcodePNG(
                        $barcodeCode,
                        'C128',
                        2,
                        25,
                        [0, 0, 0],
                        false
                    );
                }
            } else {
                // Fallback ke PNG base64
                $barcodeImage = $dns1d->getBarcodePNG(
                    $barcodeCode,
                    'C128',
                    2,
                    25,
                    [0, 0, 0],
                    false
                );
            }

            // Simpan file gambar
            file_put_contents($fullPath, $barcodeImage);

            Log::info('Barcode image generated successfully', [
                'code' => $barcodeCode,
                'filename' => $filename,
                'path' => $relativePath,
            ]);

            // Return array untuk disimpan sebagai JSON
            return [
                'code' => $barcodeCode,
                'image' => $relativePath,  // Path relatif untuk storage
            ];
        } catch (\Exception $e) {
            Log::error('Failed to generate barcode image', [
                'code' => $barcodeCode,
                'error' => $e->getMessage(),
            ]);

            throw $e;
        }
    }

    /**
     * Generate barcode image untuk Book
     * Data akan disimpan ke field Books.barcode sebagai JSON
     *
     * @param  int  $bookId  ID book
     * @param  string  $barcodeCode  Kode barcode
     * @return array{code: string, image: string}
     *
     * @throws \Exception
     */
    public function generateBookBarcode(int $bookId, string $barcodeCode): array
    {
        $filename = 'book_'.$bookId.'_'.$barcodeCode;

        return $this->generateAndSave($barcodeCode, $filename);
    }

    /**
     * Generate QR Code untuk User
     * Data akan disimpan ke field UserDetails.qr_code sebagai JSON
     *
     * @param  int  $userId  ID user
     * @param  string  $qrcodeCode  Kode QR (dari UserDetail.qr_code)
     * @return array{code: string, image: string}
     *
     * @throws \Exception
     */
    public function generateUserQRCode(int $userId, string $qrcodeCode): array
    {
        $filename = 'user_'.$userId.'_'.$qrcodeCode;

        // Generate sebagai QRCODE type
        try {
            if (empty($qrcodeCode)) {
                throw new \InvalidArgumentException('QR code tidak boleh kosong');
            }

            $filename = preg_replace('/[^a-zA-Z0-9_-]/', '_', $filename);
            $relativePath = self::BARCODE_DIRECTORY.'/'.$filename.'.'.self::IMAGE_FORMAT;
            $fullPath = storage_path('app/public/'.$relativePath);

            $directory = dirname($fullPath);
            if (! is_dir($directory)) {
                @mkdir($directory, 0755, true);
            }

            $dns1d = new DNS1D;
            // Set storage path untuk menghindari masalah path
            $dns1d->setStorPath(storage_path('app/public/barcodes/'));

            // Gunakan getBarcodePNGPath untuk QR Code
            $qrImagePath = $dns1d->getBarcodePNGPath(
                $qrcodeCode,
                'QRCODE',  // QR Code type
                2,
                2
            );

            if ($qrImagePath) {
                $sourcePath = storage_path('app/public/barcodes/'.basename($qrImagePath));

                if (file_exists($sourcePath)) {
                    // Salin file ke lokasi target
                    copy($sourcePath, $fullPath);
                    // Hapus file sementara
                    unlink($sourcePath);
                } else {
                    // Fallback ke base64
                    $qrImage = $dns1d->getBarcodePNG($qrcodeCode, 'QRCODE', 2, 2);
                    file_put_contents($fullPath, $qrImage);
                }
            } else {
                // Fallback ke base64
                $qrImage = $dns1d->getBarcodePNG($qrcodeCode, 'QRCODE', 2, 2);
                file_put_contents($fullPath, $qrImage);
            }

            Log::info('QR code generated successfully', [
                'code' => $qrcodeCode,
                'path' => $relativePath,
            ]);

            return [
                'code' => $qrcodeCode,
                'image' => $relativePath,
            ];
        } catch (\Exception $e) {
            Log::error('Failed to generate QR code', [
                'code' => $qrcodeCode,
                'error' => $e->getMessage(),
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

            $fullPath = storage_path('app/public/'.$imagePath);

            if (file_exists($fullPath)) {
                unlink($fullPath);

                Log::info('Barcode image deleted', ['path' => $imagePath]);

                return true;
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

        $fullPath = storage_path('app/public/'.$imagePath);

        return file_exists($fullPath);
    }

    /**
     * Get barcode/QR image URL dari path
     *
     * @param  string  $imagePath  Path relatif gambar
     * @return string URL publik
     */
    public function getBarcodeUrl(string $imagePath): string
    {
        if (empty($imagePath)) {
            return '';
        }

        return asset('storage/'.$imagePath);
    }

    /**
     * Parse barcode/QR data dari JSON
     * Helper untuk extract code dan image dari field barcode/qr_code
     *
     * @param  array|string|null  $data  Data dari field barcode/qr_code
     * @return array{code: string|null, image: string|null}
     */
    public static function parseBarcode($data): array
    {
        // Jika null atau empty string
        if (empty($data)) {
            return ['code' => null, 'image' => null];
        }

        // Jika string JSON
        if (is_string($data)) {
            $decoded = json_decode($data, true);
            if (is_array($decoded)) {
                return [
                    'code' => $decoded['code'] ?? null,
                    'image' => $decoded['image'] ?? null,
                ];
            }
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
     * @param  string  $barcodeCode  Kode barcode baru
     * @param  string|null  $filename  Nama file baru
     * @return array{code: string, image: string}
     *
     * @throws \Exception
     */
    public function regenerate(?string $oldImagePath, string $barcodeCode, ?string $filename = null): array
    {
        // Hapus barcode image lama jika ada
        if (! empty($oldImagePath)) {
            $this->deleteBarcode($oldImagePath);
        }

        // Generate yang baru
        return $this->generateAndSave($barcodeCode, $filename);
    }
}
