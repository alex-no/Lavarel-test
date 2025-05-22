<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Http\Resources\DevelopmentPlanResource;
use App\Models\DevelopmentPlan;

/**
 * DevelopmentPlanController implements the CRUD actions for DevelopmentPlan model.
 *
 * @OA\Tag(
 *     name="DevelopmentPlan",
 *     description="API for working with Development Plan"
 * )
 * @OA\Schema(
 *     schema="DevelopmentPlan",
 *     title="Development Plan",
 *     required={"sort_order", "status", "feature", "technology"},
 *     @OA\Property(property="sort_order", type="integer"),
 *     @OA\Property(property="status", type="string"),
 *     @OA\Property(property="feature", type="string"),
 *     @OA\Property(property="technology", type="string"),
 *     @OA\Property(property="result", type="string", nullable=true)
 * )
 */
class DevelopmentPlanController extends Controller
{
    /**
     * Display a listing of all DevelopmentPlan models.
     *
     * @return array
     *
     * @OA\Get(
     *     path="/api/development-plan?status={status}",
     *     operationId="getDevelopmentPlans",
     *     summary="Retrieve a list of Development Plans",
     *     description="Returns a list of Development Plans from the database",
     *     tags={"About system"},
     *     @OA\Parameter(
     *         name="status",
     *         description="Status of Plan",
     *         in="query",
     *         @OA\Schema(type="string", enum={"pending", "in_progress", "completed"})
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
     *                     @OA\Property(property="sort_order", type="integer"),
     *                     @OA\Property(property="status", type="string", example="in_progress", description="Status of Feature"),
     *                     @OA\Property(property="feature", type="string", example="REST API", description="Feature Name"),
     *                     @OA\Property(property="technology", type="string", example="Yii2, PHP", description="Technology of the feature"),
     *                     @OA\Property(property="result", type="string", nullable=true, example="API", description="result of the feature"),
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
     *         response=404,
     *         description="Development Plan not found"
     *     )
     * )
     */
    public function index(Request $request)
    {
        $request->validate([
            'status' => 'nullable|in:pending,in_progress,completed',
        ]);

        $devPlans = DevelopmentPlan::query()
        ->when($request->has('status'), function ($query) use ($request) {
            $query->where('status', $request->status);
        })
        ->orderBy('status', 'desc')
        ->orderBy('sort_order', 'asc')
        ->paginate($request->get('per_page', 20)); // Default 20 records per page

        $plans = DevelopmentPlanResource::collection($devPlans);

        return  response()->json($plans, 200, ['content-type' => 'application/json']);
    }

    /**
     * Display the specified single resource in the Development Plan model.
     *
     * @param int $id ID
     * @return array|DevelopmentPlan
     * @throws NotFoundHttpException if the model cannot be found
     *
     * @OA\Get(
     *     path="/api/development-plan/{id}",
     *     summary="Get a single Development Plan details",
     *     description="Returns details of a single Development Plan",
     *     tags={"About system"},
     *     operationId="getDevelopmentPlanById",
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         description="ID of the Development Plan",
     *         @OA\Schema(type="integer", example=11)
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Successful response",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="id", type="integer", example=11),
     *             @OA\Property(property="sort_order", type="integer", example=10),
     *             @OA\Property(property="status", type="string", example="in_progress", description="Status of Feature"),
     *             @OA\Property(property="feature_uk", type="string", example="REST API", description="Feature Name in Ukrainian"),
     *             @OA\Property(property="feature_en", type="string", example="REST API", description="Feature Name in English"),
     *             @OA\Property(property="feature_ru", type="string", example="REST API", description="Feature Name in Russian"),
     *             @OA\Property(property="technology_uk", type="string", example="Yii2, PHP", description="Technology of the feature in Ukrainian"),
     *             @OA\Property(property="technology_en", type="string", example="Yii2, PHP", description="Technology of the feature in English"),
     *             @OA\Property(property="technology_ru", type="string", example="Yii2, PHP", description="Technology of the feature in Russian"),
     *             @OA\Property(property="result_uk", type="string", nullable=true, example="API", description="result of the feature in Ukrainian"),
     *             @OA\Property(property="result_en", type="string", nullable=true, example="API", description="result of the feature in English"),
     *             @OA\Property(property="result_ru", type="string", nullable=true, example="API", description="result of the feature in Russian"),
     *             @OA\Property(property="status_adv", type="string", example="🔧 In Progress", description="Status with icon"),
     *             @OA\Property(property="updated_at", type="datetime", example="2025-03-12T20:08:04.566Z", description="Date and time of the last update"),
     *             @OA\Property(property="created_at", type="datetime", example="2025-03-12T20:08:04.566Z", description="Date and time of the creation")
     *         )
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="DevelopmentPlan not found"
     *     )
     * )
     */
    public function show(string $id)
    {
        $plan = DevelopmentPlan::find($id);
        if (!$plan) {
            return response()->json(['message' => 'Not found'], 404);
        }
        // dd($plan);
        return response()->json($plan);
    }

    /**
     * Store a newly created a new Development Plan model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return string|\yii\web\Response
     *
     * @OA\Post(
     *     path="/api/development-plan",
     *     security={{"bearerAuth":{}}},
     *     summary="Create a new Development Plan model",
     *     tags={"About system"},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             type="object",
     *             required={"status", "feature_uk", "feature_en", "feature_ru"},
     *             @OA\Property(property="sort_order", type="integer", example=10),
     *             @OA\Property(property="status", type="string", example="in_progress", description="Status of Feature"),
     *             @OA\Property(property="feature_uk", type="string", example="REST API", description="Feature Name in Ukrainian"),
     *             @OA\Property(property="feature_en", type="string", example="REST API", description="Feature Name in English"),
     *             @OA\Property(property="feature_ru", type="string", example="REST API", description="Feature Name in Russian"),
     *             @OA\Property(property="technology_uk", type="string", example="Yii2, PHP", description="Technology of the feature in Ukrainian"),
     *             @OA\Property(property="technology_en", type="string", example="Yii2, PHP", description="Technology of the feature in English"),
     *             @OA\Property(property="technology_ru", type="string", example="Yii2, PHP", description="Technology of the feature in Russian"),
     *             @OA\Property(property="result_uk", type="string", nullable=true, example="API", description="result of the feature in Ukrainian"),
     *             @OA\Property(property="result_en", type="string", nullable=true, example="API", description="result of the feature in English"),
     *             @OA\Property(property="result_ru", type="string", nullable=true, example="API", description="result of the feature in Russian"),
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
        $validated = $request->validate([
            'feature_en' => 'required|string',
            'feature_ru' => 'nullable|string',
            'feature_uk' => 'nullable|string',
            'result_en' => 'nullable|string',
            'result_ru' => 'nullable|string',
            'result_uk' => 'nullable|string',
            'technology_en' => 'nullable|string',
            'technology_ru' => 'nullable|string',
            'technology_uk' => 'nullable|string',
            'sort_order' => 'nullable|integer',
            'status' => 'nullable|string',
        ]);

        $plan = DevelopmentPlan::create($validated);

        return response()->json($plan, 201);
    }

    /**
     * Update the specified resource in storage.
     * Updates an existing Development Plan model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param int $id ID
     * @return string|\yii\web\Response
     * @throws NotFoundHttpException if the model cannot be found
     *
     * @OA\Put(
     *     path="/api/development-plan/{id}",
     *     security={{"bearerAuth":{}}},
     *     summary="Update a Development Plan model",
     *     tags={"About system"},
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
     *             @OA\Property(property="sort_order", type="integer", example=10),
     *             @OA\Property(property="status", type="string", example="in_progress", description="Status of Feature"),
     *             @OA\Property(property="feature_uk", type="string", example="REST API", description="Feature Name in Ukrainian"),
     *             @OA\Property(property="feature_en", type="string", example="REST API", description="Feature Name in English"),
     *             @OA\Property(property="feature_ru", type="string", example="REST API", description="Feature Name in Russian"),
     *             @OA\Property(property="technology_uk", type="string", example="Yii2, PHP", description="Technology of the feature in Ukrainian"),
     *             @OA\Property(property="technology_en", type="string", example="Yii2, PHP", description="Technology of the feature in English"),
     *             @OA\Property(property="technology_ru", type="string", example="Yii2, PHP", description="Technology of the feature in Russian"),
     *             @OA\Property(property="result_uk", type="string", nullable=true, example="API", description="result of the feature in Ukrainian"),
     *             @OA\Property(property="result_en", type="string", nullable=true, example="API", description="result of the feature in English"),
     *             @OA\Property(property="result_ru", type="string", nullable=true, example="API", description="result of the feature in Russian"),
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
        $plan = DevelopmentPlan::find($id);
        if (!$plan) {
            return response()->json(['message' => 'Not found'], 404);
        }

        $validated = $request->validate([
            'feature_en' => 'sometimes|required|string',
            'feature_ru' => 'nullable|string',
            'feature_uk' => 'nullable|string',
            'result_en' => 'nullable|string',
            'result_ru' => 'nullable|string',
            'result_uk' => 'nullable|string',
            'technology_en' => 'nullable|string',
            'technology_ru' => 'nullable|string',
            'technology_uk' => 'nullable|string',
            'sort_order' => 'nullable|integer',
            'status' => 'nullable|string',
        ]);

        $plan->update($validated);
        return response()->json($plan);
    }

    /**
     * Remove the specified DevelopmentPlan model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     * @param int $id ID
     * @return \yii\web\Response
     * @throws NotFoundHttpException if the model cannot be found
     *
     * @OA\Delete(
     *     path="/api/development-plan/{id}",
     *     security={{"bearerAuth":{}}},
     *     operationId="delete Development Plan",
     *     summary="Delete a Development Plan",
     *     tags={"About system"},
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
        $plan = DevelopmentPlan::find($id);
        if (!$plan) {
            return response()->json(['message' => 'Not found'], 404);
        }

        $plan->delete();
        return response()->json(['message' => 'Deleted successfully']);
    }
}
