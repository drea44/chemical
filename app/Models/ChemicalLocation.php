<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ChemicalLocation extends Model
{
    use HasFactory;

    protected $fillable = ['name', 'building', 'room', 'shelf', 'storage_type', 'temperature_range', 'description', 'status'];

    public function chemicals()
    {
        return $this->hasMany(Chemical::class, 'location_id');
    }

    public function stockTransactions()
    {
        return $this->hasMany(StockTransaction::class, 'location_id');
    }

    public function getFullAddressAttribute(): string
    {
        $parts = array_filter([$this->building, $this->room, $this->shelf]);
        return implode(' / ', $parts) ?: $this->name;
    }
}
