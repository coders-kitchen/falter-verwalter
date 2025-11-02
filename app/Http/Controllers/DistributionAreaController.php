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
        $area = DistributionArea::create(array_merge(
            $request->validated(),
            ['user_id' => auth()->id()]
        ));

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
        $distributionArea->update($request->validated());

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
