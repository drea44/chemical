<?php

namespace App\Policies;

use App\Models\User;

class StockPolicy
{
    public function stockIn(User $user): bool
    {
        return in_array($user->role, ['ADMIN', 'STOCK_MANAGER']);
    }

    public function stockOut(User $user): bool
    {
        return in_array($user->role, ['ADMIN', 'STOCK_MANAGER']);
    }

    public function adjust(User $user): bool
    {
        return in_array($user->role, ['ADMIN', 'STOCK_MANAGER']);
    }
}
