<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class PlantResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'scientific_name' => $this->scientific_name,
            'family_genus' => $this->family_genus,
            'life_form' => $this->whenLoaded('lifeForm', fn() => [
                'id' => $this->lifeForm->id,
                'name' => $this->lifeForm->name,
            ]),
            'ecological_scales' => [
                'light' => ['value' => $this->light_number, 'state' => $this->light_number_state],
                'salt' => ['value' => $this->salt_number, 'state' => $this->salt_number_state],
                'temperature' => ['value' => $this->temperature_number, 'state' => $this->temperature_number_state],
                'continentality' => ['value' => $this->continentality_number, 'state' => $this->continentality_number_state],
                'reaction' => ['value' => $this->reaction_number, 'state' => $this->reaction_number_state],
                'moisture' => ['value' => $this->moisture_number, 'state' => $this->moisture_number_state],
                'moisture_variation' => ['value' => $this->moisture_variation, 'state' => $this->moisture_variation_state],
                'nitrogen' => ['value' => $this->nitrogen_number, 'state' => $this->nitrogen_number_state],
            ],
            'bloom_start_month' => $this->bloom_start_month,
            'bloom_end_month' => $this->bloom_end_month,
            'bloom_color' => $this->bloom_color,
            'plant_height_cm_from' => $this->plant_height_cm_from,
            'plant_height_cm_until' => $this->plant_height_cm_until,
            'lifespan' => $this->lifespan,
            'location' => $this->location,
            'is_native' => $this->is_native,
            'is_invasive' => $this->is_invasive,
            'threat_status' => $this->threat_status,
            'heavy_metal_resistance' => $this->heavy_metal_resistance,
            'persistence_organs' => $this->persistence_organs,
            'habitats' => $this->whenLoaded('habitats', HabitatResource::collection($this->habitats)),
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
    }
}
