<?php

namespace App\Policies;

use App\Models\AuditLog;
use App\Models\User;

class AuditLogPolicy
{
    /**
     * Only ADMIN and AUDITOR can view audit logs.
     */
    public function viewAny(User $user): bool
    {
        return in_array($user->role, ['ADMIN', 'AUDITOR']);
    }
}
