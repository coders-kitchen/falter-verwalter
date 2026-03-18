<?php

namespace App\Http\Controllers;

use App\Models\DistributionArea;
use App\Models\Species;
use App\Models\SpeciesDistributionArea;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class PublicMapAreaController extends Controller
{
    public function meta(Request $request, string $code): JsonResponse
    {
        $area = DistributionArea::query()
            ->with('level:id,name,code,sort_order,map_role')
            ->select(['id', 'distribution_area_level_id', 'name', 'code'])
            ->where('code', $code)
            ->first();

        if (!$area) {
            return response()->json([
                'message' => 'Distribution area not found.',
            ], 404);
        }

        $speciesId = $request->query('species_id');
        if ($speciesId !== null) {
            $species = Species::query()->find($speciesId);
            if (!$species) {
                return response()->json([
                    'message' => 'Invalid species_id.',
                    'errors' => [
                        'species_id' => ['The selected species_id is invalid.'],
                    ],
                ], 422);
            }

            $mapping = SpeciesDistributionArea::query()
                ->with('threatCategory:id,code,label,color_code')
                ->where('distribution_area_id', $area->id)
                ->where('species_id', $species->id)
                ->first();

            return response()->json([
                'data' => [
                    'code' => $area->code,
                    'name' => $area->name,
                    'level' => $area->level ? [
                        'id' => $area->level->id,
                        'name' => $area->level->name,
                        'code' => $area->level->code,
                        'sort_order' => $area->level->sort_order,
                        'map_role' => $area->level->map_role,
                    ] : null,
                    'species' => $mapping ? [
                        'id' => $species->id,
                        'threat_status' => $mapping->threatCategory ? [
                            'code' => $mapping->threatCategory->code,
                            'label' => $mapping->threatCategory->label,
                            'color' => $mapping->threatCategory->color_code,
                        ] : null,
                    ] : null,
                ],
            ]);
        }

        return response()->json([
            'data' => [
                'code' => $area->code,
                'name' => $area->name,
                'level' => $area->level ? [
                    'id' => $area->level->id,
                    'name' => $area->level->name,
                    'code' => $area->level->code,
                    'sort_order' => $area->level->sort_order,
                    'map_role' => $area->level->map_role,
                ] : null,
                'species_distribution_area_count' => SpeciesDistributionArea::query()
                    ->where('distribution_area_id', $area->id)
                    ->count(),
            ],
        ]);
    }

    public function geometry(Request $request, string $code): JsonResponse
    {
        $area = DistributionArea::query()
            ->select(['id', 'code', 'geojson_path', 'updated_at'])
            ->where('code', $code)
            ->first();

        if (!$area) {
            return response()->json([
                'message' => 'Distribution area not found.',
            ], 404);
        }

        if (!$area->geojson_path || !Storage::disk('public')->exists($area->geojson_path)) {
            return response()->json([
                'message' => 'Geometry not found for distribution area.',
            ], 404);
        }

        $disk = Storage::disk('public');
        $raw = $disk->get($area->geojson_path);
        $decoded = json_decode($raw, true);
        $geometry = is_array($decoded) ? $this->extractGeometryFromGeoJson($decoded) : null;

        if (!$geometry) {
            return response()->json([
                'message' => 'Geometry payload is invalid.',
            ], 422);
        }

        $etag = '"' . sha1($raw) . '"';
        if ($request->header('If-None-Match') === $etag) {
            return response()->json(null, 304, [
                'ETag' => $etag,
                'Last-Modified' => gmdate('D, d M Y H:i:s', $disk->lastModified($area->geojson_path)) . ' GMT',
            ]);
        }

        return response()->json([
            'data' => [
                'code' => $area->code,
                'geometry' => $geometry,
            ],
        ], 200, [
            'ETag' => $etag,
            'Last-Modified' => gmdate('D, d M Y H:i:s', $disk->lastModified($area->geojson_path)) . ' GMT',
        ]);
    }

    private function extractGeometryFromGeoJson(array $decoded): ?array
    {
        $type = $decoded['type'] ?? null;

        if ($type === 'Feature') {
            $geometry = $decoded['geometry'] ?? null;
            return is_array($geometry) ? $this->extractGeometryFromGeoJson($geometry) : null;
        }

        if ($type === 'FeatureCollection') {
            $features = $decoded['features'] ?? null;
            if (!is_array($features) || count($features) === 0 || !is_array($features[0])) {
                return null;
            }

            return $this->extractGeometryFromGeoJson($features[0]);
        }

        if (!in_array($type, ['Polygon', 'MultiPolygon'], true)) {
            return null;
        }

        $coordinates = $decoded['coordinates'] ?? null;
        if (!is_array($coordinates) || count($coordinates) === 0) {
            return null;
        }

        return [
            'type' => $type,
            'coordinates' => $coordinates,
        ];
    }
}
