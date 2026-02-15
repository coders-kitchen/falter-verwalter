<?php

namespace App\Http\Controllers;

use App\Http\Requests\DistributionAreaRequest;
use App\Http\Resources\DistributionAreaResource;
use App\Models\DistributionArea;
use Illuminate\Http\JsonResponse;

class DistributionAreaController extends Controller
{
    public function index(): JsonResponse
    {
        $areas = DistributionArea::paginate(50);

        return response()->json([
            'data' => DistributionAreaResource::collection($areas),
            'total' => $areas->total(),
            'page' => $areas->currentPage(),
        ]);
    }

    public function store(DistributionAreaRequest $request): JsonResponse
    {
        $payload = $request->validated();
        $payload['geometry_geojson'] = empty($payload['geometry_geojson']) ? null : json_decode($payload['geometry_geojson'], true);

        $area = DistributionArea::create(array_merge($payload, ['user_id' => auth()->id()]));

        return response()->json([
            'data' => new DistributionAreaResource($area),
        ], 201);
    }

    public function show(DistributionArea $distributionArea): JsonResponse
    {
        return response()->json([
            'data' => new DistributionAreaResource($distributionArea),
        ]);
    }

    public function update(DistributionAreaRequest $request, DistributionArea $distributionArea): JsonResponse
    {
        $payload = $request->validated();
        $payload['geometry_geojson'] = empty($payload['geometry_geojson']) ? null : json_decode($payload['geometry_geojson'], true);

        $distributionArea->update($payload);

        return response()->json([
            'data' => new DistributionAreaResource($distributionArea),
        ]);
    }

    public function destroy(DistributionArea $distributionArea): JsonResponse
    {
        $distributionArea->delete();

        return response()->json(null, 204);
    }
}
