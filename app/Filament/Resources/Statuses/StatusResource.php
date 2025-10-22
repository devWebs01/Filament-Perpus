<?php

namespace App\Filament\Resources\Statuses;

use App\Filament\Resources\Statuses\Pages\ManageStatuses;
use App\Models\Status;
use BackedEnum;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ForceDeleteAction;
use Filament\Actions\ForceDeleteBulkAction;
use Filament\Actions\RestoreAction;
use Filament\Actions\RestoreBulkAction;
use Filament\Forms\Components\TextInput;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\TrashedFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use UnitEnum;

class StatusResource extends Resource
{
    protected static ?string $model = Status::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedRectangleStack;

    protected static string|UnitEnum|null $navigationGroup = 'Managemen Transaksi';

    protected static ?string $navigationLabel = 'Status Peminjaman';

    protected static ?string $modelLabel = 'Status Peminjaman';

    protected static ?string $pluralModelLabel = 'Status Peminjaman';

    protected static ?string $recordTitleAttribute = 'name';

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('name')
                    ->required(),
                TextInput::make('amount'),

            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('name')
            ->columns([
                TextColumn::make('name')
                    ->label('Nama Status')
                    ->searchable(),
                TextColumn::make('amount')
                    ->label('Jumlah Denda')
                    ->searchable(),
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
                    ->getStateUsing(function (Status $record) {
                        return $record->created_by ? \App\Models\User::find($record->created_by)?->name : 'N/A';
                    })
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('updated_by')
                    ->label('Diperbarui Oleh')
                    ->getStateUsing(function (Status $record) {
                        return $record->updated_by ? \App\Models\User::find($record->updated_by)?->name : 'N/A';
                    })
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('deleted_by')
                    ->label('Dihapus Oleh')
                    ->getStateUsing(function (Status $record) {
                        return $record->deleted_by ? \App\Models\User::find($record->deleted_by)?->name : 'N/A';
                    })
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),

            ])
            ->filters([
                TrashedFilter::make(),
            ])
            ->defaultSort('created_at', 'desc')
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

    public static function getPages(): array
    {
        return [
            'index' => ManageStatuses::route('/'),
        ];
    }

    public static function getRecordRouteBindingEloquentQuery(): Builder
    {
        return parent::getRecordRouteBindingEloquentQuery()
            ->withoutGlobalScopes([
                SoftDeletingScope::class,
            ]);
    }

    /**
     * Get the redirect URL after creating a new status
     */
    public static function getRedirectUrlAfterCreate(): string
    {
        return static::getUrl('index');
    }

    /**
     * Get the redirect URL after editing a status
     */
    public static function getRedirectUrlAfterEdit(): string
    {
        return static::getUrl('index');
    }
}
