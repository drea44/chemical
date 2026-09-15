<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ChemicalMonthlyBalance extends Model
{
    use HasFactory;

    protected $fillable = [
        'chemical_id',
        'period_month',
        'saldo_awal',
        'penerimaan',
    ];

    protected $casts = [
        'saldo_awal' => 'float',
        'penerimaan' => 'float',
    ];

    public function chemical(): BelongsTo
    {
        return $this->belongsTo(Chemical::class);
    }
}
