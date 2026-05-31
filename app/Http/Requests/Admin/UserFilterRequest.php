<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;

class UserFilterRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'search' => ['nullable', 'string', 'max:100'],
            'verif'  => ['nullable', 'in:verified,unverified'],
            'role'   => ['nullable', 'in:admin,user'],
            'page'   => ['nullable', 'integer', 'min:1'],
        ];
    }

    public function filters(): array
    {
        return [
            'search' => $this->string('search')->toString() ?: null,
            'verif'  => $this->input('verif'),
            'role'   => $this->input('role'),
        ];
    }
}