<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;

class StoreBahanRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'nama'                   => ['required', 'string', 'max:255', 'unique:bahans,nama'],
            'expired_expectancy_day' => ['nullable', 'integer', 'min:1', 'max:3650'],
        ];
    }

    public function messages(): array
    {
        return [
            'nama.required'                  => 'Nama bahan wajib diisi.',
            'nama.unique'                    => 'Nama bahan sudah ada.',
            'expired_expectancy_day.integer' => 'Ekspektasi expired harus berupa angka.',
            'expired_expectancy_day.min'     => 'Ekspektasi expired minimal 1 hari.',
            'expired_expectancy_day.max'     => 'Ekspektasi expired maksimal 3650 hari (10 tahun).',
        ];
    }
}