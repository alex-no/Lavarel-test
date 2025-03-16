<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Http\Resources\PetOwnerResource;
use App\Http\Requests\IndexPetOwnerRequest;
use App\Models\PetOwner;

/**
 * @OA\Tag(
 *     name="PetOwner",
 *     description="API for working with Pet Owners"
 * )
 * @OA\Schema(
 *     schema="PetOwner",
 *     title="PetOwners",
 *     description="Владельцы животных",
 * )
 */
class PetOwnerController extends Controller
{
    /**
     * @OA\Get(
     *     path="/api/resource",
     *     summary="Retrieve a list of Pet Owners",
     *     description="Returns a list of Pet Owners from the database",
     *     operationId="getPetOwners",
     *     tags={"PetOwner"},
     *     @OA\Response(
     *         response=200,
     *         description="Successful operation",
     *         @OA\JsonContent(
     *             type="object",
     *             title="PetOwners",
     *             description="Владельцы животных",
     *             @OA\Property(
     *                 property="id",
     *                 type="integer",
     *                 example="1",
     *                 description="ID of the Pet"
     *             ),
     *             @OA\Property(
     *                 property="user_id",
     *                 type="integer",
     *                 example="1",
     *                 description="ID of owner"
     *             ),
     *             @OA\Property(
     *                 property="owner",
     *                 type="string",
     *                 example="John Doe",
     *                 description="Name of owner"
     *             ),
     *             @OA\Property(
     *                 property="pet_type_id",
     *                 type="integer",
     *                 example="1",
     *                 description="ID of the Pet Type"
     *             ),
     *             @OA\Property(
     *                 property="type",
     *                 type="string",
     *                 example="Dog",
     *                 description="Type of the Pet in Currrent language"
     *             ),
     *             @OA\Property(
     *                 property="pet_breed_id",
     *                 type="integer",
     *                 example="1",
     *                 description="ID of the Pet Breed"
     *             ),
     *             @OA\Property(
     *                 property="breed",
     *                 type="string",
     *                 example="German Shepherd",
     *                 description="Breed of the Pet in Currrent language"
     *             ),
     *             @OA\Property(
     *                 property="nickname",
     *                 type="string",
     *                 example="Lord",
     *                 description="Nickname of the Pet in Currrent language"
     *             ),
     *             @OA\Property(
     *                 property="year_of_birth",
     *                 type="integer",
     *                 example="2020",
     *                 description="Year of birth"
     *             ),
     *             @OA\Property(
     *                 property="age",
     *                 type="integer",
     *                 example="5",
     *                 description="Age of pet"
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
     *         response=401,
     *         description="Unauthorized"
     *     ),
     *     @OA\Response(
     *         response=500,
     *         description="Internal server error"
     *     )
     * )
     */
    public function index(IndexPetOwnerRequest $request)
    {
        $petOwner = PetOwner::query();
        if ($request->user_id) {
            $petOwner->when($request->has('user_id'), function ($query) use ($request) {
                $query->where('user_id', $request->user_id);
            });
        }
        $result = $petOwner->paginate($request->get('per_page', 10)); // Default 10 records per page

        return PetOwnerResource::collection($result);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }
}
