<?php

namespace App\Filament\Resources\Books\Schemas;

use App\Models\Category;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon; // Asumsikan model Category ada

class BookForm
{
    // Konstanta untuk readability
    private const SECTION_BASIC_INFO = 'Informasi Dasar';

    private const SECTION_ADDITIONAL_DETAILS = 'Detail Tambahan';

    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Detail Buku')
                    ->description('Masukkan informasi lengkap tentang buku ini.')
                    ->icon(Heroicon::BookOpen)
                    ->schema([
                        // Sub-section untuk informasi dasar
                        Section::make(self::SECTION_BASIC_INFO)
                            ->schema([
                                TextInput::make('title')
                                    ->label('Judul Buku')
                                    ->placeholder('Masukkan judul buku')
                                    ->required()
                                    ->maxLength(255),
                                Select::make('category_id')
                                    ->label('Kategori')
                                    ->options(Category::pluck('name', 'id')) // Menggunakan relasi untuk opsi dinamis
                                    ->required()
                                    ->searchable(),
                                TextInput::make('isbn')
                                    ->label('ISBN')
                                    ->placeholder('Masukkan ISBN unik')
                                    ->required()
                                    ->unique(table: 'books', column: 'isbn', ignoreRecord: true) // Validasi unik
                                    ->maxLength(13),
                                TextInput::make('author')
                                    ->label('Penulis')
                                    ->placeholder('Masukkan nama penulis')
                                    ->required()
                                    ->maxLength(255),
                                TextInput::make('year_published')
                                    ->label('Tahun Terbit')
                                    ->placeholder('Masukkan tahun terbit')
                                    ->required()
                                    ->numeric()
                                    ->minValue(1000)
                                    ->maxValue(now()->year),
                                TextInput::make('publisher')
                                    ->label('Penerbit')
                                    ->placeholder('Masukkan nama penerbit')
                                    ->required()
                                    ->maxLength(255),
                            ])->columns(2),
                        // Sub-section untuk detail tambahan
                        Section::make(self::SECTION_ADDITIONAL_DETAILS)
                            ->schema([
                                FileUpload::make('image')
                                    ->label('Gambar Sampul')
                                    ->image()
                                    ->required()
                                    ->maxSize(5120) // Batas 2MB untuk performa
                                    ->acceptedFileTypes(['image/jpeg', 'image/png'])
                                    ->directory('books')
                                    ->columnSpanFull()
                                    ->imageEditor()
                                    ->imageEditorAspectRatios([
                                        null,
                                        '16:9',
                                        '4:3',
                                        '1:1',
                                    ]),
                                Textarea::make('synopsis')
                                    ->label('Sinopsis')
                                    ->placeholder('Masukkan ringkasan buku')
                                    ->required()
                                    ->columnSpanFull()
                                    ->maxLength(1000),
                                TextInput::make('book_count')
                                    ->label('Jumlah Buku')
                                    ->placeholder('Masukkan jumlah eksemplar')
                                    ->required()
                                    ->numeric()
                                    ->minValue(1),
                                TextInput::make('bookshelf')
                                    ->label('Rak Buku')
                                    ->placeholder('Masukkan lokasi rak')
                                    ->nullable(),
                                TextInput::make('source')
                                    ->label('Sumber')
                                    ->placeholder('Masukkan sumber buku')
                                    ->nullable(),
                                TextInput::make('price')
                                    ->label('Harga')
                                    ->placeholder('Masukkan harga dalam Rupiah')
                                    ->nullable()
                                    ->numeric()
                                    ->minValue(0),
                                Select::make('type')
                                    ->label('Tipe Buku')
                                    ->options([
                                        'Umum' => 'Umum',
                                        'Paket' => 'Paket',
                                    ])
                                    ->required()
                                    ->columnSpanFull(),
                            ])->columns(2),
                    ])->columnSpanFull(),
            ]);
    }
}
