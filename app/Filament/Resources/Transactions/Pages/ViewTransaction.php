<?php

namespace App\Filament\Resources\Transactions\Pages;

use App\Filament\Resources\Transactions\TransactionResource;
use Filament\Actions\EditAction;
use Filament\Resources\Pages\ViewRecord;

class ViewTransaction extends ViewRecord
{
    protected static string $resource = TransactionResource::class;

    protected function getHeaderActions(): array
    {
        return [
            \Filament\Actions\Action::make('confirm_borrow')
                ->label('Konfirmasi')
                ->icon('heroicon-o-check')
                ->color('success')
                ->requiresConfirmation()
                ->visible(fn($record) => $record->status && $record->status->name === 'Menunggu Persetujuan')
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

            \Filament\Actions\Action::make('reject')
                ->label('Tolak Peminjaman')
                ->color('danger')
                ->icon('heroicon-o-x-circle')
                ->requiresConfirmation()
                ->modalHeading('Tolak Peminjaman Buku')
                ->modalDescription('Apakah Anda yakin ingin menolak peminjaman buku ini? Status akan diubah menjadi Ditolak.')
                ->modalSubmitActionLabel('Ya, Tolak')
                ->visible(fn($record) => $record->status && $record->status->name === 'Menunggu Persetujuan')
                ->action(function ($record) {
                    $tolakStatus = \App\Models\Status::where('name', 'Tolak')->first();

                    if ($tolakStatus) {
                        $record->update(['status_id' => $tolakStatus->id]);

                        \Filament\Notifications\Notification::make()
                            ->title('Peminjaman Ditolak')
                            ->success()
                            ->send();
                    }
                }),

            \Filament\Actions\Action::make('return_book')
                ->label('Kembalikan')
                ->icon('heroicon-o-arrow-uturn-left')
                ->color('primary')
                ->requiresConfirmation()
                ->modalHeading('Konfirmasi Pengembalian')
                ->modalDescription('Apakah Anda yakin ingin memproses pengembalian buku ini?')
                ->visible(fn($record) => $record->status && in_array($record->status->name, ['Dipinjam', 'Terlambat'], true))
                ->action(function ($record) {
                    $service = app(\App\Services\TransactionService::class);
                    $result = $service->returnBook($record->id);

                    \Filament\Notifications\Notification::make()
                                ->title($result['success'] ? 'Berhasil' : 'Gagal')
                                ->body($result['message'])
                        ->{$result['success'] ? 'success' : 'danger'}()
                            ->send();
                }),

            EditAction::make()
                ->hidden(fn($record) => $record->status && in_array($record->status->name, ['Dikembalikan', 'Dibatalkan'], true)),

            \Filament\Actions\DeleteAction::make()
                ->hidden(fn($record) => $record->status && in_array($record->status->name, ['Dipinjam', 'Terlambat'], true)),
            \Filament\Actions\ForceDeleteAction::make(),
            \Filament\Actions\RestoreAction::make(),
        ];
    }
}
