<?php

namespace App\Filament\Resources\Transactions\Tables;

use Filament\Actions\Action;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
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
                    ->description(fn ($record): string => $record->user->userDetail?->nis ?? $record->user->userDetail?->nisn ?? '-'),

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

                EditAction::make()
                    ->label('Edit')
                    ->button()
                    ->hidden(fn ($record) => in_array($record->status->name, ['Dikembalikan', 'Dibatalkan'], true)),

                // Action untuk konfirmasi peminjaman
                Action::make('confirm_borrow')
                    ->label('Konfirmasi')
                    ->icon('heroicon-o-check')
                    ->color('success')
                    ->button()
                    ->requiresConfirmation()
                    ->visible(fn ($record) => $record->status->name === 'Menunggu Persetujuan')
                    ->action(function ($record) {
                        $service = app(\App\Services\TransactionService::class);
                        $result = $service->confirmBorrow($record->id);

                        if ($result['success']) {
                            \Filament\Notifications\Notification::make()
                                ->title('Berhasil')
                                ->body($result['message'])
                                ->success()
                                ->send();
                        } else {
                            \Filament\Notifications\Notification::make()
                                ->title('Gagal')
                                ->body($result['message'])
                                ->danger()
                                ->send();
                        }
                    }),

                // Action untuk pengembalian buku
                Action::make('return_book')
                    ->label('Kembalikan')
                    ->icon('heroicon-o-arrow-uturn-left')
                    ->color('primary')
                    ->button()
                    ->requiresConfirmation()
                    ->modalHeading('Konfirmasi Pengembalian')
                    ->modalDescription('Apakah Anda yakin ingin memproses pengembalian buku ini?')
                    ->visible(fn ($record) => in_array($record->status->name, ['Dipinjam', 'Terlambat'], true))
                    ->action(function ($record) {
                        $service = app(\App\Services\TransactionService::class);
                        $result = $service->returnBook($record->id);

                        \Filament\Notifications\Notification::make()
                            ->title($result['success'] ? 'Berhasil' : 'Gagal')
                            ->body($result['message'])
                            ->{$result['success'] ? 'success' : 'danger'}()
                            ->send();
                    }),

                DeleteAction::make()
                    ->button()
                    ->hidden(fn ($record) => in_array($record->status->name, ['Dipinjam', 'Terlambat'], true)),
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
