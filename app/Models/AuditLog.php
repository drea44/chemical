<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AuditLog extends Model
{
    public $timestamps = false;

    protected $fillable = [
        'user_id', 'action', 'module', 'record_type', 'record_id',
        'old_values', 'new_values', 'ip_address', 'user_agent', 'status',
    ];

    protected $casts = [
        'old_values' => 'array',
        'new_values' => 'array',
        'created_at' => 'datetime',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function getActionLabelAttribute(): string
    {
        return match($this->action) {
            'created'   => 'Created',
            'updated'   => 'Updated',
            'deleted'   => 'Deleted',
            'login'     => 'Login',
            'logout'    => 'Logout',
            'stock_in'  => 'Stock In',
            'stock_out' => 'Stock Out',
            'adjusted'  => 'Adjustment',
            default     => ucfirst($this->action),
        };
    }

    public function getActionColorAttribute(): string
    {
        return match($this->action) {
            'created'   => 'green',
            'updated'   => 'blue',
            'deleted'   => 'red',
            'login'     => 'green',
            'logout'    => 'gray',
            'stock_in'  => 'green',
            'stock_out' => 'orange',
            'adjusted'  => 'yellow',
            default     => 'gray',
        };
    }
}
