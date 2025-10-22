<?php

namespace App\Filament\Resources\Transactions\Tables;

use App\Models\Transaction;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ForceDeleteAction;
use Filament\Actions\ForceDeleteBulkAction;
use Filament\Actions\RestoreAction;
use Filament\Actions\RestoreBulkAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\TrashedFilter;
use Filament\Tables\Table;

class TransactionsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('code')
                    ->label('Kode Transaksi')
                    ->searchable(),
                TextColumn::make('book.title')
                    ->label('Judul Buku')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('book.category.name')
                    ->label('Kategori')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('user.name')
                    ->label('Peminjam')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('status.name')
                    ->label('Status')
                    ->searchable()
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'Menunggu Persetujuan' => 'warning',
                        'Konfirmasi Pinjam' => 'success',
                        'Terlambat' => 'danger',
                        'Dikembalikan' => 'success',
                        'Hilang' => 'danger',
                        'Rusak Ringan' => 'warning',
                        'Rusak Berat' => 'danger',
                        'Tolak' => 'danger',
                        default => 'gray',
                    }),
                TextColumn::make('penalty_total')
                    ->label('Denda')
                    ->money('IDR')
                    ->searchable(),
                TextColumn::make('borrow_date')
                    ->label('Tanggal Pinjam')
                    ->date('d/m/Y')
                    ->sortable(),
                TextColumn::make('return_date')
                    ->label('Tanggal Kembali')
                    ->date('d/m/Y')
                    ->sortable()
                    ->placeholder('Belum kembali'),
                TextColumn::make('created_at')
                    ->label('Dibuat Pada')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('updated_at')
                    ->label('Diperbarui Pada')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('deleted_at')
                    ->label('Dihapus Pada')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('created_by')
                    ->label('Dibuat Oleh')
                    ->getStateUsing(function (Transaction $record) {
                        return $record->created_by ? \App\Models\User::find($record->created_by)?->name : 'N/A';
                    })
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('updated_by')
                    ->label('Diperbarui Oleh')
                    ->getStateUsing(function (Transaction $record) {
                        return $record->updated_by ? \App\Models\User::find($record->updated_by)?->name : 'N/A';
                    })
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->defaultSort('created_at', 'desc')
            ->filters([
                TrashedFilter::make(),
            ])
            ->recordActions([
                EditAction::make()->button(),
                DeleteAction::make()->button(),
                ForceDeleteAction::make()->button(),
                RestoreAction::make()->button(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                    ForceDeleteBulkAction::make(),
                    RestoreBulkAction::make(),
                ]),
            ]);
    }
}
