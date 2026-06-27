<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Auth\GoogleAuthController;

Route::middleware('guest')->group(function () {
    Route::get('/auth/google/redirect', [GoogleAuthController::class, 'redirectToGoogle'])
        ->name('auth.google.redirect');

    Route::get('/auth/google/callback', [GoogleAuthController::class, 'handleGoogleCallback'])
        ->name('auth.google.callback');
});

