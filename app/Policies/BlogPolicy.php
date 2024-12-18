<?php

namespace App\Policies;

use App\Enums\Permission;
use App\Models\Blog;
use App\Models\User;

class BlogPolicy
{
    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(User $user): bool
    {
        return $user->hasPermissionTo(Permission::BLOGS_VIEW);
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, Blog $model): bool
    {
        return true;
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user): bool
    {
        return $user->hasPermissionTo(Permission::BLOGS_CREATE);
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, Blog $model): bool
    {
        if ($user->id === $model->id) {
            return true;
        }

        return $user->hasPermissionTo(Permission::BLOGS_UPDATE);
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, Blog $model): bool
    {
        return $user->hasPermissionTo(Permission::BLOGS_DELETE);
    }

    /**
     * Determine whether the user can restore the model.
     */
    public function restore(User $user, Blog $model): bool
    {
        return false;
    }

    /**
     * Determine whether the user can permanently delete the model.
     */
    public function forceDelete(User $user, Blog $model): bool
    {
        return $user->hasPermissionTo(Permission::BLOGS_DELETE);
    }
}
