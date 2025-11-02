<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class LifeFormRequest extends FormRequest
{
    public function authorize(): bool
    {
        return auth()->check();
    }

    public function rules(): array
    {
        return [
            'name' => 'required|string|max:255|unique:life_forms,name,' . ($this->life_form?->id ?? 'NULL'),
            'description' => 'nullable|string',
            'examples' => 'nullable|array',
            'examples.*' => 'string|max:255',
        ];
    }

    public function messages(): array
    {
        return [
            'name.required' => 'Der Name ist erforderlich.',
            'name.unique' => 'Dieser Name existiert bereits.',
        ];
    }
}
