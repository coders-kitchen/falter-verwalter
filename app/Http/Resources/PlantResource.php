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
                'light' => $this->light_number,
                'salt' => $this->salt_number,
                'temperature' => $this->temperature_number,
                'continentality' => $this->continentality_number,
                'reaction' => $this->reaction_number,
                'moisture' => $this->moisture_number,
                'moisture_variation' => $this->moisture_variation,
                'nitrogen' => $this->nitrogen_number,
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
            'persistence_organs' => $this->persistence_organs,
            'habitats' => $this->whenLoaded('habitats', HabitatResource::collection($this->habitats)),
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
    }
}
