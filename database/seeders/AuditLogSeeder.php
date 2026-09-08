<?php

namespace Database\Seeders;

use App\Models\AuditLog;
use App\Models\User;
use App\Models\Chemical;
use Illuminate\Database\Seeder;

class AuditLogSeeder extends Seeder
{
    public function run(): void
    {
        $admin   = User::where('email', 'admin@example.com')->first();
        $manager = User::where('email', 'manager@example.com')->first();
        $auditor = User::where('email', 'auditor@example.com')->first();
        $viewer  = User::where('email', 'viewer@example.com')->first();

        $chemicals = Chemical::take(5)->get();

        $logs = [
            [
                'user_id'     => $admin->id,
                'action'      => 'login',
                'module'      => 'Auth',
                'record_type' => 'User',
                'record_id'   => $admin->id,
                'old_values'  => null,
                'new_values'  => ['email' => $admin->email],
                'ip_address'  => '192.168.1.10',
                'user_agent'  => 'Mozilla/5.0 (Windows NT 10.0; Win64; x64)',
                'status'      => 'success',
                'created_at'  => now()->subHours(2),
            ],
            [
                'user_id'     => $manager->id,
                'action'      => 'login',
                'module'      => 'Auth',
                'record_type' => 'User',
                'record_id'   => $manager->id,
                'old_values'  => null,
                'new_values'  => ['email' => $manager->email],
                'ip_address'  => '192.168.1.11',
                'user_agent'  => 'Mozilla/5.0 (Windows NT 10.0; Win64; x64)',
                'status'      => 'success',
                'created_at'  => now()->subHours(5),
            ],
            [
                'user_id'     => $admin->id,
                'action'      => 'created',
                'module'      => 'Chemical',
                'record_type' => 'Chemical',
                'record_id'   => $chemicals[0]->id ?? 1,
                'old_values'  => null,
                'new_values'  => ['chemical_name' => $chemicals[0]->chemical_name ?? 'Acetone', 'current_stock' => 45.5],
                'ip_address'  => '192.168.1.10',
                'user_agent'  => 'Mozilla/5.0',
                'status'      => 'success',
                'created_at'  => now()->subDays(3),
            ],
            [
                'user_id'     => $manager->id,
                'action'      => 'stock_in',
                'module'      => 'Stock',
                'record_type' => 'StockTransaction',
                'record_id'   => 1,
                'old_values'  => ['stock' => 40.0],
                'new_values'  => ['stock' => 45.5, 'quantity_added' => 5.5],
                'ip_address'  => '192.168.1.11',
                'user_agent'  => 'Mozilla/5.0',
                'status'      => 'success',
                'created_at'  => now()->subDays(2),
            ],
            [
                'user_id'     => $manager->id,
                'action'      => 'stock_out',
                'module'      => 'Stock',
                'record_type' => 'StockTransaction',
                'record_id'   => 2,
                'old_values'  => ['stock' => 10.0],
                'new_values'  => ['stock' => 8.0, 'quantity_removed' => 2.0],
                'ip_address'  => '192.168.1.11',
                'user_agent'  => 'Mozilla/5.0',
                'status'      => 'success',
                'created_at'  => now()->subDays(1),
            ],
            [
                'user_id'     => $admin->id,
                'action'      => 'updated',
                'module'      => 'Chemical',
                'record_type' => 'Chemical',
                'record_id'   => $chemicals[1]->id ?? 2,
                'old_values'  => ['minimum_stock' => 8.0],
                'new_values'  => ['minimum_stock' => 10.0],
                'ip_address'  => '192.168.1.10',
                'user_agent'  => 'Mozilla/5.0',
                'status'      => 'success',
                'created_at'  => now()->subDays(5),
            ],
            [
                'user_id'     => $auditor->id,
                'action'      => 'login',
                'module'      => 'Auth',
                'record_type' => 'User',
                'record_id'   => $auditor->id,
                'old_values'  => null,
                'new_values'  => ['email' => $auditor->email],
                'ip_address'  => '192.168.1.15',
                'user_agent'  => 'Mozilla/5.0 (Macintosh; Intel Mac OS X)',
                'status'      => 'success',
                'created_at'  => now()->subDays(1),
            ],
            [
                'user_id'     => $manager->id,
                'action'      => 'adjusted',
                'module'      => 'Stock',
                'record_type' => 'StockAdjustment',
                'record_id'   => 1,
                'old_values'  => ['previous_stock' => 3.0],
                'new_values'  => ['adjusted_stock' => 2.5, 'difference' => -0.5, 'reason' => 'Evaporation allowance'],
                'ip_address'  => '192.168.1.11',
                'user_agent'  => 'Mozilla/5.0',
                'status'      => 'success',
                'created_at'  => now()->subDays(4),
            ],
            [
                'user_id'     => $viewer->id,
                'action'      => 'login',
                'module'      => 'Auth',
                'record_type' => 'User',
                'record_id'   => $viewer->id,
                'old_values'  => null,
                'new_values'  => ['email' => $viewer->email],
                'ip_address'  => '192.168.1.20',
                'user_agent'  => 'Mozilla/5.0 (X11; Linux x86_64)',
                'status'      => 'success',
                'created_at'  => now()->subDays(2),
            ],
            [
                'user_id'     => $admin->id,
                'action'      => 'created',
                'module'      => 'User',
                'record_type' => 'User',
                'record_id'   => $viewer->id,
                'old_values'  => null,
                'new_values'  => ['name' => $viewer->name, 'role' => 'VIEWER', 'department' => $viewer->department],
                'ip_address'  => '192.168.1.10',
                'user_agent'  => 'Mozilla/5.0',
                'status'      => 'success',
                'created_at'  => now()->subDays(10),
            ],
            [
                'user_id'     => $admin->id,
                'action'      => 'updated',
                'module'      => 'Settings',
                'record_type' => 'SystemSetting',
                'record_id'   => null,
                'old_values'  => ['expiry_warning_days' => 14],
                'new_values'  => ['expiry_warning_days' => 30],
                'ip_address'  => '192.168.1.10',
                'user_agent'  => 'Mozilla/5.0',
                'status'      => 'success',
                'created_at'  => now()->subDays(7),
            ],
            [
                'user_id'     => null,
                'action'      => 'login',
                'module'      => 'Auth',
                'record_type' => 'User',
                'record_id'   => null,
                'old_values'  => null,
                'new_values'  => ['email' => 'unknown@attempt.com'],
                'ip_address'  => '203.45.67.89',
                'user_agent'  => 'curl/7.68.0',
                'status'      => 'failed',
                'created_at'  => now()->subHours(12),
            ],
        ];

        foreach ($logs as $log) {
            AuditLog::create($log);
        }
    }
}
