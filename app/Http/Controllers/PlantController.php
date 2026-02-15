<?php

namespace App\Http\Controllers;

use App\Http\Requests\PlantRequest;
use App\Http\Resources\PlantResource;
use App\Models\Plant;
use Illuminate\Http\JsonResponse;

class PlantController extends Controller
{
    public function index(): JsonResponse
    {
        $plants = Plant::with('lifeForm:id,name', 'threatCategory:id,code,label,color_code')->paginate(50);

        return response()->json([
            'data' => PlantResource::collection($plants),
            'total' => $plants->total(),
            'page' => $plants->currentPage(),
        ]);
    }

    public function store(PlantRequest $request): JsonResponse
    {
        $plant = Plant::create(array_merge(
            $request->validated(),
            ['user_id' => auth()->id()]
        ));

        if ($request->has('habitat_ids')) {
            $plant->habitats()->sync($request->habitat_ids);
        }

        return response()->json([
            'data' => new PlantResource($plant->load('lifeForm:id,name', 'habitats:id,name', 'threatCategory:id,code,label,color_code')),
        ], 201);
    }

    public function show(Plant $plant): JsonResponse
    {
        return response()->json([
            'data' => new PlantResource($plant->load('lifeForm:id,name', 'habitats:id,name', 'speciesAsHostPlant:id,name', 'threatCategory:id,code,label,color_code')),
        ]);
    }

    public function update(PlantRequest $request, Plant $plant): JsonResponse
    {
        $plant->update($request->validated());

        if ($request->has('habitat_ids')) {
            $plant->habitats()->sync($request->habitat_ids);
        }

        return response()->json([
            'data' => new PlantResource($plant->load('lifeForm:id,name', 'habitats:id,name', 'threatCategory:id,code,label,color_code')),
        ]);
    }

    public function destroy(Plant $plant): JsonResponse
    {
        $plant->delete();

        return response()->json(null, 204);
    }
}
