<?php

use App\Http\Controllers\Api\AdminScholarshipIngestController;
use Illuminate\Support\Facades\Route;

Route::post('/admin/scholarships', [AdminScholarshipIngestController::class, 'store'])
    ->middleware(['scholarship.admin.api', 'throttle:20,1']);
