<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use App\Http\Resources\PetBreedResource;
use App\Models\PetBreed;

/**
 * @OA\Tag(
 *     name="PetBreed",
 *     description="API for working with Pet Breeds"
 * )
 * @OA\Schema(
 *     schema="PetBreed",
 *     title="PetBreeds",
 *     description="Типы животных",
 * )
 */
class PetBreedController extends Controller
{
    /**
     * @OA\Get(
     *     path="/api/pet-breeds",
     *     summary="Get list of Pet Breeds",
     *     tags={"PetBreeds"},
     *     @OA\Parameter(
     *         name="page",
     *         in="query",
     *         required=false,
     *         description="Page number for pagination",
     *         @OA\Schema(
     *             type="integer",
     *             example=1
     *         )
     *     ),
     *     @OA\Parameter(
     *         name="per_page",
     *         in="query",
     *         required=false,
     *         description="Number of items per page for pagination",
     *         @OA\Schema(
     *             type="integer",
     *             example=10
     *         )
     *     ),
     *     @OA\Parameter(
     *         name="pet_type_id",
     *         in="query",
     *         required=true,
     *         description="ID of the Pet Breed to filter breeds",
     *         @OA\Schema(
     *             type="integer",
     *             format="int64",
     *             example=1,
     *             description="Must be a valid ID from the pet_types table"
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Successful operation",
     *         @OA\JsonContent(
     *             type="array",
     *             @OA\Items(
     *                 type="object",
     *                 @OA\Property(property="id", type="integer", example="1"),
     *                 @OA\Property(property="name", type="string", example="Китайская хохлатая собака"),
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
    public function index(Request $request)
    {
        $request->validate([
            'pet_type_id' => 'required|exists:pet_types,id',
        ]);

        $petBreeds = PetBreed::query()
        ->when($request->has('pet_type_id'), function ($query) use ($request) {
            $query->where('pet_type_id', $request->pet_type_id);
        })
        ->paginate($request->get('per_page', 10)); // По умолчанию 10 записей на страницу

        return PetBreedResource::collection($petBreeds);
    }

    /**
     * @OA\Post(
     *     path="/api/pet-breeds",
     *     summary="Store a new Pet Breed",
     *     description="Creates a new Pet Breed and stores it in the database.",
     *     operationId="storePetBreeds",
     *     tags={"PetBreeds"},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             type="object",
     *             required={"pet_type_id", "name_uk", "name_en", "name_ru"},
     *             @OA\Property(
     *                 property="pet_type_id",
     *                 type="integer",
     *                 example="1",
     *                 description="ID of the Pet Breed"
     *             ),
     *             @OA\Property(
     *                 property="name_uk",
     *                 type="string",
     *                 example="Китайський чубатий собака",
     *                 description="Name of the created Pet Breed in Ukrainian"
     *             ),
     *             @OA\Property(
     *                 property="name_en",
     *                 type="string",
     *                 example="Chinese Crested Dog",
     *                 description="Name of the created Pet Breed in English"
     *             ),
     *             @OA\Property(
     *                 property="name_ru",
     *                 type="string",
     *                 example="Китайская хохлатая собака",
     *                 description="Name of the created Pet Breed in Russian"
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=201,
     *         description="Pet Breed created successfully",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(
     *                 property="id",
     *                 type="integer",
     *                 example="1",
     *                 description="ID of the created Pet Breed"
     *             ),
     *             @OA\Property(
     *                 property="pet_type_id",
     *                 type="integer",
     *                 example="1",
     *                 description="ID of the Pet Breed"
     *             ),
     *             @OA\Property(
     *                 property="name_uk",
     *                 type="string",
     *                 example="Китайський чубатий собака",
     *                 description="Name of the created Pet Breed in Ukrainian"
     *             ),
     *             @OA\Property(
     *                 property="name_en",
     *                 type="string",
     *                 example="Chinese Crested Dog",
     *                 description="Name of the created Pet Breed in English"
     *             ),
     *             @OA\Property(
     *                 property="name_ru",
     *                 type="string",
     *                 example="Китайская хохлатая собака",
     *                 description="Name of the created Pet Breed in Russian"
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
        $validator = Validator::make($request->json()->all(), [
            'pet_type_id' => 'required|exists:pet_types,id',
            'name_uk' => 'required|string|max:255',
            'name_en' => 'required|string|max:255',
            'name_ru' => 'required|string|max:255',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 400);
        }

        $petBreed = PetBreed::create([
            'pet_type_id' => $request->json('pet_type_id'),
            'name_uk' => $request->json('name_uk'),
            'name_en' => $request->json('name_en'),
            'name_ru' => $request->json('name_ru'),
        ]);

        return response()->json([
            'message' => 'Pet Breed created successfully',
            'data' => $petBreed,
        ]);
    }

    /**
     * @OA\Get(
     *     path="/api/pet-breeds/{id}",
     *     summary="Retrieve a specific resource by ID",
     *     tags={"PetBreeds"},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         description="ID of the Pet Breed to retrieve",
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
     *                 description="ID of the requested Pet Breed"
     *             ),
     *             @OA\Property(
     *                 property="pet_type_id",
     *                 type="integer",
     *                 example="1",
     *                 description="ID of the Pet Breed"
     *             ),
     *             @OA\Property(
     *                 property="name_uk",
     *                 type="string",
     *                 example="Китайський чубатий собака",
     *                 description="Name of the requested Pet Breed in Ukrainian"
     *             ),
     *             @OA\Property(
     *                 property="name_en",
     *                 type="string",
     *                 example="Chinese Crested Dog",
     *                 description="Name of the requested Pet Breed in English"
     *             ),
     *             @OA\Property(
     *                 property="name_ru",
     *                 type="string",
     *                 example="Китайская хохлатая собака",
     *                 description="Name of the requested Pet Breed in Russian"
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
     *         description="Pet Breed not found"
     *     )
     * )
     */
    public function show(string $id)
    {
        return PetBreed::findOrFail($id);
    }

    /**
     * @OA\Put(
     *     path="/api/pet-breeds/{id}",
     *     summary="Update an existing Pet Breed",
     *     description="Updates the details of an existing Pet Breed by its ID.",
     *     operationId="updatePetBreed",
     *     tags={"PetBreeds"},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         description="ID of the Pet Breed to update",
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
     *                 property="pet_type_id",
     *                 type="integer",
     *                 format="int64",
     *                 example="1",
     *                 description="ID of the Pet Breed"
     *             ),
     *             @OA\Property(
     *                 property="name_uk",
     *                 type="string",
     *                 example="Китайський чубатий собака",
     *                 description="Name of the updated Pet Breed in Ukrainian"
     *             ),
     *             @OA\Property(
     *                 property="name_en",
     *                 type="string",
     *                 example="Chinese Crested Dog",
     *                 description="Name of the updated Pet Breed in English"
     *             ),
     *             @OA\Property(
     *                 property="name_ru",
     *                 type="string",
     *                 example="Китайская хохлатая собака",
     *                 description="Name of the updated Pet Breed in Russian"
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Pet Breed updated successfully",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(
     *                 property="id",
     *                 type="integer",
     *                 example="1",
     *                 description="ID of the updated Pet Breed"
     *             ),
     *             @OA\Property(
     *                 property="pet_type_id",
     *                 type="integer",
     *                 example="1",
     *                 description="ID of the Pet Breed"
     *             ),
     *             @OA\Property(
     *                 property="name_uk",
     *                 type="string",
     *                 example="Китайський чубатий собака",
     *                 description="Name of the updated Pet Breed in Ukrainian"
     *             ),
     *             @OA\Property(
     *                 property="name_en",
     *                 type="string",
     *                 example="Chinese Crested Dog",
     *                 description="Name of the updated Pet Breed in English"
     *             ),
     *             @OA\Property(
     *                 property="name_ru",
     *                 type="string",
     *                 example="Китайская хохлатая собака",
     *                 description="Name of the updated Pet Breed in Russian"
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
        $validator = Validator::make($request->json()->all(), [
            'pet_type_id' => 'sometimes|exists:pet_types,id',
            'name_uk' => 'sometimes|string|max:255',
            'name_en' => 'sometimes|string|max:255',
            'name_ru' => 'sometimes|string|max:255',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 400);
        }

        $petBreed = PetBreed::findOrFail($id);

        $validData = $validator->validated();

        if (!empty($validData)) {
            $petBreed->update($validData);
        }

        return response()->json([
            'message' => 'Pet breed updated successfully',
            'data' => $petBreed,
        ]);
    }

    /**
     * @OA\Delete(
     *     path="/api/pet-breeds/{id}",
     *     summary="Delete a Pet Breed",
     *     description="Deletes a PetBreed by its ID",
     *     operationId="destroyPetBreed",
     *     tags={"PetBreeds"},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         description="ID of the Pet Breed to delete",
     *         required=true,
     *         @OA\Schema(
     *             type="integer"
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Pet Breed deleted successfully",
     *         @OA\JsonContent(
     *             @OA\Property(
     *                 property="message",
     *                 type="string",
     *                 example="Pet Breed deleted successfully"
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
        $petBreed = PetBreed::findOrFail($id);
        $petBreed->delete();

        return response()->json(null, 204);
    }
}
