<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;
use Illuminate\Database\Eloquent\Model;

use App\Http\Controllers\Api\SiteController;
use App\Http\Controllers\Api\LanguageController;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\TestController;
use App\Http\Controllers\Api\PetTypeController;
use App\Http\Controllers\Api\PetBreedController;
use App\Http\Controllers\Api\PetOwnerController;

Route::get('/', [SiteController::class, 'index']);
Route::get('/languages', [LanguageController::class, 'index']);
Route::get('/languages/{language}', [LanguageController::class, 'show']);
Route::post('/languages', [LanguageController::class, 'store']);
Route::match(['put', 'patch'], '/languages/{language}', [LanguageController::class, 'update']);
Route::get('/current-language', [LanguageController::class, 'getCurrentLanguage']);

Route::post('/register', [AuthController::class, 'register']);
Route::post('/login', [AuthController::class, 'login']);
Route::get('/logout', [AuthController::class, 'logout']);

// Route::get('/user', function (Request $request) {
//     return $request->user();
// })->middleware('auth:sanctum');

Route::middleware('auth:sanctum')->group(function () {
    // Route for resending the verification email
    Route::post('/email/verification-notification', function (Request $request) {
        $request->user()->sendEmailVerificationNotification();
        return response()->json(['message' => 'Verification link sent!']);
    })->middleware(['throttle:6,1'])->name('verification.send');

    // Route for getting user information
    Route::get('/user', function (Request $request) {
        return $request->user();
    })->middleware('verified');

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

Route::apiResource('/pet-types', PetTypeController::class);
Route::apiResource('/pet-breeds', PetBreedController::class);
Route::apiResource('/pet-owners', PetOwnerController::class);

Route::get('/check-db', [TestController::class, 'checkDatabaseConnection']);
Route::get('/server-clock', [TestController::class, 'checkServerClock']);
Route::get('/check-mail', [TestController::class, 'checkEmailSend']);
