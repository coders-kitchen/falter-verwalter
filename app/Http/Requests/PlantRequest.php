<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class PlantRequest extends FormRequest
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
            'family_genus' => 'nullable|string|max:255',
            'life_form_id' => 'required|exists:life_forms,id',
            'light_number' => 'nullable|integer|min:1|max:9',
            'temperature_number' => 'nullable|integer|min:1|max:9',
            'continentality_number' => 'nullable|integer|min:1|max:9',
            'reaction_number' => 'nullable|integer|min:1|max:9',
            'moisture_number' => 'nullable|integer|min:1|max:9',
            'moisture_variation' => 'nullable|integer|min:1|max:9',
            'nitrogen_number' => 'nullable|integer|min:1|max:9',
            'bloom_months.*' => 'string',
            'bloom_color' => 'nullable|string|max:255',
            'plant_height_cm' => 'nullable|integer|min:0',
            'lifespan' => 'nullable|in:annual,biennial,perennial',
            'location' => 'nullable|string',
            'is_native' => 'boolean',
            'is_invasive' => 'boolean',
            'threat_status' => 'nullable|string|max:255',
            'persistence_organs' => 'nullable|string',
            'habitat_ids' => 'nullable|array',
            'habitat_ids.*' => 'exists:habitats,id',
        ];
    }

    public function messages(): array
    {
        return [
            'name.required' => 'Der Pflanzenname ist erforderlich.',
            'life_form_id.required' => 'Die Lebensart ist erforderlich.',
            'life_form_id.exists' => 'Die ausgewählte Lebensart existiert nicht.',
            'light_number.max' => 'Die Lichtzahl darf 9 nicht überschreiten.',
            'temperature_number.max' => 'Die Temperaturzahl darf 9 nicht überschreiten.',
        ];
    }
}
