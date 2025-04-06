<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\AuthController;
// use L5Swagger\Http\Controllers\SwaggerController;

Route::get('/', function () {
    return view('welcome');
});
Route::get('/verify-email', [AuthController::class, 'verify'])->name('email.verify');

// Route::get('/api/documentation', [SwaggerController::class, 'api']);