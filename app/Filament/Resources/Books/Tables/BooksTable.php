<?php

namespace App\Filament\Resources\Books\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Tables\Columns\ImageColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class BooksTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                ImageColumn::make('image')
                    ->label('Gambar')
                    ->circular(),
                TextColumn::make('title')
                    ->label('Judul')
                    ->searchable(),
                TextColumn::make('category.name')
                    ->label('Kategori')
                    ->sortable(),
                TextColumn::make('author')
                    ->label('Penulis')
                    ->searchable(),
                TextColumn::make('year_published')
                    ->label('Tahun Terbit')
                    ->numeric()
                    ->sortable(),
                TextColumn::make('book_count')
                    ->label('Jumlah')
                    ->numeric()
                    ->sortable(),
                TextColumn::make('type')
                    ->label('Tipe')
                    ->searchable(),
                TextColumn::make('created_at')
                    ->label('Dibuat')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('updated_at')
                    ->label('Diperbarui')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                //
            ])
            ->recordActions([
                EditAction::make()->button(),
                DeleteAction::make()->button(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }
}
