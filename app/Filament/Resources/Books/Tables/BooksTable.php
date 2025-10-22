<?php

namespace App\Filament\Resources\Books\Tables;

use App\Models\Book;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ForceDeleteAction;
use Filament\Actions\ForceDeleteBulkAction;
use Filament\Actions\RestoreAction;
use Filament\Actions\RestoreBulkAction;
use Filament\Tables\Columns\ImageColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\TrashedFilter;
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
                    ->searchable()
                    ->limit(20),
                TextColumn::make('category.name')
                    ->label('Kategori')
                    ->sortable()
                    ->limit(15),
                TextColumn::make('book_count')
                    ->label('Jumlah')
                    ->numeric()
                    ->sortable(),
                TextColumn::make('type')
                    ->label('Tipe')
                    ->searchable(),
                TextColumn::make('deleted_at')
                    ->label('Dihapus Pada')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('created_by')
                    ->label('Dibuat Oleh')
                    ->getStateUsing(function (Book $record) {
                        return $record->created_by ? \App\Models\User::find($record->created_by)?->name : 'N/A';
                    })
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('updated_by')
                    ->label('Diperbarui Oleh')
                    ->getStateUsing(function (Book $record) {
                        return $record->updated_by ? \App\Models\User::find($record->updated_by)?->name : 'N/A';
                    })
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('deleted_by')
                    ->label('Dihapus Oleh')
                    ->getStateUsing(function (Book $record) {
                        return $record->deleted_by ? \App\Models\User::find($record->deleted_by)?->name : 'N/A';
                    })
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->defaultSort('created_at', 'desc')
            ->filters([
                TrashedFilter::make(),
            ])
            ->recordActions([
                EditAction::make()->button(),
                DeleteAction::make()->button(),
                ForceDeleteAction::make()->button(),
                RestoreAction::make()->button(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                    ForceDeleteBulkAction::make(),
                    RestoreBulkAction::make(),
                ]),
            ]);
    }
}
