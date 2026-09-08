<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    use HasFactory, Notifiable;

    protected $fillable = [
        'name',
        'email',
        'password',
        'role',
        'department',
        'position',
        'avatar',
        'status',
        'last_login_at',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'last_login_at'     => 'datetime',
            'password'          => 'hashed',
        ];
    }

    // Relationships
    public function stockTransactions()
    {
        return $this->hasMany(StockTransaction::class, 'performed_by');
    }

    public function auditLogs()
    {
        return $this->hasMany(AuditLog::class);
    }

    public function reports()
    {
        return $this->hasMany(Report::class, 'generated_by');
    }

    public function stockAdjustmentsAdjusted()
    {
        return $this->hasMany(StockAdjustment::class, 'adjusted_by');
    }

    public function stockAdjustmentsApproved()
    {
        return $this->hasMany(StockAdjustment::class, 'approved_by');
    }

    // Helpers
    public function isAdmin(): bool
    {
        return $this->role === 'ADMIN';
    }

    public function isStockManager(): bool
    {
        return $this->role === 'STOCK_MANAGER';
    }

    public function isAuditor(): bool
    {
        return $this->role === 'AUDITOR';
    }

    public function isViewer(): bool
    {
        return $this->role === 'VIEWER';
    }

    public function hasRole(string|array $roles): bool
    {
        if (is_string($roles)) {
            return $this->role === $roles;
        }
        return in_array($this->role, $roles);
    }

    public function canManageStock(): bool
    {
        return in_array($this->role, ['ADMIN', 'STOCK_MANAGER']);
    }

    public function getRoleLabelAttribute(): string
    {
        return match($this->role) {
            'ADMIN'         => 'Administrator',
            'STOCK_MANAGER' => 'Stock Manager',
            'AUDITOR'       => 'Auditor',
            'VIEWER'        => 'Viewer',
            default         => $this->role,
        };
    }
}
