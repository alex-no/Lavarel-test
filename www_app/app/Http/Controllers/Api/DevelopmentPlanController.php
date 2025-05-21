<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\DevelopmentPlan;

class DevelopmentPlanController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $plans = DevelopmentPlan::orderBy('sort_order')->get();
        return response()->json($plans);
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $plan = DevelopmentPlan::find($id);
        if (!$plan) {
            return response()->json(['message' => 'Not found'], 404);
        }
        return response()->json($plan);
    }

    /**
     * Store a newly created resource in storage.
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
     * Remove the specified resource from storage.
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
