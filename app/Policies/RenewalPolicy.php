<?php

namespace App\Policies;

use App\Enums\UserType;
use App\Models\Renewal;
use App\Models\User;

class RenewalPolicy
{
    public function viewAny(User $user): bool
    {
        return true;
    }

    public function view(User $user, Renewal $renewal): bool
    {
        if ($user->type == UserType::Admin) {
            return true;
        } elseif ($user->type == UserType::Agent) {
            return true;
        } elseif ($user->type == UserType::Retiree && $renewal->retiree_id == $user->retiree->id) {
            return true;
        }

        return false;
    }

    public function create(User $user): bool
    {
        return $user->type == UserType::Retiree;
    }

    public function update(User $user, Renewal $renewal): bool
    {
        if ($user->type == UserType::Admin) {
            return true;
        } elseif ($user->type == UserType::Agent) {
            return true;
        }

        return false;
    }

    public function delete(User $user, Renewal $renewal): bool
    {
        if ($user->type == UserType::Admin) {
            return true;
        } elseif ($user->type == UserType::Agent) {
            return true;
        }

        return false;
    }
}
