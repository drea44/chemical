<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StockInOutRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()->canManageStock();
    }

    public function rules(): array
    {
        return [
            'chemical_id'      => 'required|exists:chemicals,id',
            'transaction_type' => 'required|in:STOCK_IN,STOCK_OUT',
            'quantity'         => 'required|numeric|min:0.001',
            'reference_number' => 'nullable|string|max:100',
            'reason'           => 'required|string|max:500',
            'notes'            => 'nullable|string|max:1000',
        ];
    }

    public function messages(): array
    {
        return [
            'chemical_id.required'      => 'Please select a chemical.',
            'transaction_type.required' => 'Please select a transaction type (Stock In or Stock Out).',
            'transaction_type.in'       => 'Transaction type must be Stock In or Stock Out.',
            'quantity.required'         => 'Quantity is required.',
            'quantity.numeric'          => 'Quantity must be a number.',
            'quantity.min'              => 'Quantity must be greater than zero.',
            'reason.required'           => 'Please provide a reason.',
        ];
    }
}

