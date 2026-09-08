<?php

namespace App\Services;

use App\Models\Chemical;
use App\Models\StockAdjustment;
use App\Models\StockTransaction;
use App\Models\SystemSetting;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class StockService
{
    /**
     * Process a stock-in transaction.
     */
    public function stockIn(Chemical $chemical, float $quantity, array $data): StockTransaction
    {
        return DB::transaction(function () use ($chemical, $quantity, $data) {
            $lockedChemical = Chemical::where('id', $chemical->id)->lockForUpdate()->firstOrFail();

            $stockBefore = (float) $lockedChemical->current_stock;
            $stockAfter  = $stockBefore + $quantity;

            $lockedChemical->current_stock = $stockAfter;
            $lockedChemical->updateStatus((int) SystemSetting::getValue('expiry_warning_days', 30));
            $lockedChemical->updated_by = Auth::id();
            $lockedChemical->save();

            // Synchronize passed chemical instance
            $chemical->current_stock = $stockAfter;
            $chemical->status = $lockedChemical->status;

            $tx = StockTransaction::create([
                'transaction_code' => $this->generateTransactionCode(),
                'chemical_id'      => $lockedChemical->id,
                'transaction_type' => 'STOCK_IN',
                'quantity'         => $quantity,
                'unit'             => $lockedChemical->unit,
                'stock_before'     => $stockBefore,
                'stock_after'      => $stockAfter,
                'reference_number' => $data['reference_number'] ?? null,
                'reason'           => $data['reason'] ?? null,
                'location_id'      => $lockedChemical->location_id,
                'performed_by'     => Auth::id(),
                'transaction_date' => now(),
                'notes'            => $data['notes'] ?? null,
                'status'           => 'completed',
            ]);

            AuditLogService::logStockIn($lockedChemical->id, $stockBefore, $stockAfter, $quantity);

            return $tx;
        });
    }

    /**
     * Process a stock-out transaction. Throws exception if insufficient stock.
     */
    public function stockOut(Chemical $chemical, float $quantity, array $data): StockTransaction
    {
        return DB::transaction(function () use ($chemical, $quantity, $data) {
            $lockedChemical = Chemical::where('id', $chemical->id)->lockForUpdate()->firstOrFail();

            if ($quantity > (float) $lockedChemical->current_stock) {
                throw new \RuntimeException(
                    "Insufficient stock. Available: {$lockedChemical->current_stock} {$lockedChemical->unit}, Requested: {$quantity} {$lockedChemical->unit}."
                );
            }

            $stockBefore = (float) $lockedChemical->current_stock;
            $stockAfter  = $stockBefore - $quantity;

            $lockedChemical->current_stock = $stockAfter;
            $lockedChemical->updateStatus((int) SystemSetting::getValue('expiry_warning_days', 30));
            $lockedChemical->updated_by = Auth::id();
            $lockedChemical->save();

            // Synchronize passed chemical instance
            $chemical->current_stock = $stockAfter;
            $chemical->status = $lockedChemical->status;

            $tx = StockTransaction::create([
                'transaction_code' => $this->generateTransactionCode(),
                'chemical_id'      => $lockedChemical->id,
                'transaction_type' => 'STOCK_OUT',
                'quantity'         => $quantity,
                'unit'             => $lockedChemical->unit,
                'stock_before'     => $stockBefore,
                'stock_after'      => $stockAfter,
                'reference_number' => $data['reference_number'] ?? null,
                'reason'           => $data['reason'] ?? null,
                'location_id'      => $lockedChemical->location_id,
                'performed_by'     => Auth::id(),
                'transaction_date' => now(),
                'notes'            => $data['notes'] ?? null,
                'status'           => 'completed',
            ]);

            AuditLogService::logStockOut($lockedChemical->id, $stockBefore, $stockAfter, $quantity);

            return $tx;
        });
    }

    /**
     * Process a stock adjustment.
     */
    public function adjust(Chemical $chemical, float $newStock, array $data): StockAdjustment
    {
        return DB::transaction(function () use ($chemical, $newStock, $data) {
            $lockedChemical = Chemical::where('id', $chemical->id)->lockForUpdate()->firstOrFail();

            $previousStock = (float) $lockedChemical->current_stock;
            $difference    = $newStock - $previousStock;

            $lockedChemical->current_stock = $newStock;
            $lockedChemical->updateStatus((int) SystemSetting::getValue('expiry_warning_days', 30));
            $lockedChemical->updated_by = Auth::id();
            $lockedChemical->save();

            // Synchronize passed chemical instance
            $chemical->current_stock = $newStock;
            $chemical->status = $lockedChemical->status;

            // Also create a stock transaction record for ledger
            StockTransaction::create([
                'transaction_code' => $this->generateTransactionCode(),
                'chemical_id'      => $lockedChemical->id,
                'transaction_type' => 'ADJUSTMENT',
                'quantity'         => abs($difference),
                'unit'             => $lockedChemical->unit,
                'stock_before'     => $previousStock,
                'stock_after'      => $newStock,
                'reason'           => $data['reason'] ?? null,
                'location_id'      => $lockedChemical->location_id,
                'performed_by'     => Auth::id(),
                'transaction_date' => now(),
                'notes'            => $data['notes'] ?? null,
                'status'           => 'completed',
            ]);

            $adjustment = StockAdjustment::create([
                'adjustment_code' => $this->generateAdjustmentCode(),
                'chemical_id'     => $lockedChemical->id,
                'previous_stock'  => $previousStock,
                'adjusted_stock'  => $newStock,
                'difference'      => $difference,
                'reason'          => $data['reason'],
                'evidence'        => $data['evidence'] ?? null,
                'status'          => 'approved',
                'approved_by'     => Auth::id(),
                'adjusted_by'     => Auth::id(),
                'adjustment_date' => now(),
            ]);

            AuditLogService::logAdjustment($lockedChemical->id, $previousStock, $newStock);

            return $adjustment;
        });
    }

    private function generateTransactionCode(): string
    {
        $maxId = (int) StockTransaction::max('id');
        return 'TXN-' . str_pad($maxId + 1, 5, '0', STR_PAD_LEFT);
    }

    private function generateAdjustmentCode(): string
    {
        $maxId = (int) StockAdjustment::max('id');
        return 'ADJ-' . str_pad($maxId + 1, 5, '0', STR_PAD_LEFT);
    }
}
