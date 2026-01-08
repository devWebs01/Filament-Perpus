<?php

namespace App\Filament\Resources\Transactions\Pages;

use App\Filament\Resources\Transactions\TransactionResource;
use App\Jobs\CheckOverdueBooksJob;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListTransactions extends ListRecords
{
    protected static string $resource = TransactionResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
            \Filament\Actions\Action::make('checkOverdue')
                ->label('Kirim Pesan Keterlambatan')
                ->icon('heroicon-o-bell-alert')
                ->color('warning')
                ->action(function () {
                    // Dispatch job to background - no waiting!
                    dispatch(new CheckOverdueBooksJob);

                    // Immediate notification - user can continue working
                    \Filament\Notifications\Notification::make()
                        ->title('Proses Latar Belakang')
                        ->success()
                        ->body('Pengecekan buku terlambat sedang diproses. Anda akan mendapat notifikasi setelah selesai.')
                        ->seconds(3)
                        ->send();
                }),
        ];
    }
}
