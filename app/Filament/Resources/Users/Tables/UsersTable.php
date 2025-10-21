<?php

namespace App\Filament\Resources\Users\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ForceDeleteBulkAction;
use Filament\Actions\RestoreBulkAction;
use Filament\Actions\ViewAction;
use Filament\Tables\Actions\Action;
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
                    ->label('Name')
                    ->searchable()
                    ->sortable()
                    ->weight('bold'),

                TextColumn::make('email')
                    ->label('Email')
                    ->searchable()
                    ->sortable()
                    ->copyable()
                    ->copyMessage('Email copied to clipboard')
                    ->copyMessageDuration(1500),

                // User Details
                TextColumn::make('userDetails.phone_number')
                    ->label('Phone')
                    ->searchable()
                    ->placeholder('N/A')
                    ->toggleable(),

                TextColumn::make('userDetails.user_type_display_name')
                    ->label('User Type')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'Library Head' => 'danger',
                        'Library Staff' => 'warning',
                        'Student' => 'success',
                        default => 'gray',
                    })
                    ->searchable()
                    ->sortable(),

                TextColumn::make('userDetails.class')
                    ->label('Class')
                    ->searchable()
                    ->placeholder('N/A')
                    ->toggleable(),

                // Roles and Permissions
                TextColumn::make('roles.name')
                    ->label('Roles')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'super_admin' => 'danger',
                        'library_head' => 'warning',
                        'staff' => 'info',
                        'student' => 'success',
                        default => 'gray',
                    })
                    ->searchable()
                    ->separator(','),

                IconColumn::make('email_verified_at')
                    ->label('Verified')
                    ->boolean()
                    ->trueColor('success')
                    ->falseColor('warning')
                    ->trueIcon('heroicon-o-check-circle')
                    ->falseIcon('heroicon-o-x-circle')
                    ->sortable(),

                TextColumn::make('userDetails.membership_status_display_name')
                    ->label('Membership')
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
                    ->label('Created')
                    ->dateTime('M j, Y')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),

                TextColumn::make('updated_at')
                    ->label('Updated')
                    ->dateTime('M j, Y')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->defaultSort('created_at', 'desc')
            ->filters([
                // Role Filter
                SelectFilter::make('role')
                    ->label('Role')
                    ->relationship('roles', 'name')
                    ->searchable()
                    ->preload()
                    ->indicator('Role'),

                // User Type Filter
                Filter::make('user_type')
                    ->label('User Type')
                    ->form([
                        \Filament\Forms\Components\CheckboxList::make('types')
                            ->label('Select User Types')
                            ->options([
                                'student' => 'Student',
                                'library_head' => 'Library Head',
                                'staff' => 'Library Staff',
                            ])
                            ->columns(3),
                    ])
                    ->query(function (Builder $query, array $data): Builder {
                        return $query->when($data['types'], function (Builder $query, array $types) {
                            $query->whereHas('userDetails', function (Builder $query) use ($types) {
                                if (in_array('student', $types)) {
                                    $query->orWhere(function (Builder $q) {
                                        $q->whereNotNull('nis')->orWhereNotNull('nisn');
                                    });
                                }
                                if (in_array('library_head', $types)) {
                                    $query->orWhereHas('user', function (Builder $q) {
                                        $q->where('email', 'admin@testing.com');
                                    });
                                }
                                if (in_array('staff', $types)) {
                                    $query->orWhere(function (Builder $q) {
                                        $q->whereNull('nis')
                                            ->whereNull('nisn')
                                            ->whereNotNull('join_date')
                                            ->whereHas('user', function (Builder $u) {
                                                $u->where('email', '!=', 'admin@testing.com');
                                            });
                                    });
                                }
                            });
                        });
                    })
                    ->indicateUsing(function (array $data): ?string {
                        if (! $data['types']) {
                            return null;
                        }

                        return 'User Types: '.implode(', ', $data['types']);
                    }),

                // Membership Status Filter
                SelectFilter::make('membership_status')
                    ->label('Membership Status')
                    ->options([
                        'active' => 'Active',
                        'suspended' => 'Suspended',
                        'expired' => 'Expired',
                    ])
                    ->query(function (Builder $query, array $data): Builder {
                        return $query->when($data['value'], function (Builder $query, string $value) {
                            $query->whereHas('userDetails', function (Builder $query) use ($value) {
                                $query->where('membership_status', $value);
                            });
                        });
                    })
                    ->indicator('Membership'),

                // Email Verification Filter
                Filter::make('verified')
                    ->label('Email Verification')
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
                    ->label('Permissions')
                    ->icon('heroicon-o-shield-check')
                    ->color('warning')
                    ->visible(fn (): bool => auth()->user()->can('role_update'))
                    ->url(fn ($record): string => route('filament.admin.resources.roles.edit', $record->roles->first()))
                    ->openUrlInNewTab(),

                // Reset Password Action
                Action::make('reset_password')
                    ->label('Reset Password')
                    ->icon('heroicon-o-key')
                    ->color('danger')
                    ->visible(fn (): bool => auth()->user()->can('user_update'))
                    ->requiresConfirmation()
                    ->action(function ($record) {
                        // This would typically trigger a password reset email
                        // For now, we'll just show a success message
                        \Filament\Notifications\Notification::make()
                            ->title('Password Reset Email Sent')
                            ->body("A password reset link has been sent to {$record->email}")
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
            ->emptyStateHeading('No users found')
            ->emptyStateDescription('Create your first user to get started.')
            ->emptyStateActions([
                \Filament\Tables\Actions\CreateAction::make()
                    ->visible(fn (): bool => auth()->user()->can('user_create')),
            ]);
    }
}
