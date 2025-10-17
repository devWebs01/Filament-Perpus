<?php

namespace App\Filament\Resources\Books\Tables;

use Filament\Actions\BulkActionGroup;
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
                TextColumn::make('title')
                    ->searchable(),
                ImageColumn::make('image'),
                TextColumn::make('category_id')
                    ->numeric()
                    ->sortable(),
                TextColumn::make('isbn')
                    ->searchable(),
                TextColumn::make('author')
                    ->searchable(),
                TextColumn::make('year_published')
                    ->numeric()
                    ->sortable(),
                TextColumn::make('publisher')
                    ->searchable(),
                TextColumn::make('book_count')
                    ->numeric()
                    ->sortable(),
                TextColumn::make('bookshelf')
                    ->searchable(),
                TextColumn::make('source')
                    ->searchable(),
                TextColumn::make('price')
                    ->searchable(),
                TextColumn::make('type')
                    ->searchable(),
                TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('updated_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                //
            ])
            ->recordActions([
                EditAction::make(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }
}
