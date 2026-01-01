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
            EditAction::make(),
            \Filament\Actions\Action::make('reject')
                ->label('Tolak Peminjaman')
                ->color('danger')
                ->icon('heroicon-o-x-circle')
                ->requiresConfirmation()
                ->modalHeading('Tolak Peminjaman Buku')
                ->modalDescription('Apakah Anda yakin ingin menolak peminjaman buku ini? Status akan diubah menjadi Ditolak.')
                ->modalSubmitActionLabel('Ya, Tolak')
                ->visible(fn ($record) => $record->status && $record->status->name === 'Menunggu Persetujuan')
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
        ];
    }
}
