<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class SpeciesResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'scientific_name' => $this->scientific_name,
            'family' => $this->whenLoaded('family', fn() => [
                'id' => $this->family->id,
                'name' => $this->family->name,
            ]),
            'size_category' => $this->size_category,
            'color_description' => $this->color_description,
            'special_features' => $this->special_features,
            'gender_differences' => $this->gender_differences,
            'generations_per_year' => $this->generations_per_year,
            'hibernation_stage' => $this->hibernation_stage,
            'pupal_duration_days' => $this->pupal_duration_days,
            'red_list_status' => [
                'de' => $this->red_list_status_de,
                'eu' => $this->red_list_status_eu,
            ],
            'abundance_trend' => $this->abundance_trend,
            'protection_status' => $this->protection_status,
            'sage_feeding_indicator' => $this->sage_feeding_indicator,
            'distribution_areas' => $this->whenLoaded('distributionAreas', DistributionAreaResource::collection($this->distributionAreas)),
            'habitats' => $this->whenLoaded('habitats', HabitatResource::collection($this->habitats)),
            'host_plants' => $this->whenLoaded('hostPlants', PlantResource::collection($this->hostPlants)),
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
    }
}
