<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class StockAdjustment extends Model
{
    use HasFactory;

    protected $fillable = [
        'adjustment_code', 'chemical_id', 'previous_stock', 'adjusted_stock',
        'difference', 'reason', 'evidence', 'status', 'approved_by',
        'adjusted_by', 'adjustment_date',
    ];

    protected $casts = [
        'adjustment_date' => 'datetime',
        'previous_stock'  => 'decimal:3',
        'adjusted_stock'  => 'decimal:3',
        'difference'      => 'decimal:3',
    ];

    public function chemical()
    {
        return $this->belongsTo(Chemical::class);
    }

    public function approver()
    {
        return $this->belongsTo(User::class, 'approved_by');
    }

    public function adjuster()
    {
        return $this->belongsTo(User::class, 'adjusted_by');
    }

    public function isSignificantChange(float $threshold = 20): bool
    {
        if ($this->previous_stock == 0) return true;
        $percentChange = abs($this->difference / $this->previous_stock) * 100;
        return $percentChange >= $threshold;
    }
}
