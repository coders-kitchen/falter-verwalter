<?php

namespace App\Http\Controllers;

use App\Http\Requests\LifeFormRequest;
use App\Http\Resources\LifeFormResource;
use App\Models\LifeForm;
use Illuminate\Http\JsonResponse;

class LifeFormController extends Controller
{
    public function index(): JsonResponse
    {
        $lifeForms = LifeForm::paginate(50);

        return response()->json([
            'data' => LifeFormResource::collection($lifeForms),
            'total' => $lifeForms->total(),
            'page' => $lifeForms->currentPage(),
        ]);
    }

    public function store(LifeFormRequest $request): JsonResponse
    {
        $lifeForm = LifeForm::create(array_merge(
            $request->validated(),
            ['user_id' => auth()->id()]
        ));

        return response()->json([
            'data' => new LifeFormResource($lifeForm),
        ], 201);
    }

    public function show(LifeForm $lifeForm): JsonResponse
    {
        return response()->json([
            'data' => new LifeFormResource($lifeForm),
        ]);
    }

    public function update(LifeFormRequest $request, LifeForm $lifeForm): JsonResponse
    {
        $lifeForm->update($request->validated());

        return response()->json([
            'data' => new LifeFormResource($lifeForm),
        ]);
    }

    public function destroy(LifeForm $lifeForm): JsonResponse
    {
        $lifeForm->delete();

        return response()->json(null, 204);
    }
}
