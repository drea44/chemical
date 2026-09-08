<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StockAdjustmentRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()->canManageStock();
    }

    public function rules(): array
    {
        return [
            'chemical_id'   => 'required|exists:chemicals,id',
            'adjusted_stock'=> 'required|numeric|min:0',
            'reason'        => 'required|string|max:500',
            'evidence'      => 'nullable|string|max:500',
            'notes'         => 'nullable|string|max:1000',
        ];
    }
}
