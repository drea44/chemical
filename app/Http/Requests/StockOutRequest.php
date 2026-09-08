<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StockOutRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()->canManageStock();
    }

    public function rules(): array
    {
        return [
            'chemical_id'      => 'required|exists:chemicals,id',
            'quantity'         => 'required|numeric|min:0.001',
            'reference_number' => 'nullable|string|max:100',
            'reason'           => 'required|string|max:500',
            'notes'            => 'nullable|string|max:1000',
        ];
    }
}
