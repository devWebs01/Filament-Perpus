<?php

namespace App\Filament\Resources;

use App\Filament\Resources\BookResource\Pages;
use App\Models\Book;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class BookResource extends Resource
{
    protected static ?string $model = Book::class;

    protected static ?string $navigationIcon = 'heroicon-o-book-open';

    protected static ?string $navigationGroup = 'Library Management';

    protected static ?int $navigationSort = 1;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                // Book Information Section
                Forms\Components\Section::make('Book Information')
                    ->description('Basic information about the book')
                    ->schema([
                        Forms\Components\Grid::make(2)
                            ->schema([
                                Forms\Components\TextInput::make('title')
                                    ->label('Book Title')
                                    ->required()
                                    ->maxLength(255)
                                    ->columnSpan(2),

                                Forms\Components\Select::make('category_id')
                                    ->label('Category')
                                    ->relationship('category', 'name')
                                    ->searchable()
                                    ->preload()
                                    ->required(),

                                Forms\Components\TextInput::make('isbn')
                                    ->label('ISBN')
                                    ->required()
                                    ->unique(ignoreRecord: true)
                                    ->helperText('International Standard Book Number'),
                            ]),

                        Forms\Components\Grid::make(2)
                            ->schema([
                                Forms\Components\TextInput::make('author')
                                    ->label('Author')
                                    ->required()
                                    ->maxLength(255),

                                Forms\Components\TextInput::make('publisher')
                                    ->label('Publisher')
                                    ->required()
                                    ->maxLength(255),

                                Forms\Components\TextInput::make('year_published')
                                    ->label('Year Published')
                                    ->required()
                                    ->numeric()
                                    ->minValue(1000)
                                    ->maxValue(date('Y')),

                                Forms\Components\Select::make('type')
                                    ->label('Book Type')
                                    ->options([
                                        'fiction' => 'Fiction',
                                        'non-fiction' => 'Non-Fiction',
                                        'reference' => 'Reference',
                                        'textbook' => 'Textbook',
                                        'journal' => 'Journal',
                                        'other' => 'Other',
                                    ])
                                    ->required(),
                            ]),

                        Forms\Components\FileUpload::make('image')
                            ->label('Book Cover')
                            ->image()
                            ->imageEditor()
                            ->directory('books')
                            ->visibility('public')
                            ->columnSpanFull(),

                        Forms\Components\RichEditor::make('synopsis')
                            ->label('Synopsis')
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
                    ]),

                // Inventory Section
                Forms\Components\Section::make('Inventory Information')
                    ->description('Book inventory and location details')
                    ->schema([
                        Forms\Components\Grid::make(3)
                            ->schema([
                                Forms\Components\TextInput::make('book_count')
                                    ->label('Total Copies')
                                    ->required()
                                    ->numeric()
                                    ->default(1)
                                    ->minValue(0),

                                Forms\Components\TextInput::make('bookshelf')
                                    ->label('Bookshelf Location')
                                    ->placeholder('e.g., A1, B2, etc.')
                                    ->helperText('Physical location of the book'),

                                Forms\Components\TextInput::make('source')
                                    ->label('Source')
                                    ->placeholder('e.g., Purchase, Donation')
                                    ->helperText('How the book was acquired'),
                            ]),

                        Forms\Components\TextInput::make('price')
                            ->label('Price')
                            ->numeric()
                            ->prefix('Rp')
                            ->step(0.01)
                            ->helperText('Book price in Indonesian Rupiah'),
                    ]),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\ImageColumn::make('image')
                    ->label('Cover')
                    ->size(60)
                    ->circular()
                    ->disk('public')
                    ->defaultImageUrl(asset('images/placeholder/book-cover.png')),

                Tables\Columns\TextColumn::make('title')
                    ->label('Title')
                    ->searchable()
                    ->sortable()
                    ->limit(50)
                    ->tooltip(fn (Tables\Columns\TextColumn $column): ?string => $column->getState()),

                Tables\Columns\TextColumn::make('category.name')
                    ->label('Category')
                    ->sortable()
                    ->badge()
                    ->color('primary'),

                Tables\Columns\TextColumn::make('author')
                    ->label('Author')
                    ->searchable()
                    ->sortable(),

                Tables\Columns\TextColumn::make('isbn')
                    ->label('ISBN')
                    ->searchable()
                    ->copyable()
                    ->copyMessage('ISBN copied to clipboard')
                    ->copyMessageDuration(1500),

                Tables\Columns\TextColumn::make('book_count')
                    ->label('Copies')
                    ->numeric()
                    ->sortable()
                    ->badge()
                    ->color(fn ($record) => match (true) {
                        $record->book_count === 0 => 'danger',
                        $record->book_count <= 3 => 'warning',
                        default => 'success',
                    }),

                Tables\Columns\TextColumn::make('type')
                    ->label('Type')
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

                Tables\Columns\TextColumn::make('bookshelf')
                    ->label('Location')
                    ->searchable()
                    ->sortable()
                    ->badge()
                    ->color('info'),

                Tables\Columns\TextColumn::make('created_at')
                    ->label('Added')
                    ->dateTime('M j, Y')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->defaultSort('created_at', 'desc')
            ->filters([
                Tables\Filters\SelectFilter::make('category')
                    ->relationship('category', 'name')
                    ->searchable()
                    ->preload(),

                Tables\Filters\SelectFilter::make('type')
                    ->options([
                        'fiction' => 'Fiction',
                        'non-fiction' => 'Non-Fiction',
                        'reference' => 'Reference',
                        'textbook' => 'Textbook',
                        'journal' => 'Journal',
                        'other' => 'Other',
                    ]),

                Tables\Filters\Filter::make('available_books')
                    ->query(fn (Builder $query): Builder => $query->where('book_count', '>', 0))
                    ->label('Available Books'),

                Tables\Filters\Filter::make('out_of_stock')
                    ->query(fn (Builder $query): Builder => $query->where('book_count', '=', 0))
                    ->label('Out of Stock'),
            ])
            ->actions([
                Tables\Actions\ViewAction::make(),
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make()
                    ->requiresConfirmation()
                    ->modalHeading('Delete Book')
                    ->modalDescription('Are you sure you want to delete this book? This action cannot be undone.')
                    ->modalSubmitActionLabel('Yes, delete it'),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make()
                        ->requiresConfirmation()
                        ->modalHeading('Delete Selected Books')
                        ->modalDescription('Are you sure you want to delete these books? This action cannot be undone.')
                        ->modalSubmitActionLabel('Yes, delete them'),
                ]),
            ])
            ->emptyStateHeading('No books found')
            ->emptyStateDescription('No books have been added to the library yet.')
            ->emptyStateActions([
                Tables\Actions\CreateAction::make()
                    ->label('Add first book'),
            ]);
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListBooks::route('/'),
            'create' => Pages\CreateBook::route('/create'),
            'edit' => Pages\EditBook::route('/{record}/edit'),
        ];
    }
}
