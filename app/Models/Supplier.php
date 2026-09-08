<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Supplier extends Model
{
    use HasFactory;

    protected $table = 'chemical_suppliers';

    protected $fillable = [
        'name',
        'contact_person',
        'email',
        'phone',
        'website',
        'address',
        'status',
        'notes',
    ];

    public function chemicals()
    {
        return $this->hasMany(Chemical::class, 'supplier', 'name');
    }
}
