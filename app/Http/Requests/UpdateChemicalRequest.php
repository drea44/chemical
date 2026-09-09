<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateChemicalRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()->can('update', $this->route('chemical'));
    }

    public function rules(): array
    {
        $chemicalId = $this->route('chemical')->id ?? null;

        return [
            'chemical_name'     => 'required|string|max:255',
            'cas_number'        => 'nullable|string|max:50',
            'chemical_code'     => 'nullable|string|max:50|unique:chemicals,chemical_code,' . $chemicalId,
            'category_id'       => 'required|exists:chemical_categories,id',
            'supplier'          => 'nullable|string|max:255',
            'manufacturer'      => 'nullable|string|max:255',
            'catalog_number'    => 'nullable|string|max:100',
            'batch_number'      => 'nullable|string|max:100',
            'lot_number'        => 'nullable|string|max:100',
            'concentration'     => 'nullable|string|max:100',
            'physical_state'    => 'nullable|in:solid,liquid,gas,powder,solution',
            'unit'              => 'required|string|max:20',
            'minimum_stock'     => 'required|numeric|min:0',
            'maximum_stock'     => 'nullable|numeric|min:0',
            'storage_condition' => 'nullable|string|max:500',
            'hazard_class'      => 'nullable|string|max:100',
            'location_id'       => 'nullable|exists:chemical_locations,id',
            'received_date'     => 'nullable|date',
            'expiry_date'       => 'nullable|date|after_or_equal:received_date',
            'notes'             => 'nullable|string|max:2000',
        ];
    }
}
