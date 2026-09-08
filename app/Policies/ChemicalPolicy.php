<?php

namespace App\Policies;

use App\Models\Chemical;
use App\Models\User;

class ChemicalPolicy
{
    public function viewAny(User $user): bool
    {
        return true; // All roles can view chemical list
    }

    public function view(User $user, ?Chemical $chemical = null): bool
    {
        return true;
    }

    public function create(User $user): bool
    {
        return in_array($user->role, ['ADMIN', 'STOCK_MANAGER']);
    }

    public function update(User $user, ?Chemical $chemical = null): bool
    {
        return in_array($user->role, ['ADMIN', 'STOCK_MANAGER']);
    }

    public function delete(User $user, ?Chemical $chemical = null): bool
    {
        return $user->role === 'ADMIN';
    }
}
