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
 *     title="PetTypes",
 *     description="Типы животных",
 * )
 */
class PetTypeController extends Controller
{
    /**
     * @OA\Get(
     *     path="/api/pet-types",
     *     summary="Get list of Pet Types",
     *     tags={"PetTypes"},
     *     @OA\Response(
     *         response=200,
     *         description="Successful operation",
     *         @OA\JsonContent(
     *             type="array",
     *             @OA\Items(
     *                 type="object",
     *                 @OA\Property(property="id", type="integer", example="1"),
     *                 @OA\Property(property="name", type="string", example="собака"),
     *                 @OA\Property(property="updated_at", type="datetime", example="2025-03-12T20:08:04.566Z")
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
     * @OA\Post(
     *     path="/api/pet-types",
     *     summary="Store a new Pet Type",
     *     description="Creates a new Pet Type and stores it in the database.",
     *     operationId="storePetTypes",
     *     tags={"PetTypes"},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(
     *                 property="name_uk",
     *                 type="string",
     *                 example="собака",
     *                 description="Name of the created Pet Type in Ukrainian"
     *             ),
     *             @OA\Property(
     *                 property="name_en",
     *                 type="string",
     *                 example="dog",
     *                 description="Name of the created Pet Type in English"
     *             ),
     *             @OA\Property(
     *                 property="name_ru",
     *                 type="string",
     *                 example="собака",
     *                 description="Name of the created Pet Type in Russian"
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=201,
     *         description="Pet Type created successfully",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(
     *                 property="id",
     *                 type="integer",
     *                 example="1",
     *                 description="ID of the created Pet Type"
     *             ),
     *             @OA\Property(
     *                 property="name_uk",
     *                 type="string",
     *                 example="собака",
     *                 description="Name of the created Pet Type in Ukrainian"
     *             ),
     *             @OA\Property(
     *                 property="name_en",
     *                 type="string",
     *                 example="dog",
     *                 description="Name of the created Pet Type in English"
     *             ),
     *             @OA\Property(
     *                 property="name_ru",
     *                 type="string",
     *                 example="собака",
     *                 description="Name of the created Pet Type in Russian"
     *             ),
     *             @OA\Property(
     *                 property="updated_at",
     *                 type="datetime",
     *                 example="2025-03-12T20:08:04.566Z",
     *                 description="Date and time of the last update"
     *             ),
     *             @OA\Property(
     *                 property="created_at",
     *                 type="datetime",
     *                 example="2025-03-12T20:08:04.566Z",
     *                 description="Date and time of the creation"
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=400,
     *         description="Bad request"
     *     ),
     *     @OA\Response(
     *         response=500,
     *         description="Internal server error"
     *     )
     * )
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
     * @OA\Get(
     *     path="/api/pet-types/{id}",
     *     summary="Retrieve a specific resource by ID",
     *     tags={"PetTypes"},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         description="ID of the Pet Type to retrieve",
     *         @OA\Schema(
     *             type="integer"
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Successful retrieval of the resource",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(
     *                 property="id",
     *                 type="integer",
     *                 example="1",
     *                 description="ID of the requested Pet Type"
     *             ),
     *             @OA\Property(
     *                 property="name_uk",
     *                 type="string",
     *                 example="собака",
     *                 description="Name of the requested Pet Type in Ukrainian"
     *             ),
     *             @OA\Property(
     *                 property="name_en",
     *                 type="string",
     *                 example="dog",
     *                 description="Name of the requested Pet Type in English"
     *             ),
     *             @OA\Property(
     *                 property="name_ru",
     *                 type="string",
     *                 example="собака",
     *                 description="Name of the requested Pet Type in Russian"
     *             ),
     *             @OA\Property(
     *                 property="updated_at",
     *                 type="datetime",
     *                 example="2025-03-12T20:08:04.566Z",
     *                 description="Date and time of the last update"
     *             ),
     *             @OA\Property(
     *                 property="created_at",
     *                 type="datetime",
     *                 example="2025-03-12T20:08:04.566Z",
     *                 description="Date and time of the creation"
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Resource not found"
     *     )
     * )
     */
    public function show(string $id)
    {
        return PetType::findOrFail($id);
    }

    /**
     * @OA\Put(
     *     path="/api/resource/{id}",
     *     summary="Update an existing Pet Type",
     *     description="Updates the details of an existing Pet Type by its ID.",
     *     operationId="updateResource",
     *     tags={"PetTypes"},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         description="ID of the Pet Type to update",
     *         required=true,
     *         @OA\Schema(
     *             type="integer"
     *         )
     *     ),
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(
     *                 property="name_uk",
     *                 type="string",
     *                 example="собака",
     *                 description="Name of the updated Pet Type in Ukrainian"
     *             ),
     *             @OA\Property(
     *                 property="name_en",
     *                 type="string",
     *                 example="dog",
     *                 description="Name of the updated Pet Type in English"
     *             ),
     *             @OA\Property(
     *                 property="name_ru",
     *                 type="string",
     *                 example="собака",
     *                 description="Name of the updated Pet Type in Russian"
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Resource updated successfully",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(
     *                 property="id",
     *                 type="integer",
     *                 example="1",
     *                 description="ID of the updated Pet Type"
     *             ),
     *             @OA\Property(
     *                 property="name_uk",
     *                 type="string",
     *                 example="собака",
     *                 description="Name of the updated Pet Type in Ukrainian"
     *             ),
     *             @OA\Property(
     *                 property="name_en",
     *                 type="string",
     *                 example="dog",
     *                 description="Name of the updated Pet Type in English"
     *             ),
     *             @OA\Property(
     *                 property="name_ru",
     *                 type="string",
     *                 example="собака",
     *                 description="Name of the updated Pet Type in Russian"
     *             ),
     *             @OA\Property(
     *                 property="updated_at",
     *                 type="datetime",
     *                 example="2025-03-12T20:08:04.566Z",
     *                 description="Date and time of the last update"
     *             ),
     *             @OA\Property(
     *                 property="created_at",
     *                 type="datetime",
     *                 example="2025-03-12T20:08:04.566Z",
     *                 description="Date and time of the creation"
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Resource not found",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(
     *                 property="error",
     *                 type="string"
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=400,
     *         description="Invalid input",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(
     *                 property="error",
     *                 type="string"
     *             )
     *         )
     *     )
     * )
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
