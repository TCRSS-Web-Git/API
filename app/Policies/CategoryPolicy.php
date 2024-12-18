<?php

namespace App\Policies;

use App\Enums\CategoryType;
use App\Enums\Permission;
use App\Models\Category;
use App\Models\User;

class CategoryPolicy
{
    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(User $user): bool
    {
        return true;
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, Category $model): bool
    {
        return true;
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user, CategoryType $type): bool
    {
        if ($type === CategoryType::BLOG) {
            return $user->hasPermissionTo(Permission::BLOG_CATEGORIES_CREATE);
        }

        return false;
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, Category $model): bool
    {
        if ($model->type === CategoryType::BLOG) {
            return $user->hasPermissionTo(Permission::BLOG_CATEGORIES_UPDATE);
        }

        return false;
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, Category $model): bool
    {
        if ($model->type === CategoryType::BLOG) {
            return $user->hasPermissionTo(Permission::BLOG_CATEGORIES_DELETE);
        }

        return false;
    }

    /**
     * Determine whether the user can restore the model.
     */
    public function restore(User $user, Category $model): bool
    {
        return false;
    }

    /**
     * Determine whether the user can permanently delete the model.
     */
    public function forceDelete(User $user, Category $model): bool
    {
        return false;
    }
}
