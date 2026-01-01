<?php

namespace App\Filament\Resources\Transactions\Schemas;

use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Components\Group;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class TransactionInfolist
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->schema([
                Group::make()
                    ->schema([
                        Section::make('Informasi Anggota')
                            ->schema([
                                TextEntry::make('user.name')
                                    ->label('Nama Peminjam')
                                    ->icon('heroicon-m-user'),
                                TextEntry::make('user.userDetail.nis')
                                    ->label('NIS')
                                    ->default('-'),
                                TextEntry::make('user.userDetail.class')
                                    ->label('Kelas')
                                    ->default('-'),
                            ])
                            ->columns(2),

                        Section::make('Informasi Buku')
                            ->schema([
                                TextEntry::make('book.title')
                                    ->label('Judul Buku')
                                    ->weight('bold')
                                    ->url(fn ($record) => $record->book_id ? route('filament.admin.resources.books.view', $record->book_id) : null)
                                    ->columnSpanFull(),
                                TextEntry::make('book.author')
                                    ->label('Penulis'),
                                TextEntry::make('book.isbn')
                                    ->label('ISBN')
                                    ->copyable(),
                                TextEntry::make('book.publisher')
                                    ->label('Penerbit'),
                                TextEntry::make('book.year_published')
                                    ->label('Tahun Terbit'),
                            ])
                            ->columns(2),
                    ])
                    ->columnSpan(['lg' => 2]),

                Group::make()
                    ->schema([
                        Section::make('Detail Peminjaman')
                            ->schema([
                                TextEntry::make('code')
                                    ->label('Kode Transaksi')
                                    ->copyable(),
                                TextEntry::make('status.name')
                                    ->label('Status')
                                    ->badge()
                                    ->color(fn ($record) => $record->getStatusColor()),
                                TextEntry::make('borrow_date')
                                    ->label('Tanggal Pinjam')
                                    ->date('d M Y'),
                                TextEntry::make('due_date')
                                    ->label('Jatuh Tempo')
                                    ->date('d M Y'),
                                TextEntry::make('return_date')
                                    ->label('Tanggal Kembali')
                                    ->date('d M Y')
                                    ->placeholder('Belum dikembalikan'),
                                TextEntry::make('penalty_total')
                                    ->label('Denda')
                                    ->money('IDR')
                                    ->default(0),
                            ]),

                        Section::make('Catatan')
                            ->schema([
                                TextEntry::make('notes')
                                    ->label('Catatan')
                                    ->default('-')
                                    ->columnSpanFull(),
                            ]),
                    ])
                    ->columnSpan(['lg' => 1]),
            ])
            ->columns(['lg' => 3]);
    }
}
