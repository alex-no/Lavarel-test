<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
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
 */class PetBreedController extends Controller
{
    /**
     * @OA\Get(
     *     path="/api/pet-breeds",
     *     summary="Get list of Pet Breeds",
     *     tags={"PetBreeds"},
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
    public function index(Request $request)
    {
        $request->validate([
            'pet_type_id' => 'required|exists:pet_types,id',
        ]);

        $query = PetBreed::query();
        $query->where('pet_type_id', $request->pet_type_id);

        return PetBreedResource::collection($query->get());
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
     *             @OA\Property(
     *                 property="name_uk",
     *                 type="string",
     *                 example="собака",
     *                 description="Name of the created Pet Breed in Ukrainian"
     *             ),
     *             @OA\Property(
     *                 property="name_en",
     *                 type="string",
     *                 example="dog",
     *                 description="Name of the created Pet Breed in English"
     *             ),
     *             @OA\Property(
     *                 property="name_ru",
     *                 type="string",
     *                 example="собака",
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
     *                 property="name_uk",
     *                 type="string",
     *                 example="собака",
     *                 description="Name of the created Pet Breed in Ukrainian"
     *             ),
     *             @OA\Property(
     *                 property="name_en",
     *                 type="string",
     *                 example="dog",
     *                 description="Name of the created Pet Breed in English"
     *             ),
     *             @OA\Property(
     *                 property="name_ru",
     *                 type="string",
     *                 example="собака",
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
        //
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
     *                 property="name_uk",
     *                 type="string",
     *                 example="собака",
     *                 description="Name of the requested Pet Breed in Ukrainian"
     *             ),
     *             @OA\Property(
     *                 property="name_en",
     *                 type="string",
     *                 example="dog",
     *                 description="Name of the requested Pet Breed in English"
     *             ),
     *             @OA\Property(
     *                 property="name_ru",
     *                 type="string",
     *                 example="собака",
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
     *         description="Resource not found"
     *     )
     * )
     */
    public function show(string $id)
    {
        //
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
     *                 property="name_uk",
     *                 type="string",
     *                 example="собака",
     *                 description="Name of the updated Pet Breed in Ukrainian"
     *             ),
     *             @OA\Property(
     *                 property="name_en",
     *                 type="string",
     *                 example="dog",
     *                 description="Name of the updated Pet Breed in English"
     *             ),
     *             @OA\Property(
     *                 property="name_ru",
     *                 type="string",
     *                 example="собака",
     *                 description="Name of the updated Pet Breed in Russian"
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
     *                 description="ID of the updated Pet Breed"
     *             ),
     *             @OA\Property(
     *                 property="name_uk",
     *                 type="string",
     *                 example="собака",
     *                 description="Name of the updated Pet Breed in Ukrainian"
     *             ),
     *             @OA\Property(
     *                 property="name_en",
     *                 type="string",
     *                 example="dog",
     *                 description="Name of the updated Pet Breed in English"
     *             ),
     *             @OA\Property(
     *                 property="name_ru",
     *                 type="string",
     *                 example="собака",
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
        //
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
        //
    }
}
