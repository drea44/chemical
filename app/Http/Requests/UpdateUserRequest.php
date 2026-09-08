<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateUserRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()->isAdmin();
    }

    public function rules(): array
    {
        $userId = $this->route('user')->id ?? null;

        return [
            'name'       => 'required|string|max:255',
            'email'      => 'required|email|unique:users,email,' . $userId,
            'role'       => 'required|in:ADMIN,STOCK_MANAGER,AUDITOR,VIEWER',
            'department' => 'nullable|string|max:100',
            'position'   => 'nullable|string|max:100',
            'status'     => 'nullable|in:active,inactive',
        ];
    }
}
