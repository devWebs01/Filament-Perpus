<?php

declare(strict_types=1);

namespace App\Policies;

use App\Models\Status;
use Illuminate\Auth\Access\HandlesAuthorization;
use Illuminate\Foundation\Auth\User as AuthUser;

class StatusPolicy
{
    use HandlesAuthorization;

    public function viewAny(AuthUser $authUser): bool
    {
        return true;
    }

    public function view(AuthUser $authUser, Status $status): bool
    {
        return true;
    }

    public function create(AuthUser $authUser): bool
    {
        return true;
    }

    public function update(AuthUser $authUser, Status $status): bool
    {
        return true;
    }

    public function delete(AuthUser $authUser, Status $status): bool
    {
        return true;
    }

    public function restore(AuthUser $authUser, Status $status): bool
    {
        return true;
    }

    public function forceDelete(AuthUser $authUser, Status $status): bool
    {
        return true;
    }

    public function forceDeleteAny(AuthUser $authUser): bool
    {
        return true;
    }

    public function restoreAny(AuthUser $authUser): bool
    {
        return true;
    }

    public function replicate(AuthUser $authUser, Status $status): bool
    {
        return true;
    }

    public function reorder(AuthUser $authUser): bool
    {
        return true;
    }
}
