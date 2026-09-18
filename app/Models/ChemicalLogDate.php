<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class ChemicalLogDate extends Model
{
    use HasFactory;

    protected $fillable = [
        'log_date',
        'analyst_name',
        'analyst_take_1',
        'analyst_take_2',
        'analyst_take_3',
        'period_month',
        'notes',
        'created_by',
    ];

    protected $casts = [
        'log_date' => 'date',
    ];

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function usages(): HasMany
    {
        return $this->hasMany(ChemicalDailyUsage::class, 'log_date_id');
    }
}

