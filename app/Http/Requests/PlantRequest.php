<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Validator;

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
            'light_number_state' => 'required|in:numeric,x,unknown',
            'salt_number_state' => 'required|in:numeric,x,unknown',
            'temperature_number_state' => 'required|in:numeric,x,unknown',
            'continentality_number_state' => 'required|in:numeric,x,unknown',
            'reaction_number_state' => 'required|in:numeric,x,unknown',
            'moisture_number_state' => 'required|in:numeric,x,unknown',
            'moisture_variation_state' => 'required|in:numeric,x,unknown',
            'nitrogen_number_state' => 'required|in:numeric,x,unknown',
            'light_number' => 'nullable|integer|min:1|max:9|required_if:light_number_state,numeric',
            'temperature_number' => 'nullable|integer|min:1|max:9|required_if:temperature_number_state,numeric',
            'continentality_number' => 'nullable|integer|min:1|max:9|required_if:continentality_number_state,numeric',
            'reaction_number' => 'nullable|integer|min:1|max:9|required_if:reaction_number_state,numeric',
            'moisture_number' => 'nullable|integer|min:1|max:12|required_if:moisture_number_state,numeric',
            'moisture_variation' => 'nullable|integer|min:1|max:9|required_if:moisture_variation_state,numeric',
            'nitrogen_number' => 'nullable|integer|min:1|max:9|required_if:nitrogen_number_state,numeric',
            'salt_number' => 'nullable|integer|min:0|max:9|required_if:salt_number_state,numeric',
            'bloom_start_month' => 'nullable|integer|between:1,12',
            'bloom_end_month' => 'nullable|integer|between:1,12',
            'bloom_color' => 'nullable|string|max:255',
            'plant_height_cm_from' => 'nullable|integer|min:0',
            'plant_height_cm_until' => 'nullable|integer|min:0',
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

    public function withValidator(Validator $validator): void
    {
        $validator->after(function (Validator $validator) {
            $fields = [
                'light_number',
                'salt_number',
                'temperature_number',
                'continentality_number',
                'reaction_number',
                'moisture_number',
                'moisture_variation',
                'nitrogen_number',
            ];

            foreach ($fields as $field) {
                $state = $this->input("{$field}_state", 'numeric');
                $value = $this->input($field);

                if ($state !== 'numeric' && $value !== null && $value !== '') {
                    $validator->errors()->add($field, 'Bei X oder ? darf kein Zahlenwert gesetzt sein.');
                }
            }
        });
    }
}
