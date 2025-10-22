<?php

declare(strict_types=1);

namespace App\Policies;

use App\Models\Transaction;
use Illuminate\Auth\Access\HandlesAuthorization;
use Illuminate\Foundation\Auth\User as AuthUser;

class TransactionPolicy
{
    use HandlesAuthorization;

    public function viewAny(AuthUser $authUser): bool
    {
        return true;
    }

    public function view(AuthUser $authUser, Transaction $transaction): bool
    {
        return true;
    }

    public function create(AuthUser $authUser): bool
    {
        return true;
    }

    public function update(AuthUser $authUser, Transaction $transaction): bool
    {
        return true;
    }

    public function delete(AuthUser $authUser, Transaction $transaction): bool
    {
        return true;
    }

    public function restore(AuthUser $authUser, Transaction $transaction): bool
    {
        return true;
    }

    public function forceDelete(AuthUser $authUser, Transaction $transaction): bool
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

    public function replicate(AuthUser $authUser, Transaction $transaction): bool
    {
        return true;
    }

    public function reorder(AuthUser $authUser): bool
    {
        return true;
    }
}
