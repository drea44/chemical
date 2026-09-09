<?php

namespace App\Services;

use App\Models\Chemical;
use App\Models\SystemSetting;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class ChemicalService
{
    /**
     * Generate a unique chemical code.
     */
    public function generateChemicalCode(): string
    {
        $last = Chemical::orderBy('id', 'desc')->first();
        $next = $last ? ($last->id + 1) : 1;
        return 'CHM-' . str_pad($next, 4, '0', STR_PAD_LEFT);
    }

    /**
     * Update status for all chemicals based on current settings (BUG-11 fix: chunking).
     */
    public function refreshAllStatuses(): void
    {
        $expiryDays = (int) SystemSetting::getValue('expiry_warning_days', 30);
        Chemical::chunk(100, function ($chemicals) use ($expiryDays) {
            foreach ($chemicals as $chemical) {
                $chemical->updateStatus($expiryDays);
                $chemical->saveQuietly();
            }
        });
    }

    /**
     * Get notification counts for topbar badge (BUG-23 fix: single aggregated query).
     */
    public function getNotificationCounts(): array
    {
        $counts = Chemical::selectRaw('status, count(*) as total')
            ->whereIn('status', ['LOW', 'CRITICAL', 'EXPIRING_SOON', 'EXPIRED'])
            ->groupBy('status')
            ->pluck('total', 'status');

        $low          = (int) ($counts['LOW'] ?? 0);
        $critical     = (int) ($counts['CRITICAL'] ?? 0);
        $expiringSoon = (int) ($counts['EXPIRING_SOON'] ?? 0);
        $expired      = (int) ($counts['EXPIRED'] ?? 0);

        return [
            'low_stock'     => $low,
            'critical'      => $critical,
            'expiring_soon' => $expiringSoon,
            'expired'       => $expired,
            'total'         => $low + $critical + $expiringSoon + $expired,
        ];
    }

    /**
     * Get dashboard statistics (optimized to single group-by query + sum).
     */
    public function getDashboardStats(): array
    {
        $statusCounts = Chemical::selectRaw('status, count(*) as total')
            ->groupBy('status')
            ->pluck('total', 'status');

        return [
            'total_chemicals'  => (int) $statusCounts->sum(),
            'total_stock'      => (float) Chemical::sum('current_stock'),
            'low_stock'        => (int) ($statusCounts['LOW'] ?? 0),
            'critical_stock'   => (int) ($statusCounts['CRITICAL'] ?? 0),
            'expiring_soon'    => (int) ($statusCounts['EXPIRING_SOON'] ?? 0),
            'expired'          => (int) ($statusCounts['EXPIRED'] ?? 0),
            'safe'             => (int) ($statusCounts['SAFE'] ?? 0),
        ];
    }
}
