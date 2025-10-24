<?php

namespace App\Filament\Resources;

use App\Filament\Resources\TransactionResource\Pages;
use App\Models\Transaction;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class TransactionResource extends Resource
{
    protected static ?string $model = Transaction::class;

    protected static ?string $navigationIcon = 'heroicon-o-arrow-path-rounded-square';

    protected static ?string $navigationGroup = 'Library Management';

    protected static ?int $navigationSort = 3;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Transaction Information')
                    ->description('Library book borrowing and returning information')
                    ->schema([
                        Forms\Components\Grid::make(2)
                            ->schema([
                                Forms\Components\Select::make('user_id')
                                    ->label('User')
                                    ->relationship('user', 'name')
                                    ->searchable()
                                    ->preload()
                                    ->required()
                                    ->reactive()
                                    ->afterStateUpdated(fn ($state, callable $set) => $set('user_name', \App\Models\User::find($state)?->name)
                                    ),

                                Forms\Components\Select::make('book_id')
                                    ->label('Book')
                                    ->relationship('book', 'title')
                                    ->searchable()
                                    ->preload()
                                    ->required()
                                    ->reactive()
                                    ->afterStateUpdated(fn ($state, callable $set) => $set('book_title', \App\Models\Book::find($state)?->title)
                                    ),

                                Forms\Components\Select::make('status_id')
                                    ->label('Transaction Status')
                                    ->relationship('status', 'name')
                                    ->searchable()
                                    ->preload()
                                    ->required(),

                                Forms\Components\TextInput::make('penalty_total')
                                    ->label('Penalty Amount')
                                    ->numeric()
                                    ->prefix('Rp')
                                    ->step(0.01)
                                    ->placeholder('0.00')
                                    ->helperText('Penalty amount if applicable'),
                            ]),
                    ]),

                Forms\Components\Section::make('Transaction Dates')
                    ->description('Important dates for this transaction')
                    ->schema([
                        Forms\Components\Grid::make(3)
                            ->schema([
                                Forms\Components\DatePicker::make('borrow_date')
                                    ->label('Borrow Date')
                                    ->required()
                                    ->default(now())
                                    ->helperText('When the book was borrowed'),

                                Forms\Components\DatePicker::make('due_date')
                                    ->label('Due Date')
                                    ->required()
                                    ->default(now()->addDays(7))
                                    ->helperText('When the book should be returned'),

                                Forms\Components\DatePicker::make('return_date')
                                    ->label('Return Date')
                                    ->helperText('When the book was actually returned')
                                    ->reactive()
                                    ->afterStateUpdated(function ($state, callable $get, callable $set) {
                                        if ($state && $state > $get('due_date')) {
                                            // Update status to overdue if returned late
                                            $overdueStatus = \App\Models\Status::where('name', 'overdue')->first();
                                            if ($overdueStatus) {
                                                $set('status_id', $overdueStatus->id);
                                            }
                                        } elseif ($state) {
                                            // Update status to returned if on time
                                            $returnedStatus = \App\Models\Status::where('name', 'returned')->first();
                                            if ($returnedStatus) {
                                                $set('status_id', $returnedStatus->id);
                                            }
                                        }
                                    }),
                            ]),
                    ]),

                Forms\Components\Section::make('Transaction Code')
                    ->description('Unique transaction identifier')
                    ->schema([
                        Forms\Components\TextInput::make('code')
                            ->label('Transaction Code')
                            ->required()
                            ->unique(ignoreRecord: true)
                            ->default(fn () => 'TRX-'.date('Ymd').'-'.strtoupper(uniqid()))
                            ->helperText('Unique code for this transaction'),
                    ]),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('code')
                    ->label('Transaction Code')
                    ->searchable()
                    ->sortable()
                    ->copyable()
                    ->copyMessage('Transaction code copied to clipboard')
                    ->copyMessageDuration(1500),

                Tables\Columns\TextColumn::make('user.name')
                    ->label('User')
                    ->searchable()
                    ->sortable()
                    ->weight('semibold'),

                Tables\Columns\TextColumn::make('book.title')
                    ->label('Book')
                    ->searchable()
                    ->sortable()
                    ->limit(50)
                    ->tooltip(function (Tables\Columns\TextColumn $column): ?string {
                        return $column->getRecord()->book->title;
                    }),

                Tables\Columns\TextColumn::make('status.name')
                    ->label('Status')
                    ->badge()
                    ->color(fn ($record): string => match ($record->status?->name) {
                        'borrowed' => 'warning',
                        'returned' => 'success',
                        'overdue' => 'danger',
                        default => 'gray',
                    }),

                Tables\Columns\TextColumn::make('borrow_date')
                    ->label('Borrowed')
                    ->date('M j, Y')
                    ->sortable(),

                Tables\Columns\TextColumn::make('due_date')
                    ->label('Due Date')
                    ->date('M j, Y')
                    ->sortable()
                    ->badge()
                    ->color(fn ($record): string => $record->due_date < now() && $record->status?->name !== 'returned' ? 'danger' : 'primary'
                    ),

                Tables\Columns\TextColumn::make('return_date')
                    ->label('Returned')
                    ->date('M j, Y')
                    ->sortable()
                    ->placeholder('Not returned')
                    ->badge()
                    ->color(fn ($record): string => $record->return_date ? 'success' : 'warning'),

                Tables\Columns\TextColumn::make('penalty_total')
                    ->label('Penalty')
                    ->money('IDR')
                    ->sortable()
                    ->placeholder('None')
                    ->badge()
                    ->color('danger'),

                Tables\Columns\TextColumn::make('created_at')
                    ->label('Created')
                    ->dateTime('M j, Y H:i')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->defaultSort('created_at', 'desc')
            ->filters([
                Tables\Filters\SelectFilter::make('status')
                    ->label('Status')
                    ->relationship('status', 'name')
                    ->searchable()
                    ->preload(),

                Tables\Filters\Filter::make('overdue')
                    ->query(fn (Builder $query): Builder => $query->where('due_date', '<', now())
                        ->whereHas('status', fn (Builder $q) => $q->where('name', '!=', 'returned'))
                    )
                    ->label('Overdue Books'),

                Tables\Filters\Filter::make('active')
                    ->query(fn (Builder $query): Builder => $query->whereHas('status', fn (Builder $q) => $q->where('name', 'borrowed'))
                    )
                    ->label('Active Borrowings'),

                Tables\Filters\Filter::make('date_range')
                    ->form([
                        Forms\Components\DatePicker::make('start_date')
                            ->label('Start Date'),
                        Forms\Components\DatePicker::make('end_date')
                            ->label('End Date'),
                    ])
                    ->query(function (Builder $query, array $data): Builder {
                        return $query
                            ->when(
                                $data['start_date'],
                                fn (Builder $query, $date): Builder => $query->whereDate('borrow_date', '>=', $date)
                            )
                            ->when(
                                $data['end_date'],
                                fn (Builder $query, $date): Builder => $query->whereDate('borrow_date', '<=', $date)
                            );
                    })
                    ->indicateUsing(function (array $data): array {
                        $indicators = [];
                        if ($data['start_date'] ?? null) {
                            $indicators[] = 'Start: '.$data['start_date'];
                        }
                        if ($data['end_date'] ?? null) {
                            $indicators[] = 'End: '.$data['end_date'];
                        }

                        return $indicators;
                    }),
            ])
            ->actions([
                Tables\Actions\ViewAction::make(),
                Tables\Actions\EditAction::make(),
                Tables\Actions\Action::make('return_book')
                    ->label('Return Book')
                    ->icon('heroicon-o-arrow-uturn-left')
                    ->color('success')
                    ->requiresConfirmation()
                    ->modalHeading('Return Book')
                    ->modalDescription('Mark this book as returned?')
                    ->modalSubmitActionLabel('Yes, return book')
                    ->action(function (Transaction $record) {
                        $returnedStatus = \App\Models\Status::where('name', 'returned')->first();
                        if ($returnedStatus) {
                            $record->update([
                                'status_id' => $returnedStatus->id,
                                'return_date' => now(),
                            ]);
                        }
                    })
                    ->visible(fn (Transaction $record): bool => $record->status?->name === 'borrowed'),
                Tables\Actions\DeleteAction::make()
                    ->requiresConfirmation(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make()
                        ->requiresConfirmation(),
                ]),
                Tables\Actions\BulkAction::make('mark_returned')
                    ->label('Mark as Returned')
                    ->icon('heroicon-o-check')
                    ->color('success')
                    ->requiresConfirmation()
                    ->modalHeading('Mark Selected as Returned')
                    ->modalDescription('Mark all selected books as returned?')
                    ->modalSubmitActionLabel('Yes, mark as returned')
                    ->action(function ($records) {
                        $returnedStatus = \App\Models\Status::where('name', 'returned')->first();
                        if ($returnedStatus) {
                            $records->each(function (Transaction $record) use ($returnedStatus) {
                                if ($record->status?->name === 'borrowed') {
                                    $record->update([
                                        'status_id' => $returnedStatus->id,
                                        'return_date' => now(),
                                    ]);
                                }
                            });
                        }
                    })
                    ->deselectRecordsAfterCompletion()
                    ->visible(fn (): bool => auth()->user()->can('manage_transactions')),
            ])
            ->emptyStateHeading('No transactions found')
            ->emptyStateDescription('No library transactions have been recorded yet.')
            ->emptyStateActions([
                Tables\Actions\CreateAction::make()
                    ->label('Create first transaction'),
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
            'index' => Pages\ListTransactions::route('/'),
            'create' => Pages\CreateTransaction::route('/create'),
            'edit' => Pages\EditTransaction::route('/{record}/edit'),
        ];
    }
}
