<?php

namespace App\Http\Controllers;

use App\Http\Requests\SpeciesRequest;
use App\Http\Resources\SpeciesResource;
use App\Models\Species;
use Illuminate\Http\JsonResponse;

class SpeciesController extends Controller
{
    public function index(): JsonResponse
    {
        $species = Species::with('family:id,name')
            ->paginate(50);

        return response()->json([
            'data' => SpeciesResource::collection($species),
            'total' => $species->total(),
            'page' => $species->currentPage(),
        ]);
    }

    public function store(SpeciesRequest $request): JsonResponse
    {
        $species = Species::create(array_merge(
            $request->validated(),
            ['user_id' => auth()->id()]
        ));

        // Attach many-to-many relationships
        if ($request->has('distribution_area_ids')) {
            $species->distributionAreas()->sync($request->distribution_area_ids);
        }
        if ($request->has('habitat_ids')) {
            $species->habitats()->sync($request->habitat_ids);
        }
        if ($request->has('host_plant_ids')) {
            foreach ($request->host_plant_ids as $plantId) {
                $species->plants()->attach($plantId, [
                    'is_nectar' => false,
                    'is_larval_host' => true,
                ]);
            }
        }

        return response()->json([
            'data' => new SpeciesResource($species->load('family:id,name', 'distributionAreas:id,name', 'habitats:id,name', 'plants:id,name')),
        ], 201);
    }

    public function show(Species $species): JsonResponse
    {
        return response()->json([
            'data' => new SpeciesResource($species->load('family:id,name', 'distributionAreas:id,name', 'habitats:id,name', 'plants:id,name')),
        ]);
    }

    public function update(SpeciesRequest $request, Species $species): JsonResponse
    {
        $species->update($request->validated());

        // Update many-to-many relationships
        if ($request->has('distribution_area_ids')) {
            $species->distributionAreas()->sync($request->distribution_area_ids);
        }
        if ($request->has('habitat_ids')) {
            $species->habitats()->sync($request->habitat_ids);
        }
        if ($request->has('host_plant_ids')) {
            $species->plants()->detach();
            foreach ($request->host_plant_ids as $plantId) {
                $species->plants()->attach($plantId, [
                    'is_nectar' => false,
                    'is_larval_host' => true,
                ]);
            }
        }

        return response()->json([
            'data' => new SpeciesResource($species->load('family:id,name', 'distributionAreas:id,name', 'habitats:id,name', 'plants:id,name')),
        ]);
    }

    public function destroy(Species $species): JsonResponse
    {
        $species->delete();

        return response()->json(null, 204);
    }
}
