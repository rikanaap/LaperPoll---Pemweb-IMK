<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;

class StoreFilterRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'title'       => ['required', 'string', 'max:255', 'unique:filters,title'],
            'level'       => ['nullable', 'integer', 'in:1,2,3'],
            'description' => ['nullable', 'string', 'max:500'],
        ];
    }

    public function messages(): array
    {
        return [
            'title.required' => 'Nama filter wajib diisi.',
            'title.unique'   => 'Nama filter sudah ada.',
            'level.in'       => 'Tipe filter tidak valid.',
        ];
    }
}