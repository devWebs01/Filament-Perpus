<?php

namespace App\Filament\Resources\Users\Pages;

use App\Filament\Resources\Users\UserResource;
use App\Models\UserDetail;
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
            DeleteAction::make(),
            ForceDeleteAction::make(),
            RestoreAction::make(),
        ];
    }

    /**
     * Handle the update of user with associated details and roles
     */
    protected function handleRecordUpdate(Model $record, array $data): Model
    {
        // Extract user details data and roles
        $userDetailData = $data['userDetail'] ?? [];
        $roles = $data['roles'] ?? [];

        // Remove user details and roles from user data
        unset($data['userDetail'], $data['roles']);

        // Update the user
        $record->update($data);

        // Update or create user details
        if (! empty($userDetailData)) {
            $userDetail = $record->userDetail;
            if ($userDetail) {
                $userDetail->update($userDetailData);
            } else {
                $userDetailData['user_id'] = $record->id;
                UserDetail::create($userDetailData);
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
        if ($record->userDetail) {
            $data['userDetail'] = $record->userDetail->toArray();
        }

        // Add user roles to form
        $data['roles'] = $record->roles->pluck('name')->toArray();

        return $data;
    }
}
