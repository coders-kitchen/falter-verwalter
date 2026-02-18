<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class SpeciesRequest extends FormRequest
{
    public function authorize(): bool
    {
        return auth()->check() && auth()->user()->isAdmin();
    }

    public function rules(): array
    {
        return [
            'name' => 'required|string|max:255',
            'scientific_name' => 'nullable|string|max:255',
            'family_id' => 'nullable|exists:families,id|required_without:genus_id',
            'genus_id' => 'nullable|exists:genera,id|required_without:family_id',
            'size_category' => 'required|in:XS,S,M,L,XL',
            'color_description' => 'nullable|string',
            'special_features' => 'nullable|string|max:255',
            'gender_differences' => 'nullable|string',
            'hibernation_stage' => 'nullable|in:egg,larva,pupa,adult',
            'pupal_duration_days' => 'nullable|integer|min:0',
            'red_list_status_de' => 'nullable|string|max:255',
            'red_list_status_eu' => 'nullable|string|max:255',
            'abundance_trend' => 'nullable|string|max:255',
            'protection_status' => 'nullable|string|max:255',
            'distribution_area_ids' => 'nullable|array',
            'distribution_area_ids.*' => 'exists:distribution_areas,id',
            'habitat_ids' => 'nullable|array',
            'habitat_ids.*' => 'exists:habitats,id',
            'host_plant_ids' => 'nullable|array',
            'host_plant_ids.*' => 'exists:plants,id',
        ];
    }

    public function messages(): array
    {
        return [
            'name.required' => 'Der Artname ist erforderlich.',
            'genus_id.required' => 'Die Gattung ist erforderlich.',
            'genus_id.exists' => 'Die ausgewählte Gattung existiert nicht.',
            'size_category.required' => 'Die Größenkategorie ist erforderlich.',
            'size_category.in' => 'Die Größenkategorie muss eine gültige Option sein.',
        ];
    }
}
