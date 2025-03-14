<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Http\Resources\PetOwnerResource;
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
     * Display a listing of the resource.
     */
    public function index()
    {
        return PetOwnerResource::collection(PetOwner::all());
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
