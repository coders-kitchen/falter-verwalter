<?php

namespace App\Http\Controllers;

use App\Http\Requests\HabitatRequest;
use App\Http\Resources\HabitatResource;
use App\Models\Habitat;
use Illuminate\Http\JsonResponse;

class HabitatController extends Controller
{
    public function index(): JsonResponse
    {
        $habitats = Habitat::with('parent:id,name', 'children:id,name,parent_id')
            ->whereNull('parent_id')
            ->paginate(50);

        return response()->json([
            'data' => HabitatResource::collection($habitats),
            'total' => $habitats->total(),
            'page' => $habitats->currentPage(),
        ]);
    }

    public function store(HabitatRequest $request): JsonResponse
    {
        $data = array_merge(
            $request->validated(),
            ['user_id' => auth()->id()]
        );

        // Set level based on parent
        if ($data['parent_id'] ?? null) {
            $parent = Habitat::find($data['parent_id']);
            $data['level'] = ($parent->level ?? 0) + 1;
        } else {
            $data['level'] = 1;
        }

        $habitat = Habitat::create($data);

        return response()->json([
            'data' => new HabitatResource($habitat->load('parent:id,name', 'children:id,name,parent_id')),
        ], 201);
    }

    public function show(Habitat $habitat): JsonResponse
    {
        return response()->json([
            'data' => new HabitatResource($habitat->load('parent:id,name', 'children:id,name,parent_id', 'species:id,name', 'plants:id,name')),
        ]);
    }

    public function update(HabitatRequest $request, Habitat $habitat): JsonResponse
    {
        $data = $request->validated();

        // Prevent circular reference
        if (($data['parent_id'] ?? null) === $habitat->id) {
            return response()->json([
                'message' => 'Ein Habitat kann nicht sein eigenes übergeordnetes Habitat sein.',
            ], 422);
        }

        // Update level if parent changed
        if (($data['parent_id'] ?? null) !== $habitat->parent_id) {
            if ($data['parent_id'] ?? null) {
                $parent = Habitat::find($data['parent_id']);
                $data['level'] = ($parent->level ?? 0) + 1;
            } else {
                $data['level'] = 1;
            }
        }

        $habitat->update($data);

        return response()->json([
            'data' => new HabitatResource($habitat->load('parent:id,name', 'children:id,name,parent_id')),
        ]);
    }

    public function destroy(Habitat $habitat): JsonResponse
    {
        if ($habitat->children()->count() > 0 || $habitat->species()->count() > 0) {
            return response()->json([
                'message' => 'Dieses Habitat kann nicht gelöscht werden, da noch untergeordnete Habitate oder Arten zugeordnet sind.',
            ], 409);
        }

        $habitat->delete();

        return response()->json(null, 204);
    }
}
