<?php

namespace App\Policies;

use App\Models\Popup;
use App\Models\User;

class PopupPolicy
{
    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(?User $user): bool
    {
        return true;
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, Popup $popup): bool
    {
        return true;
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user): bool
    {
        return true;
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, Popup $popup): bool
    {
        return true;
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, Popup $popup): bool
    {
        return true;
    }

    /**
     * Determine whether the user can permanently delete the model.
     */
    public function reorder(User $user): bool
    {
        return true;
    }
}
