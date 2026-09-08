<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ChemicalCategory extends Model
{
    use HasFactory;

    protected $fillable = ['name', 'description', 'color', 'status'];

    public function chemicals()
    {
        return $this->hasMany(Chemical::class, 'category_id');
    }
}
