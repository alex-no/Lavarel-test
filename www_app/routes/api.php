<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\Api\LanguageController;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\DatabaseController;
use App\Http\Controllers\Api\PetTypeController;
use App\Http\Controllers\Api\PetBreedController;
use App\Http\Controllers\Api\PetOwnerController;
use Illuminate\Database\Eloquent\Model;

//Route::apiResource('languages', LanguageController::class);
Route::get('/languages', [LanguageController::class, 'index']);
Route::get('/languages/{language}', [LanguageController::class, 'show']);
Route::post('/languages', [LanguageController::class, 'store']);
// Route::put('/languages/{language}', [LanguageController::class, 'update']);
Route::match(['put', 'patch'], '/languages/{language}', [LanguageController::class, 'update']);

Route::get('/current-language', function (Request $request) {
    $language = App::getLocale();
    return response()->json(['language' =>$language]);
});

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

    Route::patch('/users', function (Request $request) {
        $request->validate(['lang' => 'required|string|exists:language,code']);

        $user = Auth::user();
        $user->language_code = $request->lang;
        if ($user instanceof Model) {
            $user->save();
        } else {
            return response()->json(['error' => 'User model not found'], 500);
        }

        return response()->json(['message' => 'Language updated', 'language' => $user->language_code]);
    });
});

// Route::middleware('auth:sanctum')->post('/logout', [AuthController::class, 'logout']);

// Route::middleware([\Laravel\Sanctum\Http\Middleware\EnsureFrontendRequestsAreStateful::class, 'auth:sanctum'])
//     ->get('/user', function (Request $request) {
//         return $request->user();
//     });

Route::apiResource('/pet-type', PetTypeController::class);
Route::apiResource('/pet-breed', PetBreedController::class);
Route::apiResource('/pet-owner', PetOwnerController::class);

Route::get('/check-db', [DatabaseController::class, 'checkDatabaseConnection']);