<?php

namespace App\Policies;

use App\Models\AwardImage;
use App\Models\User;

class AwardImagePolicy
{
    public function viewAny(?User $user): bool
    {
        return true;
    }

    public function create(User $user): bool
    {
        return true;
    }

    public function update(User $user): bool
    {
        return true;
    }

    public function delete(User $user, AwardImage $awardImage): bool
    {
        return true;
    }

    public function reorder(User $user): bool
    {
        return true;
    }
}
