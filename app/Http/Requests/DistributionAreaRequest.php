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
        $distributionAreaId = $this->distribution_area?->id ?? 'NULL';

        return [
            'name' => 'required|string|max:255|unique:distribution_areas,name,' . $distributionAreaId,
            'code' => 'required|string|max:120|alpha_dash|unique:distribution_areas,code,' . $distributionAreaId,
            'description' => 'nullable|string',
            'geometry_geojson' => 'nullable|json',
        ];
    }

    public function messages(): array
    {
        return [
            'name.required' => 'Der Name ist erforderlich.',
            'name.unique' => 'Dieses Verbreitungsgebiet existiert bereits.',
            'code.required' => 'Der Code ist erforderlich.',
            'code.alpha_dash' => 'Der Code darf nur Buchstaben, Zahlen und Bindestriche enthalten.',
            'code.unique' => 'Dieser Code existiert bereits.',
            'geometry_geojson.json' => 'Die GeoJSON-Geometrie muss gültiges JSON sein.',
        ];
    }
}
