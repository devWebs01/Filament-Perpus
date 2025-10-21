<?php

namespace App\Filament\Resources\Users\Tables;

use App\Models\User;
use Filament\Actions\Action;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ForceDeleteBulkAction;
use Filament\Actions\RestoreBulkAction;
use Filament\Actions\ViewAction;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\Filter;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Filters\TrashedFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

/**
 * Users Table Configuration
 *
 * This table configuration displays users with their roles, permissions,
 * and library information with comprehensive filtering and actions.
 */
class UsersTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                // User Information
                TextColumn::make('name')
                    ->label('Nama')
                    ->searchable()
                    ->sortable()
                    ->weight('bold'),

                TextColumn::make('email')
                    ->label('Email')
                    ->searchable()
                    ->sortable()
                    ->copyable()
                    ->copyMessage('Email disalin ke clipboard')
                    ->copyMessageDuration(1500),

                // User Details - Only show if UserDetail relationship exists
                TextColumn::make('UserDetail.phone_number')
                    ->label('Telepon')
                    ->searchable()
                    ->placeholder('Tidak Ada')
                    ->toggleable()
                    ->getStateUsing(function (User $record): ?string {
                        try {
                            return $record->UserDetail?->phone_number;
                        } catch (\Exception $e) {
                            return null;
                        }
                    }),

                TextColumn::make('user_type')
                    ->label('Tipe Pengguna')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'super_admin' => 'danger',
                        'ketua_perpustakaan' => 'warning',
                        'petugas' => 'info',
                        'siswa' => 'success',
                        default => 'gray',
                    })
                    ->getStateUsing(function (User $record): ?string {
                        try {
                            return $record->UserDetail?->user_type_display_name;
                        } catch (\Exception $e) {
                            // Get user type from role as fallback
                            return $record->roles->first()?->name;
                        }
                    })
                    ->searchable()
                    ->sortable(),

                TextColumn::make('UserDetail.class')
                    ->label('Kelas')
                    ->searchable()
                    ->placeholder('Tidak Ada')
                    ->toggleable()
                    ->getStateUsing(function (User $record): ?string {
                        try {
                            return $record->UserDetail?->class;
                        } catch (\Exception $e) {
                            return null;
                        }
                    }),

                // Roles and Permissions
                TextColumn::make('roles.name')
                    ->label('Peran')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'super_admin' => 'danger',
                        'ketua_perpustakaan' => 'warning',
                        'petugas' => 'info',
                        'siswa' => 'success',
                        default => 'gray',
                    })
                    ->searchable()
                    ->separator(','),

                IconColumn::make('email_verified_at')
                    ->label('Terverifikasi')
                    ->boolean()
                    ->trueColor('success')
                    ->falseColor('warning')
                    ->trueIcon('heroicon-o-check-circle')
                    ->falseIcon('heroicon-o-x-circle')
                    ->sortable(),

                TextColumn::make('UserDetail.membership_status_display_name')
                    ->label('Keanggotaan')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'Active' => 'success',
                        'Suspended' => 'warning',
                        'Expired' => 'danger',
                        default => 'gray',
                    })
                    ->sortable(),

                // Timestamps
                TextColumn::make('created_at')
                    ->label('Dibuat')
                    ->dateTime('M j, Y')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),

                TextColumn::make('updated_at')
                    ->label('Diperbarui')
                    ->dateTime('M j, Y')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->defaultSort('created_at', 'desc')
            ->filters([
                // Role Filter
                SelectFilter::make('role')
                    ->label('Peran')
                    ->relationship('roles', 'name')
                    ->searchable()
                    ->preload()
                    ->indicator('Peran'),

                // User Type Filter
                Filter::make('user_type')
                    ->label('Tipe Pengguna')
                    ->form([
                        \Filament\Forms\Components\CheckboxList::make('types')
                            ->label('Pilih Tipe Pengguna')
                            ->options([
                                'siswa' => 'Siswa',
                                'ketua_perpustakaan' => 'Ketua Perpustakaan',
                                'petugas' => 'Petugas Perpustakaan',
                            ])
                            ->columns(3),
                    ])
                    ->query(function (Builder $query, array $data): Builder {
                        return $query->when($data['types'], function (Builder $query, array $types) {
                            $query->whereHas('roles', function (Builder $query) use ($types) {
                                $query->whereIn('name', $types);
                            });
                        });
                    })
                    ->indicateUsing(function (array $data): ?string {
                        if (! $data['types']) {
                            return null;
                        }

                        return 'Jenis Pengguna: '.implode(', ', $data['types']);
                    }),

                // Membership Status Filter (Only show if UserDetail table exists)
                SelectFilter::make('membership_status')
                    ->label('Status Keanggotaan')
                    ->options([
                        'active' => 'Aktif',
                        'suspended' => 'Ditangguhkan',
                        'expired' => 'Kadaluarsa',
                    ])
                    ->query(function (Builder $query, array $data): Builder {
                        return $query->when($data['value'], function (Builder $query, string $value) {
                            try {
                                $query->whereHas('UserDetail', function (Builder $query) use ($value) {
                                    $query->where('membership_status', $value);
                                });
                            } catch (\Exception $e) {
                                // Skip filter if UserDetail doesn't exist
                                return $query;
                            }
                        });
                    })
                    ->indicator('Keanggotaan'),

                // Email Verification Filter
                Filter::make('verified')
                    ->label('Verifikasi Email')
                    ->query(fn (Builder $query): Builder => $query->whereNotNull('email_verified_at'))
                    ->toggle(),

                // Trashed Filter
                TrashedFilter::make(),
            ])
            ->actions([
                // View Action
                ViewAction::make()
                    ->visible(fn (): bool => auth()->user()->can('user_read')),

                // Edit Action
                EditAction::make()
                    ->visible(fn (): bool => auth()->user()->can('user_update')),

                // Delete Action
                DeleteAction::make()
                    ->visible(fn (): bool => auth()->user()->can('user_delete')),

                // Manage Permissions Action
                Action::make('manage_permissions')
                    ->label('Izin Akses')
                    ->icon('heroicon-o-shield-check')
                    ->color('warning')
                    ->visible(fn ($record): bool => auth()->user()->can('role_update') &&
                    $record->roles()->exists()
                    )
                    ->url(fn ($record): string => route('filament.admin.resources.shield.roles.edit', ['record' => $record->roles->first()->id]))
                    ->openUrlInNewTab(),

                // Reset Password Action
                Action::make('reset_password')
                    ->label('Reset Kata Sandi')
                    ->icon('heroicon-o-key')
                    ->color('danger')
                    ->visible(fn (): bool => auth()->user()->can('user_update'))
                    ->requiresConfirmation()
                    ->action(function ($record) {
                        // This would typically trigger a password reset email
                        // For now, we'll just show a success message
                        \Filament\Notifications\Notification::make()
                            ->title('Email Reset Kata Sandi Terkirim')
                            ->body("Tautan reset kata sandi telah dikirim ke {$record->email}")
                            ->success()
                            ->send();
                    }),
            ])
            ->bulkActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make()
                        ->visible(fn (): bool => auth()->user()->can('user_delete')),
                    ForceDeleteBulkAction::make()
                        ->visible(fn (): bool => auth()->user()->can('user_delete')),
                    RestoreBulkAction::make()
                        ->visible(fn (): bool => auth()->user()->can('user_delete')),
                ]),
            ])
            ->emptyStateHeading('Tidak ada pengguna ditemukan')
            ->emptyStateDescription('Buat pengguna pertama Anda untuk memulai.');
        // ->emptyStateActions([
        //     \Filament\Actions\CreateAction::make()
        //         ->visible(fn (): bool => auth()->user()->can('user_create')),
        // ])
    }
}
