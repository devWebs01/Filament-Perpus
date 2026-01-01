<?php

namespace App\Filament\Resources\Transactions\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\ViewAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Filters\TrashedFilter;
use Filament\Tables\Table;

class TransactionsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->defaultSort('created_at', 'desc')
            ->columns([
                TextColumn::make('code')
                    ->label('Kode')
                    ->searchable()
                    ->copyable()
                    ->toggleable(),

                TextColumn::make('user.name')
                    ->label('Anggota')
                    ->searchable()
                    ->sortable()
                    ->description(fn ($record): string => $record->user->userDetail->nis ?? $record->user->userDetail->nisn ?? '-'),

                TextColumn::make('book.title')
                    ->label('Buku')
                    ->searchable()
                    ->sortable()
                    ->limit(30)
                    ->tooltip(fn (TextColumn $column): ?string => $column->getState())
                    ->wrap(),

                TextColumn::make('borrow_date')
                    ->label('Tanggal Pinjam')
                    ->date('d M Y')
                    ->sortable()
                    ->toggleable(),

                TextColumn::make('due_date')
                    ->label('Jatuh Tempo')
                    ->date('d M Y')
                    ->sortable()
                    ->color(fn ($record) => $record->due_date < now() && $record->status->name === 'Dipinjam' ? 'danger' : 'default')
                    ->toggleable(),

                TextColumn::make('return_date')
                    ->label('Tanggal Kembali')
                    ->date('d M Y')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),

                TextColumn::make('status.name')
                    ->label('Status')
                    ->badge()
                    ->color(fn ($record): string => match ($record->status->name) {
                        'Dipinjam' => 'success',
                        'Dikembalikan' => 'info',
                        'Terlambat' => 'danger',
                        'Menunggu Persetujuan' => 'warning',
                        'Dibatalkan' => 'gray',
                        'Tolak' => 'danger',
                        default => 'gray',
                    })
                    ->searchable(),

                TextColumn::make('penalty_total')
                    ->label('Denda')
                    ->money('IDR')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),

                TextColumn::make('created_at')
                    ->label('Dibuat')
                    ->dateTime('d M Y H:i')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                SelectFilter::make('status_id')
                    ->label('Status')
                    ->relationship('status', 'name')
                    ->multiple()
                    ->preload(),

                SelectFilter::make('user_id')
                    ->label('Anggota')
                    ->relationship('user', 'name')
                    ->searchable()
                    ->preload(),

                TrashedFilter::make(),
            ])
            ->recordActions([
                ViewAction::make()
                    ->label('Detail')
                    ->button()
                    ->icon('heroicon-o-eye'),
            ])
            ->bulkActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ])
            ->emptyStateHeading('Belum ada transaksi')
            ->emptyStateDescription('Mulai dengan membuat transaksi peminjaman buku.')
            ->emptyStateIcon('heroicon-o-rectangle-stack');
    }
}
