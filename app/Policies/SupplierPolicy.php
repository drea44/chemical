<?php

namespace App\Policies;

use App\Models\Supplier;
use App\Models\User;

class SupplierPolicy
{
    public function viewAny(User $user): bool
    {
        return true;
    }

    public function view(User $user, ?Supplier $supplier = null): bool
    {
        return true;
    }

    public function create(User $user): bool
    {
        return in_array($user->role, ['ADMIN', 'STOCK_MANAGER']);
    }

    public function update(User $user, ?Supplier $supplier = null): bool
    {
        return in_array($user->role, ['ADMIN', 'STOCK_MANAGER']);
    }

    public function delete(User $user, ?Supplier $supplier = null): bool
    {
        return $user->role === 'ADMIN';
    }
}
