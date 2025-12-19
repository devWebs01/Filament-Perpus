<?php

namespace App\Filament\Resources\Books\Schemas;

use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\RichEditor;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Schemas\Schema;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Section;


class BookForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Informasi Buku')
                    ->description('Informasi dasar tentang buku')
                    ->schema([
                        Grid::make(2)
                            ->schema([
                                TextInput::make('title')
                                    ->label('Judul Buku')
                                    ->required()
                                    ->maxLength(255)
                                    ->columnSpan(2),

                                Select::make('category_id')
                                    ->label('Kategori')
                                    ->relationship('category', 'name')
                                    ->searchable()
                                    ->preload()
                                    ->required(),

                                TextInput::make('isbn')
                                    ->label('ISBN')
                                    ->numeric()
                                    ->required()
                                    ->unique(ignoreRecord: true)
                                    ->helperText('International Standard Book Number'),
                            ]),

                        Grid::make(2)
                            ->schema([
                                TextInput::make('author')
                                    ->label('Penulis')
                                    ->required()
                                    ->maxLength(255),

                                TextInput::make('publisher')
                                    ->label('Penerbit')
                                    ->required()
                                    ->maxLength(255),

                                TextInput::make('year_published')
                                    ->label('Tahun Terbit')
                                    ->required()
                                    ->numeric()
                                    ->minValue(1000)
                                    ->maxValue(date('Y')),

                                Select::make('type')
                                    ->label('Tipe Buku')
                                    ->options([
                                        'fiction' => 'Fiksi',
                                        'non-fiction' => 'Non-Fiksi',
                                        'reference' => 'Referensi',
                                        'textbook' => 'Buku Teks',
                                        'journal' => 'Jurnal',
                                        'other' => 'Lainnya',
                                    ])
                                    ->required(),
                            ]),

                        FileUpload::make('image')
                            ->label('Sampul Buku')
                            ->image()
                            ->imageEditor()
                            ->directory('books')
                            ->visibility('public')
                            ->columnSpanFull(),

                        RichEditor::make('synopsis')
                            ->label('Sinopsis')
                            ->required()
                            ->columnSpanFull()
                            ->toolbarButtons([
                                'bold',
                                'italic',
                                'bulletList',
                                'orderedList',
                                'undo',
                                'redo',
                            ]),
                    ])
                    ->columnSpanFull(),

                Section::make('Informasi Inventaris')
                    ->description('Detail inventaris dan lokasi buku')
                    ->schema([
                        TextInput::make('book_count')
                            ->label('Jumlah Eksemplar')
                            ->required()
                            ->numeric()
                            ->default(1)
                            ->minValue(0),

                        TextInput::make('bookshelf')
                            ->label('Lokasi Rak Buku')
                            ->placeholder('contoh: A1, B2, dll.')
                            ->helperText('Lokasi fisik buku'),

                        TextInput::make('source')
                            ->label('Sumber')
                            ->placeholder('contoh: Pembelian, Donasi')
                            ->helperText('Cara buku diperoleh'),
                        TextInput::make('price')
                            ->label('Harga')
                            ->numeric()
                            ->prefix('Rp')
                            ->step(0.01)
                            ->helperText('Harga buku dalam Rupiah'),
                    ])
                    ->columns(2)
                    ->columnSpanFull(),
            ]);
    }
}
