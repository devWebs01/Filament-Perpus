<?php

namespace App\Filament\Resources\Users\Pages;

use App\Filament\Resources\Users\UserResource;
use App\Models\UserDetail;
use Filament\Resources\Pages\CreateRecord;
use Illuminate\Database\Eloquent\Model;

/**
 * Create User Page
 *
 * Handles user creation with automatic creation of associated user details
 * and role assignment for the library system.
 */
class CreateUser extends CreateRecord
{
    protected static string $resource = UserResource::class;

    /**
     * Handle the creation of user with associated details and roles
     */
    protected function handleRecordCreation(array $data): Model
    {
        // Extract user details data
        $userDetailData = $data['userDetail'] ?? [];
        $roles = $data['roles'] ?? [];

        // Remove user details and roles from user data
        unset($data['userDetail'], $data['roles']);

        // Create the user
        $user = static::getModel()::create($data);

        // Create user details if provided
        if (! empty($userDetailData)) {
            $userDetailData['user_id'] = $user->id;
            UserDetail::create($userDetailData);
        }

        // Assign roles to the user
        if (! empty($roles)) {
            $user->syncRoles($roles);
        }

        return $user;
    }

    /**
     * Get the redirect URL after successful user creation
     */
    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }

    /**
     * Check if the current user can create users
     */
    public static function canAccess(array $parameters = []): bool
    {
        return auth()->user()->can('user_create');
    }
}
