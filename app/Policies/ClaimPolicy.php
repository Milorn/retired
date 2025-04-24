<?php

namespace App\Policies;

use App\Enums\UserType;
use App\Models\Claim;
use App\Models\User;

class ClaimPolicy
{
    public function viewAny(User $user): bool
    {
        if ($user->type == UserType::Agent) {
            return true;
        } elseif ($user->type == UserType::Retiree) {
            return true;
        }

        return false;
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, Claim $claim): bool
    {
        if ($user->type == UserType::Agent) {
            return true;
        } elseif ($user->type == UserType::Retiree && $claim->retiree_id == $user->retiree->id) {
            return true;
        }

        return false;
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user): bool
    {
        return $user->type == UserType::Retiree;
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, Claim $claim): bool
    {
        if ($user->type == UserType::Agent) {
            return true;
        }

        return false;
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, Claim $claim): bool
    {
        if ($user->type == UserType::Agent) {
            return true;
        }

        return false;
    }
}
