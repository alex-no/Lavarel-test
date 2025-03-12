<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Http\Resources\PetTypeResource;
use App\Models\PetType;

/**
 * @OA\Tag(
 *     name="PetType",
 *     description="API for working with Pet Types"
 * )
 * @OA\Schema(
 *     schema="PetType",
 *     title="Pet Type",
 *     description="Типы животных",
 * )
 */
class PetTypeController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    /**
     * @OA\Get(
     *     path="/api/pet-types",
     *     summary="Get list of pet types",
     *     tags={"PetTypes"},
     *     @OA\Response(
     *         response=200,
     *         description="Successful operation",
     *         @OA\JsonContent(
     *             type="array",
     *             @OA\Items(
     *                 type="object",
     *                 @OA\Property(property="id", type="integer"),
     *                 @OA\Property(property="name", type="string"),
     *                 @OA\Property(property="updated_at", type="datetime")
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=401,
     *         description="Unauthorized"
     *     ),
     *     @OA\Response(
     *         response=403,
     *         description="Forbidden"
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Not Found"
     *     )
     * )
     */
    public function index()
    {
        return PetTypeResource::collection(PetType::all());
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'name_uk' => 'required|string|max:255',
            'name_en' => 'required|string|max:255',
            'name_ru' => 'required|string|max:255',
        ]);

        $petType = PetType::create([
            'name_uk' => $request->input('name_uk'),
            'name_en' => $request->input('name_en'),
            'name_ru' => $request->input('name_ru'),
        ]);

        return new PetTypeResource($petType);
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        return PetType::findOrFail($id);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $request->validate([
            'name_uk' => 'sometimes|string|max:255',
            'name_en' => 'sometimes|string|max:255',
            'name_ru' => 'sometimes|string|max:255',
        ]);

        $petType = PetType::findOrFail($id);

        $petType->update($request->only(['name_uk', 'name_en', 'name_ru']));

        return new PetTypeResource($petType);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $petType = PetType::findOrFail($id);
        $petType->delete();

        return response()->json(null, 204);
    }
}
