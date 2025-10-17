<?php

namespace App\Filament\Resources\Books\Schemas;

use Filament\Infolists\Components\ImageEntry;
use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;

class BookInfolist
{
    // Konstanta untuk readability, sesuai dengan BookForm.php
    private const SECTION_BASIC_INFO = 'Informasi Dasar';

    private const SECTION_ADDITIONAL_DETAILS = 'Detail Tambahan';

    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Detail Buku')
                    ->description('Informasi lengkap tentang buku ini.')
                    ->icon(Heroicon::BookOpen)
                    ->schema([
                        // Sub-section untuk informasi dasar
                        Section::make(self::SECTION_BASIC_INFO)
                            ->schema([
                                TextEntry::make('title')
                                    ->label('Judul Buku'),
                                ImageEntry::make('image')
                                    ->label('Gambar Buku')
                                    ->disk('public') // Asumsi gambar disimpan di disk public
                                    ->default(asset('images/default-book.png')) // Placeholder jika gambar tidak ada
                                    ->width(200)
                                    ->height(300),
                                TextEntry::make('category.name')
                                    ->label('Kategori')
                                    ->placeholder('Tidak ada kategori'),
                                TextEntry::make('isbn')
                                    ->label('ISBN'),
                                TextEntry::make('author')
                                    ->label('Penulis'),
                                TextEntry::make('year_published')
                                    ->label('Tahun Terbit')
                                    ->date('Y') // Format sebagai tahun
                                    ->placeholder('-'),
                                TextEntry::make('publisher')
                                    ->label('Penerbit'),
                            ])->columns(2),
                        // Sub-section untuk detail tambahan
                        Section::make(self::SECTION_ADDITIONAL_DETAILS)
                            ->schema([
                                TextEntry::make('synopsis')
                                    ->label('Sinopsis')
                                    ->columnSpanFull(),
                                TextEntry::make('book_count')
                                    ->label('Jumlah Buku')
                                    ->numeric(),
                                TextEntry::make('bookshelf')
                                    ->label('Rak Buku')
                                    ->placeholder('-'),
                                TextEntry::make('source')
                                    ->label('Sumber')
                                    ->placeholder('-'),
                                TextEntry::make('price')
                                    ->label('Harga')
                                    ->money('IDR') // Format sebagai mata uang Rupiah
                                    ->placeholder('-'),
                                TextEntry::make('type')
                                    ->label('Tipe')
                                    ->placeholder('-'),
                                TextEntry::make('created_at')
                                    ->label('Dibuat Pada')
                                    ->dateTime()
                                    ->placeholder('-'),
                                TextEntry::make('updated_at')
                                    ->label('Diperbarui Pada')
                                    ->dateTime()
                                    ->placeholder('-'),
                            ])->columns(2),
                    ])->columnSpanFull(),
            ]);
    }
}
