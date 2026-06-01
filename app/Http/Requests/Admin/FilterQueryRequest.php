<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;

class FilterQueryRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'search' => ['nullable', 'string', 'max:100'],
            'level'  => ['nullable', 'integer', 'in:1,2,3'], // ✅ fix
            'page'   => ['nullable', 'integer', 'min:1'],
        ];
    }

    public function filters(): array
    {
        return [
            'search' => $this->string('search')->value() ?: null,
            'level'  => $this->input('level'),
        ];
    }
}