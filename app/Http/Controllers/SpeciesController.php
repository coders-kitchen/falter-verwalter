<?php

namespace App\Http\Controllers;

use App\Http\Requests\SpeciesRequest;
use App\Http\Resources\SpeciesResource;
use App\Models\Genus;
use App\Models\Species;
use Illuminate\Http\JsonResponse;

class SpeciesController extends Controller
{
    public function index(): JsonResponse
    {
        $species = Species::with('family:id,name', 'genus.subfamily.family', 'genus.tribe')
            ->paginate(50);

        return response()->json([
            'data' => SpeciesResource::collection($species),
            'total' => $species->total(),
            'page' => $species->currentPage(),
        ]);
    }

    public function store(SpeciesRequest $request): JsonResponse
    {
        $payload = $request->validated();
        if (!empty($payload['genus_id'])) {
            $genus = Genus::with('subfamily.family')->findOrFail((int) $payload['genus_id']);
            $payload['family_id'] = $genus->subfamily->family->id;
        }

        $species = Species::create(array_merge(
            $payload,
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
            'data' => new SpeciesResource($species->load('family:id,name', 'genus.subfamily.family', 'genus.tribe', 'distributionAreas:id,name', 'habitats:id,name', 'plants:id,name')),
        ], 201);
    }

    public function show(Species $species): JsonResponse
    {
        return response()->json([
            'data' => new SpeciesResource($species->load('family:id,name', 'genus.subfamily.family', 'genus.tribe', 'distributionAreas:id,name', 'habitats:id,name', 'plants:id,name')),
        ]);
    }

    public function update(SpeciesRequest $request, Species $species): JsonResponse
    {
        $payload = $request->validated();
        if (!empty($payload['genus_id'])) {
            $genus = Genus::with('subfamily.family')->findOrFail((int) $payload['genus_id']);
            $payload['family_id'] = $genus->subfamily->family->id;
        }

        $species->update($payload);

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
            'data' => new SpeciesResource($species->load('family:id,name', 'genus.subfamily.family', 'genus.tribe', 'distributionAreas:id,name', 'habitats:id,name', 'plants:id,name')),
        ]);
    }

    public function destroy(Species $species): JsonResponse
    {
        $species->delete();

        return response()->json(null, 204);
    }
}
