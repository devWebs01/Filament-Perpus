<?php

namespace Database\Seeders;

use App\Models\Status;
use Illuminate\Database\Seeder;

class StatusSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $statuses = [
            [
                'name' => 'Menunggu Persetujuan',
                'amount' => 0,
            ],
            [
                'name' => 'Dipinjam',
                'amount' => 0,
            ],
            [
                'name' => 'Terlambat',
                'amount' => 500,
            ],
            [
                'name' => 'Dikembalikan',
                'amount' => 0,
            ],
            [
                'name' => 'Hilang',
                'amount' => 50000,
            ],
            [
                'name' => 'Rusak Ringan',
                'amount' => 5000,
            ],
            [
                'name' => 'Rusak Berat',
                'amount' => 10000,
            ],
            [
                'name' => 'Tolak',
                'amount' => 0,
            ],
            [
                'name' => 'Dibatalkan',
                'amount' => 0,
            ],

        ];

        foreach ($statuses as $status) {
            Status::updateOrCreate(
                ['name' => $status['name']], // cek berdasarkan kolom ini
                $status
            );
        }
    }
}
