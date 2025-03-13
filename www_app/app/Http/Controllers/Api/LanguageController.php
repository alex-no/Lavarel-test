<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\LanguageResource;
use App\Models\Language;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Lang;

class LanguageController extends Controller
{
    /**
     * @OA\Get(
     *     path="/api/languages",
     *     summary="Get list of Languages",
     *     tags={"Languages"},
     *     @OA\Response(
     *         response=200,
     *         description="Successful operation",
     *         @OA\JsonContent(
     *             type="array",
     *             @OA\Items(
     *                 type="object",
     *                 @OA\Property(property="code", type="string", example="en"),
     *                 @OA\Property(property="short_name", type="string", example="Eng"),
     *                 @OA\Property(property="full_name", type="string", example="English")
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
        return LanguageResource::collection(Language::where('is_enabled', true)->orderBy('order')->get());
    }

    /**
     * @OA\Post(
     *     path="/api/languages",
     *     summary="Store a new Language",
     *     description="Creates a new Language and stores it in the database.",
     *     operationId="storeLanguage",
     *     tags={"Languages"},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             type="object",
     *             required={"code", "short_name", "full_name"},
     *             @OA\Property(
     *                 property="code",
     *                 type="string",
     *                 example="en",
     *                 description="Code of Language"
     *             ),
     *             @OA\Property(
     *                 property="short_name",
     *                 type="string",
     *                 example="Eng",
     *                 description="Short name of Language"
     *             ),
     *             @OA\Property(
     *                 property="full_name",
     *                 type="string",
     *                 example="English",
     *                 description="Full name of Language"
     *             ),
     *             @OA\Property(
     *                 property="is_enabled",
     *                 type="boolean",
     *                 example=true,
     *                 description="Is enabled Language"
     *             ),
     *             @OA\Property(
     *                 property="order",
     *                 type="integer",
     *                 example=1,
     *                 description="Order of Language"
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=201,
     *         description="Languages created successfully",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(
     *                 property="code",
     *                 type="string",
     *                 example="en",
     *                 description="Code of created Language"
     *             ),
     *             @OA\Property(
     *                 property="short_name",
     *                 type="string",
     *                 example="Eng",
     *                 description="Short name of created Language"
     *             ),
     *             @OA\Property(
     *                 property="full_name",
     *                 type="string",
     *                 example="English",
     *                 description="Full name of created Language"
     *             ),
     *             @OA\Property(
     *                 property="is_enabled",
     *                 type="boolean",
     *                 example=true,
     *                 description="Is enabled Language"
     *             ),
     *             @OA\Property(
     *                 property="order",
     *                 type="integer",
     *                 example=1,
     *                 description="Order of Language"
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
            'code' => 'required|string|max:2',
            'short_name' => 'required|string|max:3',
            'full_name' => 'required|string|max:255',
            'is_enabled' => 'sometimes|boolean',
            'order' => 'sometimes|integer',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 400);
        }

        $petBreed = Language::create([
            'code' => $request->json('code'),
            'short_name' => $request->json('short_name'),
            'full_name' => $request->json('full_name'),
            'is_enabled' => $request->json('is_enabled', true),
            'order' => $request->json('order', 10),
        ]);

        return response()->json([
            'message' => 'Language created successfully',
            'data' => $petBreed,
        ]);
    }

    /**
     * Display the specified resource.
     */
    public function show(string $code)
    {
        return Language::findOrFail($code);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $code)
    {
        $validator = Validator::make($request->json()->all(), [
            'code' => 'sometimes|string|max:2',
            'short_name' => 'sometimes|string|max:3',
            'full_name' => 'sometimes|string|max:255',
            'is_enabled' => 'sometimes|boolean',
            'order' => 'sometimes|integer',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 400);
        }

        $language = Language::findOrFail($code);

        $validData = $validator->validated();

        if (!empty($validData)) {
            $language->update($validData);
        }

        return response()->json([
            'message' => 'Language updated successfully',
            'data' => $language,
        ]);
    }

}
