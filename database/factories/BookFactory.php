<?php

namespace Database\Factories;

use App\Models\Book;
use App\Models\Category;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Book>
 */
class BookFactory extends Factory
{
    protected $model = Book::class;

    public function definition(): array
    {
        return [
            'title' => fake()->sentence(3),
            'image' => 'images/default-book.jpg',
            'category_id' => Category::factory(),
            'isbn' => fake()->isbn13(),
            'barcode' => Book::generateBarcode(),
            'author' => fake()->name(),
            'year_published' => fake()->year(),
            'publisher' => fake()->company(),
            'synopsis' => fake()->paragraph(3),
            'book_count' => fake()->numberBetween(1, 10),
            'bookshelf' => fake()->bothify('Rak-##'),
            'source' => fake()->randomElement(['Beli', 'Hibah', 'Sewa']),
            'price' => fake()->numberBetween(50000, 500000),
            'type' => fake()->randomElement(['fiction', 'non-fiction', 'reference', 'textbook', 'journal', 'other']),
        ];
    }
}
