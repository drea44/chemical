<?php

namespace App\Policies;

use App\Models\AuditLog;
use App\Models\User;

class AuditLogPolicy
{

    public function viewAny(User $user): bool
    {
        return in_array($user->role, ['ADMIN', 'AUDITOR']);
    }
}

