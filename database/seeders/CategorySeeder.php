<?php

namespace Database\Seeders;

use App\Models\Category;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class CategorySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $categories = [
            'Ilmu Komputer',
            'Ilmu Pengetahuan & Matematika',
            'Ekonomi & Keuangan',
            'Bisnis & Manajemen',
            'Politik & Pemerintahan',
            'Sejarah',
            'Filsafat',
        ];

        foreach ($categories as $category) {
            try {
                $category = Category::create([
                    'name' => $category,
                    'slug' => Str::slug($category),
                ]);

                $this->command->info('Menambahkan kategori: '.$category->name);
            } catch (\Throwable $th) {
                // throw $th;
                $this->command->info('Menambahkan kategori gagal: '.$th);
            }
        }
    }
}
