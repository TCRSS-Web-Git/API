<?php

namespace App\Policies;

use App\Enums\Permission;
use App\Models\Career;
use App\Models\User;

class CareerPolicy
{
    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(User $user): bool
    {
        return $user->hasPermissionTo(Permission::CAREERS_VIEW);
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, Career $career): bool
    {
        return true;
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user): bool
    {
        return $user->hasPermissionTo(Permission::CAREERS_CREATE);
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, Career $career): bool
    {
        return $user->hasPermissionTo(Permission::CAREERS_UPDATE);
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, Career $career): bool
    {
        return $user->hasPermissionTo(Permission::CAREERS_DELETE);

    }

    /**
     * Determine whether the user can restore the model.
     */
    public function restore(User $user, Career $career): bool
    {
        return false;
    }

    /**
     * Determine whether the user can permanently delete the model.
     */
    public function forceDelete(User $user, Career $career): bool
    {
        return $user->hasPermissionTo(Permission::CAREERS_DELETE);
    }
}
