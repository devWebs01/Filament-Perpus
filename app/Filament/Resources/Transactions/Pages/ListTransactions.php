<?php

namespace App\Filament\Resources\Transactions\Pages;

use App\Filament\Resources\Transactions\TransactionResource;
use Artisan;
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
                ->label('Cek Buku Terlambat')
                ->icon('heroicon-o-bell-alert')
                ->color('warning')
                ->requiresConfirmation()
                ->modalHeading('Cek Buku Terlambat')
                ->modalDescription('Akan memeriksa semua buku yang terlambat dan mengirim notifikasi email ke peminjam. Lanjutkan?')
                ->modalSubmitActionLabel('Ya, Kirim Notifikasi')
                ->action(function () {
                    $exitCode = Artisan::call('books:check-overdue', [
                        '--days' => 1,
                    ]);

                    $output = Artisan::output();

                    if ($exitCode === 0) {
                        \Filament\Notifications\Notification::make()
                            ->title('Berhasil')
                            ->success()
                            ->body('Notifikasi terlambat telah diproses.')
                            ->persistent()
                            ->send();
                    } else {
                        \Filament\Notifications\Notification::make()
                            ->title('Gagal')
                            ->danger()
                            ->body('Terjadi kesalahan saat memproses notifikasi.')
                            ->persistent()
                            ->send();
                    }
                }),
        ];
    }
}
