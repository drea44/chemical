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
     *
     * Uses a sequential number based on max existing code, with a uniqueness loop
     * to handle gaps from deleted records. Falls back to timestamp+random suffix
     * if no unique sequential code can be found after reasonable attempts.
     */
    public function generateChemicalCode(): string
    {
        // Derive next sequence from the highest CHM-XXXX code in the database
        $lastCode = Chemical::where('chemical_code', 'like', 'CHM-%')
            ->orderByRaw("CAST(REPLACE(chemical_code, 'CHM-', '') AS UNSIGNED) DESC")
            ->value('chemical_code');

        $next = 1;
        if ($lastCode && preg_match('/^CHM-(\d+)$/', $lastCode, $m)) {
            $next = (int) $m[1] + 1;
        }

        // Loop to ensure uniqueness (handles gaps from deleted records)
        $attempts = 0;
        while ($attempts < 10) {
            $candidate = 'CHM-' . str_pad($next, 4, '0', STR_PAD_LEFT);
            if (!Chemical::where('chemical_code', $candidate)->exists()) {
                return $candidate;
            }
            $next++;
            $attempts++;
        }

        // Fallback: timestamp + random suffix (collision-safe by construction)
        return 'CHM-' . now()->format('YmdHis') . '-' . strtoupper(\Illuminate\Support\Str::random(4));
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
