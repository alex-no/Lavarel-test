<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Auth;
use App\Http\Resources\PetOwnerResource;
use App\Http\Requests\IndexPetOwnerRequest;
use App\Models\User;
use App\Models\PetOwner;

/**
 * @OA\Tag(
 *     name="PetOwner",
 *     description="API for working with Pet Owners"
 * )
 * @OA\Schema(
 *     schema="PetOwner",
 *     description="Pet Owners",
 *     required={"user_id", "pet_type_id", "pet_breed_id", "nickname"},
 *     @OA\Property(property="user_id", type="integer"),
 *     @OA\Property(property="pet_type_id", type="integer"),
 *     @OA\Property(property="pet_breed_id", type="integer"),
 *     @OA\Property(property="nickname", type="string"),
 *     @OA\Property(property="description", type="string", nullable=true)
 * )
 */
class PetOwnerController extends Controller
{
    public function __construct()
    {
        Gate::define('access-pets', function (User $user) {
            return $user->hasAnyRole(['roleUser', 'roleAdmin', 'roleSuperadmin']);
        });
    }

    /**
     * Lists all PetOwner models.
     *
     * @return array
     *
     * @OA\Get(
     *     path="/api/pet-owners?userId={userId}&petTypeId={petTypeId}&petBreedId={petBreedId}",
     *     security={{"bearerAuth":{}}},
     *     operationId="getPetOwners",
     *     summary="Retrieve a list of Pet Owners",
     *     description="Returns a list of Pet Owners from the database",
     *     tags={"PetOwner"},
     *     @OA\Parameter(
     *         name="userId",
     *         description="ID of the user",
     *         in="query",
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Parameter(
     *         name="petTypeId",
     *         description="ID of the pet type",
     *         in="query",
     *         required=false,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Parameter(
     *         name="petBreedId",
     *         description="ID of the pet breed",
     *         in="query",
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Successful response",
     *         @OA\JsonContent(
     *             @OA\Property(
     *                 property="items",
     *                 type="array",
     *                 @OA\Items(
     *                     type="object",
     *                     @OA\Property(property="id", type="integer", example=11),
     *                     @OA\Property(property="user_id", type="integer"),
     *                     @OA\Property(property="owner", type="string", example="John Doe", description="Name of Owner"),
     *                     @OA\Property(property="pet_type_id", type="integer", example="1", description="ID of the Pet Type"),
     *                     @OA\Property(property="type", type="string", example="Dog", description="Type of the Pet in Current language"),
     *                     @OA\Property(property="pet_breed_id", type="integer", example="1", description="ID of the Pet Breed"),
     *                     @OA\Property(property="breed", type="string", example="German Shepherd", description="Breed of the Pet in Current language"),
     *                     @OA\Property(property="nickname", type="string", example="Sharick", description="Nickname of the Pet in Current language"),
     *                     @OA\Property(property="year_of_birth", type="integer", example="2020", description="Year of birth"),
     *                     @OA\Property(property="age", type="integer", example="5", description="Age of pet"),
     *                     @OA\Property(property="updated_at", type="datetime", example="2025-03-12T20:08:04.566Z", description="Date and time of the last update"),
     *                     @OA\Property(property="created_at", type="datetime", example="2025-03-12T20:08:04.566Z", description="Date and time of the creation")
     *                 ),
     *                 @OA\Property(
     *                     property="_meta",
     *                     type="object",
     *                     @OA\Property(property="totalCount", type="integer", example=16),
     *                     @OA\Property(property="pageCount", type="integer", example=2),
     *                     @OA\Property(property="currentPage", type="integer", example=2),
     *                     @OA\Property(property="perPage", type="integer", example=10)
     *                 )
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=400,
     *         description="At least one of userId, petTypeId or petBreedId is required"
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Pet type not found"
     *     )
     * )
     */
    public function index(IndexPetOwnerRequest $request)
    {
        if (Gate::denies('access-pets')) {
            abort(403, 'You are not allowed to access this page.');
        }

        $petOwner = PetOwner::query();

        // Retrieving a collection of roles
        $user = Auth::user();

        $userId = $request->userId ?? null;
        $petTypeId = $request->petTypeId ?? null;
        $petBreedId = $request->petBreedId ?? null;

        // If the user has the role "roleUser" — they can only see their own records
        if ($user->hasRole('roleUser')) {
            $userId = $user->id;
        }

        // If nothing is passed — restrict the user to their ID
        if (empty($userId) && empty($petTypeId) && empty($petBreedId)) {
            $userId = $user->id;
        }

        // Apply filters if they are set
        $petOwner->when($userId, function ($query, $userId) {
            return $query->where('user_id', $userId);
        })->when($petTypeId, function ($query, $petTypeId) {
            return $query->where('pet_type_id', $petTypeId);
        })->when($petBreedId, function ($query, $petBreedId) {
            return $query->where('pet_breed_id', $petBreedId);
        });

        $result = $petOwner->paginate($request->get('per_page', 10)); // Default 10 records per page

        return PetOwnerResource::collection($result);
    }

    /**
     * Displays a single PetOwner model.
     *
     * @param string $id ID
     * @return array|PetOwner
     * @throws NotFoundHttpException if the model cannot be found
     *
     * @OA\Get(
     *     path="/api/pet-owners/{id}",
     *     security={{"bearerAuth":{}}},
     *     summary="Get a single pet and owner",
     *     tags={"PetOwner"},
     *     operationId="getPetOwnerById",
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         description="ID of the pet and owner",
     *         @OA\Schema(type="integer", example=3)
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Successful response",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="id", type="integer", example=3),
     *             @OA\Property(property="user_id", type="integer", example=1),
     *             @OA\Property(property="pet_type_id", type="integer", example=1),
     *             @OA\Property(property="pet_breed_id", type="integer", example=1),
     *             @OA\Property(property="nickname_uk", type="string", example="Шарік"),
     *             @OA\Property(property="nickname_en", type="string", example="Sharick"),
     *             @OA\Property(property="nickname_ru", type="string", example="Шарик"),
     *             @OA\Property(property="year_of_birth", type="integer", example=2020),
     *             @OA\Property(property="updated_at", type="string", format="date-time", example="2025-03-12 03:15:20"),
     *             @OA\Property(property="user_name", type="string", example="Petro"),
     *             @OA\Property(property="pet_type_name", type="string", example="dog"),
     *             @OA\Property(property="pet_breed_name", type="string", example="Chinese Crested Dog")
     *         )
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="PetOwner not found"
     *     )
     * )
     */
    public function show(string $id)
    {
        return PetOwner::findOrFail($id);
    }

    /**
     * Store a newly created PetOwner model.
     * If creation is successful, the browser will be redirected to the 'show' page.
     * @param string|\Illuminate\Http\Request
     *
     * @OA\Post(
     *     path="/api/pet-owners",
     *     security={{"bearerAuth":{}}},
     *     summary="Create a new pet and owner",
     *     tags={"PetOwner"},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             type="object",
     *             required={"pet_breed_id"},
     *             @OA\Property(property="user_id", type="integer", example=1),
     *             @OA\Property(property="pet_breed_id", type="integer", example=1),
     *             @OA\Property(property="nickname_uk", type="string", example="Шарік"),
     *             @OA\Property(property="nickname_en", type="string", example="Sharick"),
     *             @OA\Property(property="nickname_ru", type="string", example="Шарик"),
     *             @OA\Property(property="year_of_birth", type="integer", example=2020)
     *         )
     *     ),
     *     @OA\Response(
     *         response=201,
     *         description="Created"
     *     ),
     *     @OA\Response(
     *         response=400,
     *         description="Validation error"
     *     )
     * )
     */
    public function store(Request $request)
    {
        if (Gate::denies('access-pets')) {
            abort(403, 'You are not allowed to perform this action.');
        }

        $validator = Validator::make($request->json()->all(), [
            'user_id' => 'integer',
            'pet_breed_id' => 'integer',
            'nickname_uk' => 'required|string|max:255',
            'nickname_en' => 'required|string|max:255',
            'nickname_ru' => 'required|string|max:255',
            'year_of_birth' => 'integer|max:2025',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 400);
        }

        $petOwner = PetOwner::create([
            'user_id' => $request->json('year_of_birth'),
            'pet_breed_id' => $request->json('pet_breed_id'),
            'nickname_uk' => $request->json('nickname_uk'),
            'nickname_en' => $request->json('nickname_en'),
            'nickname_ru' => $request->json('nickname_ru'),
            'year_of_birth' => $request->json('year_of_birth'),
        ]);

        return response()->json([
            'message' => 'Pet type created successfully',
            'data' => $petOwner,
        ]);
    }

    /**
     * Updates an existing PetOwner model.
     * If update is successful, the browser will be redirected to the 'show' page.
     * @param string|\Illuminate\Http\Request
     * @param int $id ID
     * @throws NotFoundHttpException if the model cannot be found
     *
     * @OA\Put(
     *     path="/api/pet-owners/{id}",
     *     security={{"bearerAuth":{}}},
     *     summary="Update a pet and owner",
     *     tags={"PetOwner"},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="user_id", type="integer", example=1),
     *             @OA\Property(property="pet_breed_id", type="integer", example=1),
     *             @OA\Property(property="nickname_uk", type="string", example="Шарік"),
     *             @OA\Property(property="nickname_en", type="string", example="Sharick"),
     *             @OA\Property(property="nickname_ru", type="string", example="Шарик"),
     *             @OA\Property(property="year_of_birth", type="integer", example=2020)
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Updated"
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Not found"
     *     ),
     *     @OA\Response(
     *         response=400,
     *         description="Validation error"
     *     )
     * )
     */
    public function update(Request $request, string $id)
    {
        $validator = Validator::make($request->json()->all(), [
            'user_id' => $request->json('year_of_birth'),
            'pet_breed_id' => $request->json('pet_breed_id'),
            'nickname_uk' => $request->json('nickname_uk'),
            'nickname_en' => $request->json('nickname_en'),
            'nickname_ru' => $request->json('nickname_ru'),
            'year_of_birth' => $request->json('year_of_birth'),
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 400);
        }

        $petOwner = PetOwner::findOrFail($id);

        $validData = $validator->validated();

        if (!empty($validData)) {
            $petOwner->update($validData);
        }

        return response()->json([
            'message' => 'Pet type updated successfully',
            'data' => $petOwner,
        ]);
    }

    /**
     * Deletes an existing PetOwner model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     * @param int $id ID
     * @throws NotFoundHttpException if the model cannot be found
     *
     * @OA\Delete(
     *     path="/api/pet-owners/{id}",
     *     security={{"bearerAuth":{}}},
     *     operationId="destroyPetOwner",
     *     summary="Delete a pet owner data by ID",
     *     tags={"PetOwner"},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(
     *         response=204,
     *         description="Deleted"
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Not found"
     *     ),
     * )
     */
    public function destroy(string $id)
    {
        $petType = PetOwner::findOrFail($id);
        $petType->delete();

        return response()->json(null, 204);
    }
}
