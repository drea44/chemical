<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rules\Password;

class StoreUserRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()->isAdmin();
    }

    public function rules(): array
    {
        return [
            'name'       => 'required|string|max:255',
            'email'      => 'required|email|unique:users,email',
            'password'   => ['required', Password::min(8)->mixedCase()->numbers()],
            'role'       => 'required|in:ADMIN,STOCK_MANAGER,AUDITOR,VIEWER',
            'department' => 'nullable|string|max:100',
            'position'   => 'nullable|string|max:100',
            'status'     => 'nullable|in:active,inactive',
        ];
    }
}
