<?php

namespace App\Filament\Resources\CategoryResource\RelationManagers;

use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;
use Filament\Tables\Table;

class BooksRelationManager extends RelationManager
{
    protected static string $relationship = 'books';

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('title')
                    ->required()
                    ->maxLength(255),

                Forms\Components\TextInput::make('author')
                    ->required()
                    ->maxLength(255),

                Forms\Components\TextInput::make('isbn')
                    ->required()
                    ->unique(ignoreRecord: true)
                    ->maxLength(255),

                Forms\Components\TextInput::make('book_count')
                    ->required()
                    ->numeric()
                    ->default(1),

                Forms\Components\Select::make('type')
                    ->options([
                        'fiction' => 'Fiction',
                        'non-fiction' => 'Non-Fiction',
                        'reference' => 'Reference',
                        'textbook' => 'Textbook',
                        'journal' => 'Journal',
                        'other' => 'Other',
                    ])
                    ->required(),
            ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('title')
            ->columns([
                Tables\Columns\TextColumn::make('title')
                    ->searchable()
                    ->sortable(),

                Tables\Columns\TextColumn::make('author')
                    ->searchable()
                    ->sortable(),

                Tables\Columns\TextColumn::make('isbn')
                    ->searchable()
                    ->copyable(),

                Tables\Columns\TextColumn::make('book_count')
                    ->numeric()
                    ->sortable()
                    ->badge()
                    ->color(fn ($record) => match (true) {
                        $record->book_count === 0 => 'danger',
                        $record->book_count <= 3 => 'warning',
                        default => 'success',
                    }),

                Tables\Columns\TextColumn::make('type')
                    ->searchable()
                    ->sortable()
                    ->badge()
                    ->color(fn ($record) => match ($record->type) {
                        'fiction' => 'pink',
                        'non-fiction' => 'blue',
                        'reference' => 'yellow',
                        'textbook' => 'green',
                        'journal' => 'purple',
                        default => 'gray',
                    }),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('type')
                    ->options([
                        'fiction' => 'Fiction',
                        'non-fiction' => 'Non-Fiction',
                        'reference' => 'Reference',
                        'textbook' => 'Textbook',
                        'journal' => 'Journal',
                        'other' => 'Other',
                    ]),
            ])
            ->headerActions([
                Tables\Actions\CreateAction::make(),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }
}
