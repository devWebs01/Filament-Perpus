<?php

namespace App\Filament\Resources\Users\Pages;

use App\Filament\Resources\Users\UserResource;
use App\Models\UserDetails;
use Filament\Actions\DeleteAction;
use Filament\Actions\ForceDeleteAction;
use Filament\Actions\RestoreAction;
use Filament\Resources\Pages\EditRecord;
use Illuminate\Database\Eloquent\Model;

/**
 * Edit User Page
 *
 * Handles user editing with updates to user details and role assignments.
 * Includes permission checks for all actions.
 */
class EditUser extends EditRecord
{
    protected static string $resource = UserResource::class;

    /**
     * Define header actions with permission checks
     */
    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make()
                ->visible(fn (): bool => auth()->user()->can('user_delete')),
            ForceDeleteAction::make()
                ->visible(fn (): bool => auth()->user()->can('user_delete')),
            RestoreAction::make()
                ->visible(fn (): bool => auth()->user()->can('user_delete')),
        ];
    }

    /**
     * Handle the update of user with associated details and roles
     */
    protected function handleRecordUpdate(Model $record, array $data): Model
    {
        // Extract user details data and roles
        $userDetailsData = $data['userDetails'] ?? [];
        $roles = $data['roles'] ?? [];

        // Remove user details and roles from user data
        unset($data['userDetails'], $data['roles']);

        // Update the user
        $record->update($data);

        // Update or create user details
        if (! empty($userDetailsData)) {
            $userDetails = $record->userDetails;
            if ($userDetails) {
                $userDetails->update($userDetailsData);
            } else {
                $userDetailsData['user_id'] = $record->id;
                UserDetails::create($userDetailsData);
            }
        }

        // Update user roles
        if (! empty($roles)) {
            $record->syncRoles($roles);
        } else {
            $record->syncRoles([]);
        }

        return $record;
    }

    /**
     * Mutate the form data before filling the form
     */
    protected function mutateFormDataBeforeFill(array $data): array
    {
        $record = $this->getRecord();

        // Add user details data to form
        if ($record->userDetails) {
            $data['userDetails'] = $record->userDetails->toArray();
        }

        // Add user roles to form
        $data['roles'] = $record->roles->pluck('name')->toArray();

        return $data;
    }

    /**
     * Check if the current user can edit this user
     */
    public static function canAccess(array $parameters = []): bool
    {
        return auth()->user()->can('user_update');
    }
}
