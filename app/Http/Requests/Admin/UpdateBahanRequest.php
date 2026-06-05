<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateBahanRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $bahanId = $this->route('bahan')?->id;

        return [
            'nama'                   => ['required', 'string', 'max:255', Rule::unique('bahans', 'nama')->ignore($bahanId)],
            'expired_expectancy_day' => ['nullable', 'integer', 'min:1', 'max:3650'],
            'kategori'               => ['nullable', 'string', 'in:KARBOHIDRAT,PROTEIN,SAYURAN,BUAH,BUMBU,MINUMAN,LAINNYA'],
        ];
    }

    public function messages(): array
    {
        return [
            'nama.required'                  => 'Nama bahan wajib diisi.',
            'nama.unique'                    => 'Nama bahan sudah digunakan oleh bahan lain.',
            'expired_expectancy_day.integer' => 'Ekspektasi expired harus berupa angka.',
            'expired_expectancy_day.min'     => 'Ekspektasi expired minimal 1 hari.',
            'expired_expectancy_day.max'     => 'Ekspektasi expired maksimal 3650 hari (10 tahun).',
            'kategori.in'                    => 'Kategori tidak valid.',
        ];
    }
}