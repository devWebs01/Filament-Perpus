<?php

namespace App\Filament\Resources\Transactions\Pages;

use App\Filament\Resources\Transactions\Schemas\TransactionForm;
use App\Filament\Resources\Transactions\TransactionResource;
use App\Models\Status;
use Filament\Resources\Pages\CreateRecord;
use Filament\Schemas\Schema;

class CreateTransaction extends CreateRecord
{
    protected static string $resource = TransactionResource::class;

    public function form(Schema $schema): Schema
    {
        return TransactionForm::configure($schema);
    }

    /**
     * Hook sebelum data disimpan
     */
    protected function mutateFormDataBeforeSave(array $data): array
    {
        // Jika status belum diset, set ke default
        if (empty($data['status_id'])) {
            $data['status_id'] = Status::where('name', 'Menunggu Persetujuan')
                ->first()?->id;
        }

        // Pastikan penalty_total adalah angka
        $data['penalty_total'] = (int) ($data['penalty_total'] ?? 0);

        return $data;
    }

    /**
     * Hook setelah data berhasil disimpan
     */
    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }
}
