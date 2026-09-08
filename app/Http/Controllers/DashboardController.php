<?php

namespace App\Http\Controllers;

use App\Models\Chemical;
use App\Models\StockTransaction;
use App\Services\ChemicalService;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    public function __construct(private ChemicalService $chemicalService) {}

    public function index()
    {
        // Stat cards
        $stats = $this->chemicalService->getDashboardStats();

        // Stock movement last 7 days (for bar chart)
        $stockMovement = $this->getStockMovementLast7Days();

        // Stock status distribution (for doughnut chart) - 1 single query
        $statusCounts = Chemical::selectRaw('status, count(*) as count')
            ->groupBy('status')
            ->pluck('count', 'status');

        $stockStatus = [
            'safe'          => (int) ($statusCounts['SAFE'] ?? 0),
            'low'           => (int) ($statusCounts['LOW'] ?? 0),
            'critical'      => (int) ($statusCounts['CRITICAL'] ?? 0),
            'expired'       => (int) ($statusCounts['EXPIRED'] ?? 0),
            'expiring_soon' => (int) ($statusCounts['EXPIRING_SOON'] ?? 0),
        ];

        // Recent activity
        $recentActivity = StockTransaction::with(['chemical', 'performer'])
            ->orderBy('transaction_date', 'desc')
            ->take(10)
            ->get();

        // Critical alerts
        $criticalAlerts = Chemical::whereIn('status', ['CRITICAL', 'EXPIRED', 'EXPIRING_SOON'])
            ->with('location')
            ->orderBy('status')
            ->take(8)
            ->get();

        // Notification counts
        $notifications = $this->chemicalService->getNotificationCounts();

        return view('dashboard.index', compact(
            'stats', 'stockMovement', 'stockStatus',
            'recentActivity', 'criticalAlerts', 'notifications'
        ));
    }

    private function getStockMovementLast7Days(): array
    {
        $startDate = now()->subDays(6)->startOfDay();

        $txAggregates = StockTransaction::where('transaction_date', '>=', $startDate)
            ->whereIn('transaction_type', ['STOCK_IN', 'STOCK_OUT'])
            ->selectRaw('DATE(transaction_date) as tx_date, transaction_type, SUM(quantity) as total_qty')
            ->groupBy('tx_date', 'transaction_type')
            ->get();

        $inboundMap  = [];
        $outboundMap = [];

        foreach ($txAggregates as $row) {
            $val = (float) $row->total_qty;
            if ($row->transaction_type === 'STOCK_IN') {
                $inboundMap[$row->tx_date] = $val;
            } elseif ($row->transaction_type === 'STOCK_OUT') {
                $outboundMap[$row->tx_date] = $val;
            }
        }

        $days     = [];
        $inbound  = [];
        $outbound = [];

        for ($i = 6; $i >= 0; $i--) {
            $date = now()->subDays($i)->toDateString();
            $days[] = now()->subDays($i)->format('D, d M');
            $inbound[]  = $inboundMap[$date] ?? 0.0;
            $outbound[] = $outboundMap[$date] ?? 0.0;
        }

        return compact('days', 'inbound', 'outbound');
    }
}
