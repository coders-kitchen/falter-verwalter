<?php

namespace App\Http\Controllers;

use App\Http\Requests\FamilyRequest;
use App\Http\Resources\FamilyResource;
use App\Models\Family;
use Illuminate\Http\JsonResponse;

class FamilyController extends Controller
{
    public function index(): JsonResponse
    {
        $families = Family::with('species')->paginate(50);

        return response()->json([
            'data' => FamilyResource::collection($families),
            'total' => $families->total(),
            'page' => $families->currentPage(),
        ]);
    }

    public function store(FamilyRequest $request): JsonResponse
    {
        $family = Family::create(array_merge(
            $request->validated(),
            ['user_id' => auth()->id()]
        ));

        return response()->json([
            'data' => new FamilyResource($family),
        ], 201);
    }

    public function show(Family $family): JsonResponse
    {
        return response()->json([
            'data' => new FamilyResource($family->load('species')),
        ]);
    }

    public function update(FamilyRequest $request, Family $family): JsonResponse
    {
        $family->update($request->validated());

        return response()->json([
            'data' => new FamilyResource($family),
        ]);
    }

    public function destroy(Family $family): JsonResponse
    {
        // Prevent deletion if species exist
        if ($family->species()->count() > 0) {
            return response()->json([
                'message' => 'Diese Familie kann nicht gelöscht werden, da noch Arten zugeordnet sind.',
            ], 409);
        }

        $family->delete();

        return response()->json(null, 204);
    }
}
