<?php

namespace App\Services;

use App\Models\AuditLog;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Request;

class AuditLogService
{
    public static function log(
        string $action,
        string $module,
        ?string $recordType = null,
        ?int $recordId = null,
        ?array $oldValues = null,
        ?array $newValues = null,
        string $status = 'success'
    ): void {
        AuditLog::create([
            'user_id'     => Auth::id(),
            'action'      => $action,
            'module'      => $module,
            'record_type' => $recordType,
            'record_id'   => $recordId,
            'old_values'  => $oldValues,
            'new_values'  => $newValues,
            'ip_address'  => Request::ip(),
            'user_agent'  => Request::userAgent(),
            'status'      => $status,
        ]);
    }

    public static function logLogin(int $userId, bool $success = true): void
    {
        AuditLog::create([
            'user_id'     => $success ? $userId : null,
            'action'      => 'login',
            'module'      => 'Auth',
            'record_type' => 'User',
            'record_id'   => $userId,
            'old_values'  => null,
            'new_values'  => null,
            'ip_address'  => Request::ip(),
            'user_agent'  => Request::userAgent(),
            'status'      => $success ? 'success' : 'failed',
        ]);
    }

    public static function logLogout(int $userId): void
    {
        AuditLog::create([
            'user_id'     => $userId,
            'action'      => 'logout',
            'module'      => 'Auth',
            'record_type' => 'User',
            'record_id'   => $userId,
            'old_values'  => null,
            'new_values'  => null,
            'ip_address'  => Request::ip(),
            'user_agent'  => Request::userAgent(),
            'status'      => 'success',
        ]);
    }

    public static function logCreated(string $module, int $recordId, array $newValues): void
    {
        static::log('created', $module, $module, $recordId, null, $newValues);
    }

    public static function logUpdated(string $module, int $recordId, array $oldValues, array $newValues): void
    {
        static::log('updated', $module, $module, $recordId, $oldValues, $newValues);
    }

    public static function logDeleted(string $module, int $recordId, array $oldValues): void
    {
        static::log('deleted', $module, $module, $recordId, $oldValues, null);
    }

    public static function logStockIn(int $chemicalId, float $before, float $after, float $qty): void
    {
        static::log('stock_in', 'Stock', 'Chemical', $chemicalId, ['stock' => $before], ['stock' => $after, 'quantity_added' => $qty]);
    }

    public static function logStockOut(int $chemicalId, float $before, float $after, float $qty): void
    {
        static::log('stock_out', 'Stock', 'Chemical', $chemicalId, ['stock' => $before], ['stock' => $after, 'quantity_removed' => $qty]);
    }

    public static function logAdjustment(int $chemicalId, float $before, float $after): void
    {
        static::log('adjusted', 'Stock', 'Chemical', $chemicalId, ['stock' => $before], ['stock' => $after, 'difference' => $after - $before]);
    }
}
