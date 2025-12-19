<?php

namespace Database\Factories;

use App\Models\Category;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Category>
 */
class CategoryFactory extends Factory
{
    protected $model = Category::class;

    public function definition(): array
    {
        $name = fake()->unique()->randomElement([
            'Fiksi',
            'Non-Fiksi',
            'Sains',
            'Teknologi',
            'Sejarah',
            'Seni',
            'Bahasa',
            'Matematika',
            'Referensi',
            'Anak-anak',
        ]);

        return [
            'name' => $name,
            'slug' => Str::slug($name),
        ];
    }
}
