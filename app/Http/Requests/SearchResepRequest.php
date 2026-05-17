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
            'q'     => ['nullable', 'string', 'max:100'],
            'bahan' => ['nullable', 'string'], // Menerima format "1,2,3"
        ];
    }

    public function messages(): array
    {
        return [
            'q.string' => 'Kata kunci pencarian harus berupa teks.',
        ];
    }
}