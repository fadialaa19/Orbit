<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::get('/__verification-health', function (Request $request) {
    return response()->json([
        'app_url' => config('app.url'),
        'host' => $request->getHost(),
        'url' => $request->url(),
        'path' => $request->path(),
        'has_valid_signature' => $request->hasValidSignature(),
    ]);
});

