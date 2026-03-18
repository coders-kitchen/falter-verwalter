<?php

namespace App\Http\Resources;

use Illuminate\Support\Facades\Storage;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class DistributionAreaResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'distribution_area_level_id' => $this->distribution_area_level_id,
            'name' => $this->name,
            'code' => $this->code,
            'description' => $this->description,
            'geojson_path' => $this->geojson_path,
            'geojson_url' => $this->geojson_path ? Storage::disk('public')->url($this->geojson_path) : null,
            'level' => $this->whenLoaded('level', fn () => [
                'id' => $this->level->id,
                'name' => $this->level->name,
                'code' => $this->level->code,
                'sort_order' => $this->level->sort_order,
                'map_role' => $this->level->map_role,
            ]),
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
    }
}
