<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
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
 *     description="Pet Types",
 * )
 */
class PetTypeController extends Controller
{
    public function __construct()
    {
        // Only store, update, and destroy actions require authentication
        $this->middleware('auth:sanctum')->only(['store', 'update', 'destroy']);
    }

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
     * @OA\Post(
     *     path="/api/pet-types",
     *     security={{"bearerAuth":{}}},
     *     summary="Store a new Pet Type",
     *     description="Creates a new Pet Type and stores it in the database.",
     *     operationId="storePetTypes",
     *     tags={"PetTypes"},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             type="object",
     *             required={"name_uk", "name_en", "name_ru"},
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
        $this->authorizeRole();

        $validator = Validator::make($request->json()->all(), [
            'name_uk' => 'required|string|max:255',
            'name_en' => 'required|string|max:255',
            'name_ru' => 'required|string|max:255',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 400);
        }

        $petType = PetType::create([
            'name_uk' => $request->json('name_uk'),
            'name_en' => $request->json('name_en'),
            'name_ru' => $request->json('name_ru'),
        ]);

        return response()->json([
            'message' => 'Pet type created successfully',
            'data' => $petType,
        ]);
    }

    /**
     * @OA\Put(
     *     path="/api/pet-types/{id}",
     *     security={{"bearerAuth":{}}},
     *     summary="Update an existing Pet Type",
     *     description="Updates the details of an existing Pet Type by its ID.",
     *     operationId="updatePetType",
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
        $this->authorizeRole();

        $validator = Validator::make($request->json()->all(), [
            'name_uk' => 'sometimes|string|max:255',
            'name_en' => 'sometimes|string|max:255',
            'name_ru' => 'sometimes|string|max:255',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 400);
        }

        $petType = PetType::findOrFail($id);

        $validData = $validator->validated();

        if (!empty($validData)) {
            $petType->update($validData);
        }

        return response()->json([
            'message' => 'Pet type updated successfully',
            'data' => $petType,
        ]);
    }

    /**
     * @OA\Delete(
     *     path="/api/pet-types/{id}",
     *     security={{"bearerAuth":{}}},
     *     summary="Delete a Pet Type",
     *     description="Deletes a PetType by its ID",
     *     operationId="destroyPetType",
     *     tags={"PetTypes"},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         description="ID of the Pet Type to delete",
     *         required=true,
     *         @OA\Schema(
     *             type="integer"
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Pet Type deleted successfully",
     *         @OA\JsonContent(
     *             @OA\Property(
     *                 property="message",
     *                 type="string",
     *                 example="Pet Type deleted successfully"
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Resource not found",
     *         @OA\JsonContent(
     *             @OA\Property(
     *                 property="error",
     *                 type="string",
     *                 example="Resource not found"
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=500,
     *         description="Internal server error",
     *         @OA\JsonContent(
     *             @OA\Property(
     *                 property="error",
     *                 type="string",
     *                 example="Internal server error"
     *             )
     *         )
     *     )
     * )
     */
    public function destroy(string $id)
    {
        $this->authorizeRole(['roleSuperadmin']);

        $petType = PetType::findOrFail($id);
        $petType->delete();

        return response()->json(null, 204);
    }
}
