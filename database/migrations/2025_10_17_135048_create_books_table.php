<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('books', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->string('image');
            $table->foreignId('category_id')->constrained()->onDelete('cascade');
            $table->string('isbn');
            $table->string('author');
            $table->year('year_published');
            $table->string('publisher');
            $table->longText('synopsis');
            $table->integer('book_count');
            $table->string('bookshelf')->nullable();
            $table->string('source')->nullable();
            $table->string('price')->nullable();
            $table->enum('type', [
                'fiction',
                'non-fiction',
                'reference',
                'textbook',
                'journal',
                'other',
            ]);
            $table->timestamps();
            $table->softDeletes();
            $table->userstamps();
            $table->userstampSoftDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('books');
    }
};
