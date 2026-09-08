<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Chemical extends Model
{
    use HasFactory;

    protected $fillable = [
        'chemical_code', 'chemical_name', 'cas_number', 'category_id',
        'supplier', 'manufacturer', 'catalog_number', 'batch_number', 'lot_number',
        'concentration', 'physical_state', 'unit', 'current_stock',
        'minimum_stock', 'maximum_stock', 'storage_condition', 'hazard_class',
        'location_id', 'qr_code', 'received_date', 'expiry_date', 'status',
        'notes', 'created_by', 'updated_by',
    ];

    protected $casts = [
        'received_date'  => 'date',
        'expiry_date'    => 'date',
        'current_stock'  => 'decimal:3',
        'minimum_stock'  => 'decimal:3',
        'maximum_stock'  => 'decimal:3',
    ];

    // Relationships
    public function category()
    {
        return $this->belongsTo(ChemicalCategory::class, 'category_id');
    }

    public function location()
    {
        return $this->belongsTo(ChemicalLocation::class, 'location_id');
    }

    public function stockTransactions()
    {
        return $this->hasMany(StockTransaction::class)->orderBy('transaction_date', 'desc');
    }

    public function stockAdjustments()
    {
        return $this->hasMany(StockAdjustment::class);
    }

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function updater()
    {
        return $this->belongsTo(User::class, 'updated_by');
    }

    // Business Logic
    public function updateStatus(int $expiryWarningDays = 30): void
    {
        $status = 'SAFE';

        if ($this->expiry_date && $this->expiry_date->isPast()) {
            $status = 'EXPIRED';
        } elseif ($this->expiry_date && $this->expiry_date->lte(Carbon::now()->addDays($expiryWarningDays))) {
            $status = 'EXPIRING_SOON';
        } elseif ($this->minimum_stock > 0) {
            $criticalThreshold = $this->minimum_stock * 0.5;
            if ($this->current_stock <= $criticalThreshold) {
                $status = 'CRITICAL';
            } elseif ($this->current_stock <= $this->minimum_stock) {
                $status = 'LOW';
            }
        }

        $this->status = $status;
    }

    public function getStatusColorAttribute(): string
    {
        return match($this->status) {
            'SAFE'          => 'green',
            'LOW'           => 'yellow',
            'CRITICAL'      => 'red',
            'EXPIRED'       => 'red',
            'EXPIRING_SOON' => 'orange',
            default         => 'gray',
        };
    }

    public function getStockPercentageAttribute(): float
    {
        if (!$this->maximum_stock || $this->maximum_stock == 0) {
            return $this->current_stock > 0 ? 100 : 0;
        }
        return min(100, round(($this->current_stock / $this->maximum_stock) * 100, 1));
    }

    public function isExpired(): bool
    {
        return $this->expiry_date && $this->expiry_date->isPast();
    }

    public function isExpiringSoon(int $days = 30): bool
    {
        return $this->expiry_date
            && !$this->expiry_date->isPast()
            && $this->expiry_date->lte(Carbon::now()->addDays($days));
    }
}
