<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateFilterRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $filterId = $this->route('filter')?->id;

        return [
            'title'       => ['required', 'string', 'max:255', Rule::unique('filters', 'title')->ignore($filterId)],
            'level'       => ['nullable', 'integer', 'in:1,2,3'],
            'description' => ['nullable', 'string', 'max:500'],
        ];
    }

    public function messages(): array
    {
        return [
            'title.required' => 'Nama filter wajib diisi.',
            'title.unique'   => 'Nama filter sudah digunakan oleh filter lain.',
            'level.in'       => 'Tipe filter tidak valid.',
        ];
    }
}