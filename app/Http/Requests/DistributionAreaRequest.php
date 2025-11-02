<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class DistributionAreaRequest extends FormRequest
{
    public function authorize(): bool
    {
        return auth()->check() && auth()->user()->isAdmin();
    }

    public function rules(): array
    {
        return [
            'name' => 'required|string|max:255|unique:distribution_areas,name,' . ($this->distribution_area?->id ?? 'NULL'),
            'description' => 'nullable|string',
        ];
    }

    public function messages(): array
    {
        return [
            'name.required' => 'Der Name ist erforderlich.',
            'name.unique' => 'Dieses Verbreitungsgebiet existiert bereits.',
        ];
    }
}
