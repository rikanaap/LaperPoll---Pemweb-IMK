<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;

class ResepFilterRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true; 
    }

    public function rules(): array
    {
        return [
            'search'    => ['nullable', 'string', 'max:100'],
            'status'    => ['nullable', 'in:0,1'],
            'filter_id' => ['nullable', 'integer', 'exists:filters,id'],
            'page'      => ['nullable', 'integer', 'min:1'],
        ];
    }

    public function filters(): array
    {
        return [
            'search'    => $this->string('search')->toString() ?: null,
            'status'    => $this->filled('status') ? (bool) $this->input('status') : null,
            'filter_id' => $this->filled('filter_id') ? (int) $this->input('filter_id') : null,
        ];
    }
}