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
     * Update status for all chemicals based on current settings.
     */
    public function refreshAllStatuses(): void
    {
        $expiryDays = (int) SystemSetting::getValue('expiry_warning_days', 30);
        Chemical::all()->each(function (Chemical $chemical) use ($expiryDays) {
            $chemical->updateStatus($expiryDays);
            $chemical->saveQuietly();
        });
    }

    /**
     * Get notification counts for topbar badge.
     */
    public function getNotificationCounts(): array
    {
        return [
            'low_stock'     => Chemical::where('status', 'LOW')->count(),
            'critical'      => Chemical::where('status', 'CRITICAL')->count(),
            'expiring_soon' => Chemical::where('status', 'EXPIRING_SOON')->count(),
            'expired'       => Chemical::where('status', 'EXPIRED')->count(),
            'total'         => Chemical::whereIn('status', ['LOW', 'CRITICAL', 'EXPIRING_SOON', 'EXPIRED'])->count(),
        ];
    }

    /**
     * Get dashboard statistics.
     */
    public function getDashboardStats(): array
    {
        return [
            'total_chemicals'  => Chemical::count(),
            'total_stock'      => Chemical::sum('current_stock'),
            'low_stock'        => Chemical::whereIn('status', ['LOW'])->count(),
            'critical_stock'   => Chemical::where('status', 'CRITICAL')->count(),
            'expiring_soon'    => Chemical::where('status', 'EXPIRING_SOON')->count(),
            'expired'          => Chemical::where('status', 'EXPIRED')->count(),
            'safe'             => Chemical::where('status', 'SAFE')->count(),
        ];
    }
}
