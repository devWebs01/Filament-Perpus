<?php

namespace App\Filament\Resources\Transactions\Pages;

use App\Filament\Resources\Transactions\TransactionResource;
use Filament\Actions\DeleteAction;
use Filament\Actions\ForceDeleteAction;
use Filament\Actions\RestoreAction;
use Filament\Resources\Pages\EditRecord;

class EditTransaction extends EditRecord
{
    protected static string $resource = TransactionResource::class;

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make(),
            ForceDeleteAction::make(),
            RestoreAction::make(),
        ];
    }

    /**
     * Hook sebelum data diupdate
     */
    protected function mutateFormDataBeforeSave(array $data): array
    {
        // Pastikan penalty_total adalah angka
        $data['penalty_total'] = (int) ($data['penalty_total'] ?? 0);

        return $data;
    }

    /**
     * Hook setelah data berhasil diupdate
     */
    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }
}
