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

    protected static ?string $navigationGroup = 'Manajemen Perpustakaan';

    protected static ?int $navigationSort = 3;

    public static function getModelLabel(): string
    {
        return 'Transaksi';
    }

    public static function getPluralModelLabel(): string
    {
        return 'Transaksi';
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Informasi Transaksi')
                    ->description('Informasi peminjaman dan pengembalian buku perpustakaan')
                    ->schema([
                        Forms\Components\Grid::make(2)
                            ->schema([
                                Forms\Components\Select::make('user_id')
                                    ->label('Pengguna')
                                    ->relationship('user', 'name')
                                    ->searchable()
                                    ->preload()
                                    ->required()
                                    ->reactive()
                                    ->afterStateUpdated(fn ($state, callable $set) => $set('user_name', \App\Models\User::find($state)?->name)
                                    ),

                                Forms\Components\Select::make('book_id')
                                    ->label('Buku')
                                    ->relationship('book', 'title')
                                    ->searchable()
                                    ->preload()
                                    ->required()
                                    ->reactive()
                                    ->afterStateUpdated(fn ($state, callable $set) => $set('book_title', \App\Models\Book::find($state)?->title)
                                    ),

                                Forms\Components\Select::make('status_id')
                                    ->label('Status Transaksi')
                                    ->relationship('status', 'name')
                                    ->searchable()
                                    ->preload()
                                    ->required(),

                                Forms\Components\TextInput::make('penalty_total')
                                    ->label('Jumlah Denda')
                                    ->numeric()
                                    ->prefix('Rp')
                                    ->step(0.01)
                                    ->placeholder('0.00')
                                    ->helperText('Jumlah denda jika ada'),
                            ]),
                    ]),

                Forms\Components\Section::make('Tanggal Transaksi')
                    ->description('Tanggal-tanggal penting untuk transaksi ini')
                    ->schema([
                        Forms\Components\Grid::make(3)
                            ->schema([
                                Forms\Components\DatePicker::make('borrow_date')
                                    ->label('Tanggal Pinjam')
                                    ->required()
                                    ->default(now())
                                    ->helperText('Tanggal buku dipinjam'),

                                Forms\Components\DatePicker::make('due_date')
                                    ->label('Tanggal Jatuh Tempo')
                                    ->required()
                                    ->default(now()->addDays(7))
                                    ->helperText('Tanggal buku harus dikembalikan'),

                                Forms\Components\DatePicker::make('return_date')
                                    ->label('Tanggal Kembali')
                                    ->helperText('Tanggal buku benar-benar dikembalikan')
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

                Forms\Components\Section::make('Kode Transaksi')
                    ->description('Identifier unik untuk transaksi')
                    ->schema([
                        Forms\Components\TextInput::make('code')
                            ->label('Kode Transaksi')
                            ->required()
                            ->unique(ignoreRecord: true)
                            ->default(fn () => 'TRX-'.date('Ymd').'-'.strtoupper(uniqid()))
                            ->helperText('Kode unik untuk transaksi ini'),
                    ]),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('code')
                    ->label('Kode Transaksi')
                    ->searchable()
                    ->sortable()
                    ->copyable()
                    ->copyMessage('Kode transaksi disalin ke clipboard')
                    ->copyMessageDuration(1500),

                Tables\Columns\TextColumn::make('user.name')
                    ->label('Pengguna')
                    ->searchable()
                    ->sortable()
                    ->weight('semibold'),

                Tables\Columns\TextColumn::make('book.title')
                    ->label('Buku')
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
                    ->label('Tanggal Pinjam')
                    ->date('j M Y')
                    ->sortable(),

                Tables\Columns\TextColumn::make('due_date')
                    ->label('Jatuh Tempo')
                    ->date('j M Y')
                    ->sortable()
                    ->badge()
                    ->color(fn ($record): string => $record->due_date < now() && $record->status?->name !== 'returned' ? 'danger' : 'primary'
                    ),

                Tables\Columns\TextColumn::make('return_date')
                    ->label('Tanggal Kembali')
                    ->date('j M Y')
                    ->sortable()
                    ->placeholder('Belum dikembalikan')
                    ->badge()
                    ->color(fn ($record): string => $record->return_date ? 'success' : 'warning'),

                Tables\Columns\TextColumn::make('penalty_total')
                    ->label('Denda')
                    ->money('IDR')
                    ->sortable()
                    ->placeholder('Tidak ada')
                    ->badge()
                    ->color('danger'),

                Tables\Columns\TextColumn::make('created_at')
                    ->label('Dibuat')
                    ->dateTime('j M Y H:i')
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
                    ->label('Buku Terlambat'),

                Tables\Filters\Filter::make('active')
                    ->query(fn (Builder $query): Builder => $query->whereHas('status', fn (Builder $q) => $q->where('name', 'borrowed'))
                    )
                    ->label('Peminjaman Aktif'),

                Tables\Filters\Filter::make('date_range')
                    ->form([
                        Forms\Components\DatePicker::make('start_date')
                            ->label('Tanggal Mulai'),
                        Forms\Components\DatePicker::make('end_date')
                            ->label('Tanggal Selesai'),
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
                            $indicators[] = 'Mulai: '.$data['start_date'];
                        }
                        if ($data['end_date'] ?? null) {
                            $indicators[] = 'Selesai: '.$data['end_date'];
                        }

                        return $indicators;
                    }),
            ])
            ->actions([
                Tables\Actions\ViewAction::make(),
                Tables\Actions\EditAction::make(),
                Tables\Actions\Action::make('return_book')
                    ->label('Kembalikan Buku')
                    ->icon('heroicon-o-arrow-uturn-left')
                    ->color('success')
                    ->requiresConfirmation()
                    ->modalHeading('Kembalikan Buku')
                    ->modalDescription('Tandai buku ini sebagai dikembalikan?')
                    ->modalSubmitActionLabel('Ya, kembalikan buku')
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
                    ->label('Tandai Dikembalikan')
                    ->icon('heroicon-o-check')
                    ->color('success')
                    ->requiresConfirmation()
                    ->modalHeading('Tandai yang Dipilih Dikembalikan')
                    ->modalDescription('Tandai semua buku yang dipilih sebagai dikembalikan?')
                    ->modalSubmitActionLabel('Ya, tandai dikembalikan')
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
            ->emptyStateHeading('Tidak ada transaksi ditemukan')
            ->emptyStateDescription('Belum ada transaksi perpustakaan yang dicatat.')
            ->emptyStateActions([
                Tables\Actions\CreateAction::make()
                    ->label('Buat transaksi pertama'),
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
