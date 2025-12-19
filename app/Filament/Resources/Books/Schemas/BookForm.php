<?php

namespace App\Filament\Resources\Books\Schemas;

use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Schemas\Schema;

class BookForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('title')
                    ->required(),
                FileUpload::make('image')
                    ->image()
                    ->required(),
                TextInput::make('category_id')
                    ->required()
                    ->numeric(),
                TextInput::make('isbn')
                    ->required(),
                TextInput::make('author')
                    ->required(),
                TextInput::make('year_published')
                    ->required()
                    ->numeric(),
                TextInput::make('publisher')
                    ->required(),
                Textarea::make('synopsis')
                    ->required()
                    ->columnSpanFull(),
                TextInput::make('book_count')
                    ->required()
                    ->numeric(),
                TextInput::make('bookshelf'),
                TextInput::make('source'),
                TextInput::make('price'),
                TextInput::make('barcode'),
                TextInput::make('type')
                    ->required(),
                TextInput::make('created_by')
                    ->numeric(),
                TextInput::make('updated_by')
                    ->numeric(),
                TextInput::make('deleted_by')
                    ->numeric(),
            ]);
    }
}
