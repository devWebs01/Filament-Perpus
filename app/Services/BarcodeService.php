<?php

namespace App\Services;

use Illuminate\Support\Facades\DB;

/**
 * BarcodeService
 *
 * Service untuk menghasilkan barcode unik yang statis untuk entitas
 * User dan Book. Barcode yang dihasilkan bersifat immutable
 * (tidak berubah setelah dibuat).
 */
class BarcodeService
{
    /**
     * Prefix untuk berbagai tipe entitas
     */
    private const PREFIX_USER = 'USR_';

    private const PREFIX_BOOK = 'BOK_';

    /**
     * Panjang barcode selain prefix
     */
    private const BARCODE_LENGTH = 12;

    /**
     * Maximum retry attempts untuk generate unik barcode
     */
    private const MAX_RETRY = 10;

    /**
     * Generate barcode unik untuk UserDetail
     *
     * Format: USR_ + 12 karakter unik (total 16 karakter)
     * Contoh: USR_A3F7B2D9E4C1
     *
     * @param  int|null  $userId  ID user untuk referensi
     * @return string Barcode unik 16 karakter
     */
    public function generateUserBarcode(?int $userId = null): string
    {
        return $this->generateUniqueBarcode(
            prefix: self::PREFIX_USER,
            table: 'user_details',
            column: 'qr_code',
            context: $userId
        );
    }

    /**
     * Generate barcode unik untuk Book
     *
     * Format: BOK_ + 12 karakter unik (total 16 karakter)
     * Contoh: BOK_X9K4M2P7N3L8
     *
     * @param  int|null  $bookId  ID buku untuk referensi
     * @return string Barcode unik 16 karakter
     */
    public function generateBookBarcode(?int $bookId = null): string
    {
        return $this->generateUniqueBarcode(
            prefix: self::PREFIX_BOOK,
            table: 'books',
            column: 'barcode',
            context: $bookId
        );
    }

    /**
     * Generate barcode unik dengan validasi duplikasi
     *
     * Algoritma:
     * 1. Generate barcode dari kombinasi timestamp + random + context
     * 2. Cek duplikasi di database
     * 3. Jika duplikat, retry dengan tambahan random factor
     * 4. Ulangi sampai MAX_RETRY atau temukan barcode unik
     *
     * @param  string  $prefix  Prefix barcode (USR_/BOK_)
     * @param  string  $table  Nama tabel untuk cek duplikasi
     * @param  string  $column  Nama kolom untuk cek duplikasi
     * @param  mixed  $context  Data kontekstual untuk unikitas (ID, dll)
     * @return string Barcode unik
     *
     * @throws \RuntimeException Jika gagal generate barcode unik setelah MAX_RETRY
     */
    protected function generateUniqueBarcode(
        string $prefix,
        string $table,
        string $column,
        mixed $context = null
    ): string {
        $attempt = 0;

        while ($attempt < self::MAX_RETRY) {
            // Generate barcode base
            $barcode = $this->generateBarcodeBase($prefix, $context, $attempt);

            // Cek duplikasi di database dengan lock untuk race condition prevention
            $exists = DB::table($table)
                ->where($column, $barcode)
                ->lockForUpdate()
                ->exists();

            if (! $exists) {
                return $barcode;
            }

            $attempt++;
        }

        // Jika setelah MAX_RETRY masih duplikat, throw exception
        throw new \RuntimeException(
            'Failed to generate unique barcode after '.self::MAX_RETRY.' attempts. '.
            "Table: {$table}, Column: {$column}"
        );
    }

    /**
     * Generate base barcode dengan kombinasi timestamp, random, dan context
     *
     * Algoritma encoding:
     * - Timestamp mikrosekund terakhir untuk temporal uniqueness
     * - Random bytes untuk randomness
     * - Context ID untuk entity-specific uniqueness
     * - Base32 encoding untuk character set yang aman (hanya huruf dan angka)
     *
     * @param  string  $prefix  Prefix barcode
     * @param  mixed  $context  Data kontekstual
     * @param  int  $attempt  Nomor percobaan (untuk variation)
     * @return string Barcode lengkap dengan prefix
     */
    protected function generateBarcodeBase(string $prefix, mixed $context, int $attempt = 0): string
    {
        // Ambil microsecond time untuk temporal uniqueness
        $microtime = (int) (microtime(true) * 1000000);

        // Generate random bytes
        $randomBytes = random_bytes(4);

        // Combine context jika ada
        $contextHash = $context !== null
            ? hash('crc32', (string) $context, true)
            : '';

        // Combine semua components
        $combined = pack('J', $microtime).     // 8 bytes timestamp
            $randomBytes.               // 4 bytes random
            $contextHash.               // 4 bytes context (jika ada)
            pack('C', $attempt);         // 1 byte attempt number

        // Hash dan encode ke base32 untuk karakter yang aman
        $hash = hash('sha256', $combined, true);

        // Convert ke base32 (hanya huruf dan angka, mudah dibaca)
        $base32 = $this->base32Encode($hash);

        // Ambil sebagian karakter yang dibutuhkan
        $uniquePart = strtoupper(substr($base32, 0, self::BARCODE_LENGTH));

        return $prefix.$uniquePart;
    }

    /**
     * Base32 encoding menggunakan karakter yang mudah dibaca
     * (tanpa confusing characters seperti 0O, 1Il)
     *
     * Charset: A-Z, 2-7 (standard RFC 4648)
     *
     * @param  string  $data  Binary data
     * @return string Base32 encoded string
     */
    protected function base32Encode(string $data): string
    {
        // Base32 alphabet - standard RFC 4648
        $alphabet = 'ABCDEFGHIJKLMNOPQRSTUVWXYZ234567'; // 32 chars
        $output = '';
        $bits = 0;
        $value = 0;
        $len = strlen($data);

        for ($i = 0; $i < $len; $i++) {
            $value = ($value << 8) | ord($data[$i]);
            $bits += 8;

            while ($bits >= 5) {
                $bits -= 5;
                $index = ($value >> $bits) & 31;
                $output .= $alphabet[$index];
            }
        }

        // Handle remaining bits
        if ($bits > 0) {
            $index = ($value << (5 - $bits)) & 31;
            $output .= $alphabet[$index];
        }

        return $output;
    }

    /**
     * Validasi format barcode
     *
     * @param  string  $barcode  Barcode yang ingin divalidasi
     * @param  string  $type  Tipe barcode ('user' atau 'book')
     * @return bool True jika format valid
     */
    public function validateBarcode(string $barcode, string $type): bool
    {
        $prefix = match ($type) {
            'user' => self::PREFIX_USER,
            'book' => self::PREFIX_BOOK,
            default => null,
        };

        if ($prefix === null) {
            return false;
        }

        $expectedLength = strlen($prefix) + self::BARCODE_LENGTH;

        // Cek panjang dan prefix
        if (strlen($barcode) !== $expectedLength) {
            return false;
        }

        if (! str_starts_with($barcode, $prefix)) {
            return false;
        }

        // Cek karakter (hanya huruf kapital dan angka)
        $content = substr($barcode, strlen($prefix));

        return ctype_alnum($content) && strtoupper($content) === $content;
    }

    /**
     * Generate barcode dalam bulk untuk testing/migration
     *
     * @param  string  $type  Tipe barcode ('user' atau 'book')
     * @param  int  $count  Jumlah barcode yang ingin digenerate
     * @return array<string> List barcode yang di-generate
     */
    public function generateBulk(string $type, int $count): array
    {
        $barcodes = [];

        for ($i = 0; $i < $count; $i++) {
            $barcode = match ($type) {
                'user' => $this->generateUserBarcode(),
                'book' => $this->generateBookBarcode(),
                default => throw new \InvalidArgumentException("Invalid type: {$type}"),
            };

            $barcodes[] = $barcode;
        }

        return $barcodes;
    }

    /**
     * Extract informasi dari barcode
     *
     * @param  string  $barcode  Barcode yang ingin di-decode
     * @return array{type: string, prefix: string, code: string}
     */
    public function parseBarcode(string $barcode): array
    {
        return [
            'type' => match (true) {
                str_starts_with($barcode, self::PREFIX_USER) => 'user',
                str_starts_with($barcode, self::PREFIX_BOOK) => 'book',
                default => 'unknown',
            },
            'prefix' => substr($barcode, 0, 4),
            'code' => substr($barcode, 4),
        ];
    }
}
