<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\LanguageController;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\DatabaseController;

//Route::apiResource('languages', LanguageController::class);
Route::get('/languages', [LanguageController::class, 'index']);
Route::get('/languages/{language}', [LanguageController::class, 'show']);
Route::post('/languages', [LanguageController::class, 'store']);
// Route::put('/languages/{language}', [LanguageController::class, 'update']);
Route::match(['put', 'patch'], '/languages/{language}', [LanguageController::class, 'update']);

Route::post('/register', [AuthController::class, 'register']);
Route::post('/login', [AuthController::class, 'login']);

// Route::get('/user', function (Request $request) {
//     return $request->user();
// })->middleware('auth:sanctum');

Route::middleware('auth:sanctum')->group(function () {
    Route::get('/user', function (Request $request) {
        return $request->user();
    });

    Route::get('/logout', [AuthController::class, 'logout']);
});

// Route::middleware('auth:sanctum')->post('/logout', [AuthController::class, 'logout']);

// Route::middleware([\Laravel\Sanctum\Http\Middleware\EnsureFrontendRequestsAreStateful::class, 'auth:sanctum'])
//     ->get('/user', function (Request $request) {
//         return $request->user();
//     });

Route::get('/check-db', [DatabaseController::class, 'checkDatabaseConnection']);