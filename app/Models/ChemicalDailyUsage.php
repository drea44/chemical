<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ChemicalDailyUsage extends Model
{
    use HasFactory;

    protected $fillable = [
        'chemical_id',
        'log_date_id',
        'take_1',
        'take_2',
        'take_3',
        'updated_by',
    ];

    protected $casts = [
        'take_1' => 'float',
        'take_2' => 'float',
        'take_3' => 'float',
    ];

    public function chemical(): BelongsTo
    {
        return $this->belongsTo(Chemical::class);
    }

    public function logDate(): BelongsTo
    {
        return $this->belongsTo(ChemicalLogDate::class, 'log_date_id');
    }

    public function updater(): BelongsTo
    {
        return $this->belongsTo(User::class, 'updated_by');
    }

    public function getTotalUsageAttribute(): float
    {
        return (float)($this->take_1 ?? 0) + (float)($this->take_2 ?? 0) + (float)($this->take_3 ?? 0);
    }
}
