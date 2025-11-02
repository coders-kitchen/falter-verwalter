<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class HabitatRequest extends FormRequest
{
    public function authorize(): bool
    {
        return auth()->check() && auth()->user()->isAdmin();
    }

    public function rules(): array
    {
        return [
            'name' => 'required|string|max:255',
            'parent_id' => 'nullable|exists:habitats,id',
            'description' => 'nullable|string',
        ];
    }

    public function messages(): array
    {
        return [
            'name.required' => 'Der Name ist erforderlich.',
            'parent_id.exists' => 'Das übergeordnete Habitat existiert nicht.',
        ];
    }
}
