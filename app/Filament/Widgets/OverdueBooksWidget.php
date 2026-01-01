<?php

namespace App\Filament\Widgets;

use App\Models\Status;
use App\Models\Transaction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget as BaseWidget;

class OverdueBooksWidget extends BaseWidget
{
    protected static ?string $heading = 'Buku Terlambat';

    protected int|string|array $columnSpan = 'full';

    protected static ?int $sort = 2;

    public function table(Table $table): Table
    {
        $overdueStatus = Status::where('name', 'Terlambat')->first()?->id;

        return $table
            ->query(
                Transaction::query()
                    ->with(['book', 'user'])
                    ->where('status_id', $overdueStatus)
                    ->latest()
            )
            ->columns([
                TextColumn::make('id')
                    ->label('ID')
                    ->sortable(),

                TextColumn::make('user.name')
                    ->label('Peminjam')
                    ->searchable()
                    ->sortable()
                    ->color('danger'),

                TextColumn::make('book.title')
                    ->label('Buku')
                    ->searchable()
                    ->sortable()
                    ->limit(30),

                TextColumn::make('due_date')
                    ->label('Jatuh Tempo')
                    ->date('d M Y')
                    ->sortable()
                    ->color('danger'),

                TextColumn::make('days_overdue')
                    ->label('Terlambat')
                    ->getStateUsing(fn (Transaction $record): string => $record->due_date->diffInDays(now()).' hari')
                    ->color('danger')
                    ->weight('bold'),

                TextColumn::make('user.phone_number')
                    ->label('Telepon')
                    ->formatStateUsing(fn ($record) => $record->user?->userDetail?->phone_number ?? '-')
                    ->copyable(),
            ])
            ->defaultPaginationPageOption(5)
            ->paginated([5, 10, 25])
            ->defaultSort('due_date', 'asc');
    }

    protected function getTableHeading(): string
    {
        $overdueStatus = Status::where('name', 'Terlambat')->first();
        $count = Transaction::where('status_id', $overdueStatus?->id)->count();

        return "Buku Terlambat ({$count})";
    }
}
