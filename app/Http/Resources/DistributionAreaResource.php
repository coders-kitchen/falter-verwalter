<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class DistributionAreaResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'code' => $this->code,
            'description' => $this->description,
            'geometry_geojson' => $this->geometry_geojson,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
    }
}
