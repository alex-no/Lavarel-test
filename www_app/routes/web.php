<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Auth\VerificationController;
use App\Http\Controllers\HomeController;

Route::get('/', [HomeController::class, 'index']);

// Route for email verification
Route::get('/verify-email', [VerificationController::class, 'verify'])->name('email.verify');
// Route::get('/email/verify/{id}/{hash}', [VerificationController::class, 'verify'])
//     ->middleware(['signed', 'throttle:6,1'])
//     ->name('verification.verify');

// Route::get('/api/documentation', [SwaggerController::class, 'api']);
