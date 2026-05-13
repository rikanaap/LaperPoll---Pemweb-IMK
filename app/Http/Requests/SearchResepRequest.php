<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class SearchResepRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'bahan_ids'   => ['nullable', 'array'],
            'bahan_ids.*' => ['integer', 'exists:bahans,id'],
        ];
    }

    public function messages(): array
    {
        return [
            'bahan_ids.array'      => 'Format bahan harus berupa array.',
            'bahan_ids.*.integer'  => 'ID bahan harus berupa angka.',
            'bahan_ids.*.exists'   => 'Bahan tidak ditemukan.',
        ];
    }
}