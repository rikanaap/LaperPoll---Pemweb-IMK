<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;

class BahanFilterRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'search'  => ['nullable', 'string', 'max:100'],
            'expired' => ['nullable', 'in:yes,no'],
            'page'    => ['nullable', 'integer', 'min:1'],
        ];
    }

    public function filters(): array
    {
        return [
            'search'  => $this->string('search')->value() ?: null,
            'expired' => $this->input('expired'),
        ];
    }
}