<?php

namespace App\Policies;

use App\Models\User;

class UserPolicy
{
    public function viewAny(User $user): bool
    {
        return $user->role === 'ADMIN';
    }

    public function create(User $user): bool
    {
        return $user->role === 'ADMIN';
    }

    public function update(User $user, User $target): bool
    {
        return $user->role === 'ADMIN';
    }

    public function delete(User $user, User $target): bool
    {
        return $user->role === 'ADMIN' && $user->id !== $target->id;
    }
}
