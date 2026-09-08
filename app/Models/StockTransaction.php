<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class StockTransaction extends Model
{
    use HasFactory;

    protected $fillable = [
        'transaction_code', 'chemical_id', 'transaction_type', 'quantity', 'unit',
        'stock_before', 'stock_after', 'reference_number', 'reason',
        'location_id', 'performed_by', 'transaction_date', 'notes', 'status',
    ];

    protected $casts = [
        'transaction_date' => 'datetime',
        'quantity'         => 'decimal:3',
        'stock_before'     => 'decimal:3',
        'stock_after'      => 'decimal:3',
    ];

    public function chemical()
    {
        return $this->belongsTo(Chemical::class);
    }

    public function location()
    {
        return $this->belongsTo(ChemicalLocation::class, 'location_id');
    }

    public function performer()
    {
        return $this->belongsTo(User::class, 'performed_by');
    }

    public function getTypeColorAttribute(): string
    {
        return match($this->transaction_type) {
            'STOCK_IN'   => 'green',
            'STOCK_OUT'  => 'red',
            'ADJUSTMENT' => 'yellow',
            'TRANSFER'   => 'blue',
            default      => 'gray',
        };
    }

    public function getTypeLabelAttribute(): string
    {
        return match($this->transaction_type) {
            'STOCK_IN'   => 'Stock In',
            'STOCK_OUT'  => 'Stock Out',
            'ADJUSTMENT' => 'Adjustment',
            'TRANSFER'   => 'Transfer',
            default      => $this->transaction_type,
        };
    }
}
