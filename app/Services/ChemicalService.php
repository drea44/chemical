<?php

namespace App\Services;

use App\Models\Chemical;
use App\Models\SystemSetting;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class ChemicalService
{

    public function generateChemicalCode(): string
    {

        $lastCode = Chemical::where('chemical_code', 'like', 'CHM-%')
            ->orderByRaw("CAST(REPLACE(chemical_code, 'CHM-', '') AS UNSIGNED) DESC")
            ->value('chemical_code');

        $next = 1;
        if ($lastCode && preg_match('/^CHM-(\d+)$/', $lastCode, $m)) {
            $next = (int) $m[1] + 1;
        }

        $attempts = 0;
        while ($attempts < 10) {
            $candidate = 'CHM-' . str_pad($next, 4, '0', STR_PAD_LEFT);
            if (!Chemical::where('chemical_code', $candidate)->exists()) {
                return $candidate;
            }
            $next++;
            $attempts++;
        }

        return 'CHM-' . now()->format('YmdHis') . '-' . strtoupper(\Illuminate\Support\Str::random(4));
    }

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

