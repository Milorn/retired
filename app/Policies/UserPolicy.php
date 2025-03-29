<?php

namespace App\Policies;

use App\Enums\UserType;
use App\Models\User;

class UserPolicy
{
    public function viewAny(User $user): bool
    {
        if ($user->type == UserType::Admin) {
            return true;
        }

        if ($user->type == UserType::Agent) {
            return true;
        }

        return false;
    }

    public function view(User $user, User $model): bool
    {
        if ($user->type == UserType::Admin) {
            return true;
        }

        if ($user->type == UserType::Agent && $model->type == UserType::Retiree) {
            return true;
        }

        return false;
    }

    public function create(User $user): bool
    {
        if ($user->type == UserType::Admin) {
            return true;
        }

        return false;
    }

    public function update(User $user, User $model): bool
    {
        if ($user->type == UserType::Admin) {
            return true;
        }

        if ($user->type == UserType::Agent && $model->type == UserType::Retiree) {
            return true;
        }

        return false;
    }

    public function delete(User $user, User $model): bool
    {
        if ($user->type == UserType::Admin) {
            return true;
        }

        return false;
    }
}
