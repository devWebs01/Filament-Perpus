<?php

namespace App\Filament\Resources\Books\Schemas;

use Filament\Infolists\Components\ImageEntry;
use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class BookInfolist
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
                                TextEntry::make('title')
                                    ->label('Judul Buku')
                                    ->formatStateUsing(fn ($record) => $record?->title ?? '-')
                                    ->columnSpan(2),

                                TextEntry::make('category')
                                    ->label('Kategori')
                                    ->formatStateUsing(fn ($record) => $record?->category?->name ?? '-'),

                                TextEntry::make('isbn')
                                    ->label('ISBN')
                                    ->formatStateUsing(fn ($record) => $record?->isbn ?? '-'),
                            ]),

                        Grid::make(2)
                            ->schema([
                                TextEntry::make('author')
                                    ->label('Penulis')
                                    ->formatStateUsing(fn ($record) => $record?->author ?? '-'),

                                TextEntry::make('publisher')
                                    ->label('Penerbit')
                                    ->formatStateUsing(fn ($record) => $record?->publisher ?? '-'),

                                TextEntry::make('year_published')
                                    ->label('Tahun Terbit')
                                    ->formatStateUsing(fn ($record) => $record?->year_published ?? '-'),

                                TextEntry::make('type')
                                    ->label('Tipe Buku')
                                    ->formatStateUsing(fn ($record) => match ($record?->type) {
                                        'fiction' => 'Fiksi',
                                        'non-fiction' => 'Non-Fiksi',
                                        'reference' => 'Referensi',
                                        'textbook' => 'Buku Teks',
                                        'journal' => 'Jurnal',
                                        'other' => 'Lainnya',
                                        default => ($record?->type ?? '-'),
                                    }),
                            ]),
                    ])
                    ->columnSpanFull(),

                Section::make('Gambar Data Buku')
                    ->description('Barcode untuk scanning dan sampul buku')
                    ->schema([
                        ImageEntry::make('barcode_image')
                            ->label('Gambar Barcode')
                            ->disk('public')
                            ->defaultImageUrl(fn ($record) => $record?->barcode_image)
                            ->extraAttributes(['style' => 'max-width: 200px; height: auto;'])
                            ->default('-'),

                        ImageEntry::make('image')
                            ->label('Sampul Buku')
                            ->disk('public')
                            ->extraAttributes(['style' => 'max-width: 200px; height: auto;'])
                            ->defaultImageUrl(url('/images/no-cover.png')),
                    ])
                    ->columns(2)
                    ->columnSpanFull(),

                Section::make('Sinopsis')
                    ->description('Ringkasan isi buku')
                    ->schema([
                        TextEntry::make('synopsis')
                            ->label('Sinopsis')
                            ->markdown()
                            ->formatStateUsing(fn ($record) => $record?->synopsis ?? '<em>Tidak ada sinopsis</em>')
                            ->columnSpanFull(),
                    ])
                    ->columnSpanFull(),

                Section::make('Informasi Inventaris')
                    ->description('Detail inventaris dan lokasi buku')
                    ->schema([
                        Grid::make(2)
                            ->schema([
                                TextEntry::make('price')
                                    ->label('Harga')
                                    ->formatStateUsing(fn ($record) => $record && $record->price !== null
                                        ? 'Rp '.number_format($record->price, 2, ',', '.')
                                        : '-'),

                                TextEntry::make('book_count')
                                    ->label('Jumlah Eksemplar')
                                    ->formatStateUsing(fn ($record) => $record?->book_count ?? 0),
                            ]),

                        Grid::make(2)
                            ->schema([
                                TextEntry::make('bookshelf')
                                    ->label('Lokasi Rak Buku')
                                    ->formatStateUsing(fn ($record) => $record?->bookshelf ?? '-'),

                                TextEntry::make('source')
                                    ->label('Sumber')
                                    ->formatStateUsing(fn ($record) => $record?->source ?? '-'),
                            ]),

                        TextEntry::make('available')
                            ->label('Tersedia')
                            ->formatStateUsing(fn ($record) => method_exists($record, 'getAvailableCount')
                                ? ($record->getAvailableCount())
                                : ($record?->available ?? '-'))
                            ->columnSpanFull(),
                    ])
                    ->columns(2)
                    ->columnSpanFull(),
            ]);
    }
}
