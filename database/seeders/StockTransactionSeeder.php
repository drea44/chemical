<?php

namespace Database\Seeders;

use App\Models\Chemical;
use App\Models\ChemicalLocation;
use App\Models\StockTransaction;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class StockTransactionSeeder extends Seeder
{
    public function run(): void
    {
        $admin   = User::where('email', 'admin@example.com')->first();
        $manager = User::where('email', 'manager@example.com')->first();

        $chemicals = Chemical::with('location')->get();
        $locations = ChemicalLocation::all();

        $transactionTypes = ['STOCK_IN', 'STOCK_OUT', 'STOCK_IN', 'STOCK_OUT', 'ADJUSTMENT'];
        $reasons = [
            'STOCK_IN'   => ['Monthly replenishment order', 'Emergency restock from supplier', 'Transfer from central warehouse', 'Purchase order received', 'Donation from partner lab'],
            'STOCK_OUT'  => ['Used in experiment batch #A1', 'Quality control testing', 'Teaching laboratory use', 'Research project consumption', 'Routine analytical procedure'],
            'ADJUSTMENT' => ['Physical count correction', 'Spillage/breakage loss', 'Evaporation allowance', 'Calibration correction', 'Inventory reconciliation'],
        ];

        $transactions = [];
        $transCounter = 1;

        // Generate 55 transactions for various chemicals over last 7 days (for chart) + older
        foreach ($chemicals->take(15) as $chemical) {
            // Generate 3-4 transactions per chemical
            $numTx = rand(3, 4);
            for ($i = 0; $i < $numTx; $i++) {
                $type = $transactionTypes[array_rand($transactionTypes)];
                $daysAgo = rand(0, 60);
                $stockBefore = max(0, $chemical->current_stock + rand(-5, 5));
                $qty = round(rand(1, 10) * 0.5, 1);

                if ($type === 'STOCK_OUT' && $qty > $stockBefore) {
                    $qty = max(0.5, round($stockBefore * 0.3, 1));
                }

                $stockAfter = $type === 'STOCK_IN'
                    ? $stockBefore + $qty
                    : max(0, $stockBefore - $qty);

                $transactions[] = [
                    'transaction_code' => 'TXN-' . str_pad($transCounter, 5, '0', STR_PAD_LEFT),
                    'chemical_id'      => $chemical->id,
                    'transaction_type' => $type,
                    'quantity'         => $qty,
                    'unit'             => $chemical->unit,
                    'stock_before'     => $stockBefore,
                    'stock_after'      => $stockAfter,
                    'reference_number' => 'REF-' . strtoupper(Str::random(8)),
                    'reason'           => $reasons[$type === 'ADJUSTMENT' ? 'ADJUSTMENT' : $type][array_rand($reasons[$type === 'ADJUSTMENT' ? 'ADJUSTMENT' : $type])],
                    'location_id'      => $chemical->location_id,
                    'performed_by'     => rand(0, 1) ? $admin->id : $manager->id,
                    'transaction_date' => now()->subDays($daysAgo)->subHours(rand(0, 8)),
                    'status'           => 'completed',
                    'created_at'       => now()->subDays($daysAgo),
                    'updated_at'       => now()->subDays($daysAgo),
                ];
                $transCounter++;
            }
        }

        // Ensure we have last-7-days data for dashboard chart
        $chemicalIds = $chemicals->pluck('id')->toArray();
        for ($day = 0; $day < 7; $day++) {
            // 2-3 stock_in per day
            for ($j = 0; $j < rand(2, 3); $j++) {
                $chemId = $chemicalIds[array_rand($chemicalIds)];
                $chem = $chemicals->find($chemId);
                $transactions[] = [
                    'transaction_code' => 'TXN-' . str_pad($transCounter++, 5, '0', STR_PAD_LEFT),
                    'chemical_id'      => $chemId,
                    'transaction_type' => 'STOCK_IN',
                    'quantity'         => round(rand(2, 15) * 0.5, 1),
                    'unit'             => $chem->unit,
                    'stock_before'     => rand(5, 20),
                    'stock_after'      => rand(20, 40),
                    'reference_number' => 'REF-' . strtoupper(Str::random(8)),
                    'reason'           => 'Daily replenishment',
                    'location_id'      => $chem->location_id,
                    'performed_by'     => $manager->id,
                    'transaction_date' => now()->subDays($day)->setHour(rand(8, 12)),
                    'status'           => 'completed',
                    'created_at'       => now()->subDays($day),
                    'updated_at'       => now()->subDays($day),
                ];
            }
            // 1-2 stock_out per day
            for ($j = 0; $j < rand(1, 2); $j++) {
                $chemId = $chemicalIds[array_rand($chemicalIds)];
                $chem = $chemicals->find($chemId);
                $transactions[] = [
                    'transaction_code' => 'TXN-' . str_pad($transCounter++, 5, '0', STR_PAD_LEFT),
                    'chemical_id'      => $chemId,
                    'transaction_type' => 'STOCK_OUT',
                    'quantity'         => round(rand(1, 5) * 0.5, 1),
                    'unit'             => $chem->unit,
                    'stock_before'     => rand(15, 35),
                    'stock_after'      => rand(5, 15),
                    'reference_number' => 'REF-' . strtoupper(Str::random(8)),
                    'reason'           => 'Laboratory use',
                    'location_id'      => $chem->location_id,
                    'performed_by'     => $manager->id,
                    'transaction_date' => now()->subDays($day)->setHour(rand(13, 17)),
                    'status'           => 'completed',
                    'created_at'       => now()->subDays($day),
                    'updated_at'       => now()->subDays($day),
                ];
            }
        }

        foreach ($transactions as $tx) {
            StockTransaction::create($tx);
        }
    }
}
