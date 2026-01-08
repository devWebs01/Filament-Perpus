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
                ->visible(fn ($record) => $record->status && $record->status->name === 'Menunggu Persetujuan')
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
                })->after(
                    fn () => redirect(TransactionResource::getUrl('index'))
                ),

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
                })->after(
                    fn () => redirect(TransactionResource::getUrl('index'))
                ),

            \Filament\Actions\Action::make('return_book')
                ->label('Kembalikan')
                ->icon('heroicon-o-arrow-uturn-left')
                ->color('primary')
                ->modalHeading('Proses Pengembalian Buku')
                ->modalDescription(function ($record) {
                    if (! $record->due_date) {
                        return 'Pilih status pengembalian dan sesuaikan denda jika diperlukan.';
                    }

                    if ($record->isOverdue()) {
                        $daysOverdue = $record->getDaysOverdue();
                        $penalty = $record->getPenalty();

                        return "Buku terlambat {$daysOverdue} hari. Denda otomatis: Rp ".number_format($penalty);
                    }

                    return 'Pilih status pengembalian dan sesuaikan denda jika diperlukan.';
                })
                ->modalSubmitActionLabel('Proses Pengembalian')
                ->form([
                    \Filament\Forms\Components\Placeholder::make('overdue_info')
                        ->label('Informasi Keterlambatan')
                        ->content(function ($record) {
                            if (! $record->isOverdue()) {
                                return new \Illuminate\Support\HtmlString(
                                    '<div class="fi-ta-placeholder-text text-success-600 dark:text-success-400">✅ Dikembalikan tepat waktu</div>'
                                );
                            }

                            $daysOverdue = $record->getDaysOverdue();
                            $penalty = $record->getPenalty();

                            return new \Illuminate\Support\HtmlString(
                                '<div class="fi-ta-placeholder-text space-y-1">'.
                                '<div class="text-danger-600 dark:text-danger-400 font-semibold">Terlambat '.$daysOverdue.' hari</div>'.
                                '<div class="text-sm">Denda otomatis: <strong>Rp '.number_format($penalty).'</strong></div>'.
                                '</div>'
                            );
                        })
                        ->visible(fn ($record) => $record->due_date && $record->due_date->isPast()),
                    \Filament\Forms\Components\Select::make('return_status_id')
                        ->label('Status Pengembalian')
                        ->options(function () {
                            return \App\Models\Status::whereIn('name', ['Dikembalikan', 'Hilang', 'Rusak Ringan', 'Rusak Berat'])
                                ->pluck('name', 'id');
                        })
                        ->required()
                        ->default(function () {
                            return \App\Models\Status::where('name', 'Dikembalikan')->first()?->id;
                        })
                        ->live()
                        ->afterStateUpdated(function ($state, callable $set) {
                            $status = \App\Models\Status::find($state);
                            if ($status) {
                                $set('fine_amount', $status->amount);
                            }
                        })
                        ->selectablePlaceholder(false),
                    \Filament\Forms\Components\TextInput::make('fine_amount')
                        ->label('Denda (Rp)')
                        ->numeric()
                        ->integer()
                        ->step(1)
                        ->prefix('Rp')
                        ->default(function ($record) {
                            if ($record->isOverdue()) {
                                return $record->getPenalty();
                            }

                            return \App\Models\Status::where('name', 'Dikembalikan')
                                ->value('amount') ?? 0;
                        })
                        ->required()
                        ->minValue(0)
                        ->suffixIcon('heroicon-o-banknotes')
                        ->hint(function ($record) {
                            if (! $record->isOverdue()) {
                                return null;
                            }

                            $daysOverdue = $record->getDaysOverdue();
                            $penalty = $record->getPenalty();

                            return sprintf(
                                'Denda keterlambatan: %d hari × Rp 1.000 = Rp %s',
                                $daysOverdue,
                                number_format($penalty)
                            );
                        }),
                    \Filament\Forms\Components\Textarea::make('notes')
                        ->label('Catatan')
                        ->rows(2)
                        ->placeholder('Tambahkan catatan jika diperlukan...'),
                ])
                ->visible(fn ($record) => $record->isBorrowed())
                ->action(function ($record, array $data) {
                    $service = app(\App\Services\TransactionService::class);
                    $result = $service->returnBook($record->id, $data);

                    \Filament\Notifications\Notification::make()
                        ->title($result['success'] ? 'Berhasil' : 'Gagal')
                        ->body($result['message'])
                        ->{$result['success'] ? 'success' : 'danger'}()
                        ->send();

                })->after(
                    fn () => redirect(TransactionResource::getUrl('index'))
                ),

            EditAction::make()
                ->hidden(fn ($record) => $record->isReturned() || $record->status?->name === 'Dibatalkan'),

            \Filament\Actions\DeleteAction::make()
                ->hidden(fn ($record) => $record->isBorrowed()),
            \Filament\Actions\ForceDeleteAction::make(),
            \Filament\Actions\RestoreAction::make(),
        ];
    }
}
