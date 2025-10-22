<?php

namespace App\Filament\Resources\Users\Pages;

use App\Filament\Resources\Users\UserResource;
use App\Models\UserDetail;
use Filament\Actions\DeleteAction;
use Filament\Actions\ForceDeleteAction;
use Filament\Actions\RestoreAction;
use Filament\Resources\Pages\EditRecord;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Log;

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
        // Debug: Log incoming data
        Log::info('Edit User handleRecordUpdate - Raw Data:', [
            'data_keys' => array_keys($data),
            'roles_raw' => $data['roles'] ?? [],
            'UserDetail_keys' => array_keys($data['UserDetail'] ?? []),
            'record_id' => $record->id,
        ]);

        // Extract user details data and roles (handle both UserDetail and userDetail keys)
        $userDetailData = $data['UserDetail'] ?? $data['userDetail'] ?? [];
        $roles = $data['roles'] ?? [];

        Log::info('Edit User handleRecordUpdate - Extracted Data:', [
            'userDetail_count' => count($userDetailData),
            'roles_count' => count($roles),
            'roles_values' => $roles,
        ]);

        // Remove user details and roles from user data
        unset($data['UserDetail'], $data['userDetail'], $data['roles']);

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

        // Note: Roles are automatically synced by Filament's CheckboxCards component
        // We don't need manual syncRoles() here as it causes conflicts

        return $record;
    }

    /**
     * Mutate the form data before filling the form
     */
    protected function mutateFormDataBeforeFill(array $data): array
    {
        $record = $this->getRecord();

        // Ensure relationships are loaded
        $record->load(['userDetail', 'roles']);

        // Add user details data to form with proper key mapping
        if ($record->userDetail) {
            $userDetailData = $record->userDetail->toArray();
            // Transform to match form field naming convention (UserDetail.*)
            $data['UserDetail'] = $userDetailData;
        } else {
            $data['UserDetail'] = [];
        }

        // Add user roles to form
        $rolesArray = $record->roles->pluck('name')->toArray();
        $data['roles'] = $rolesArray;

        // Debug: Log what we're setting
        Log::info('mutateFormDataBeforeFill - Setting data:', [
            'roles' => $rolesArray,
            'userDetail' => $data['UserDetail'],
            'record_id' => $record->id,
        ]);

        return $data;
    }

    /**
     * Handle form mount to ensure data is properly loaded
     */
    public function mount(int|string $record): void
    {
        parent::mount($record);

        // Refresh the record with relationships
        $this->record->load(['userDetail', 'roles']);

        // Ensure roles data is properly set as array for CheckboxCards
        $rolesArray = $this->record->roles->pluck('name')->toArray();
        $this->data['roles'] = $rolesArray;

        // Ensure UserDetail data is properly set
        $this->data['UserDetail'] = $this->record->userDetail?->toArray() ?? [];

        // Debug: Log the data to verify
        Log::info('Edit User Mount Data:', [
            'roles' => $this->data['roles'],
            'userDetail' => $this->data['UserDetail'],
            'record_id' => $this->record->id,
        ]);
    }

    /**
     * Get the redirect URL after saving
     */
    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }
}
