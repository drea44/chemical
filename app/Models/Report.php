<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Report extends Model
{
    public $timestamps = false;

    protected $fillable = [
        'report_code', 'report_type', 'generated_by',
        'period_start', 'period_end', 'file_path', 'format',
    ];

    protected $casts = [
        'period_start' => 'date',
        'period_end'   => 'date',
        'created_at'   => 'datetime',
    ];

    public function generator()
    {
        return $this->belongsTo(User::class, 'generated_by');
    }

    public function getReportTypeLabelAttribute(): string
    {
        return match($this->report_type) {
            'inventory_summary'  => 'Inventory Summary',
            'stock_movement'     => 'Stock Movement',
            'expiry_report'      => 'Expiry Report',
            'usage_report'       => 'Usage Report',
            'adjustment_report'  => 'Adjustment Report',
            default              => ucfirst(str_replace('_', ' ', $this->report_type)),
        };
    }
}
