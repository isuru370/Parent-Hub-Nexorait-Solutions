<?php

use App\Http\Controllers\Api\AppVersionController;
use App\Http\Controllers\Api\Auth\StudentLoginDetailsController;
use App\Http\Controllers\Api\InstituteController;
use App\Http\Controllers\Api\StudentRegistryController;
use Illuminate\Support\Facades\Route;

Route::prefix('v1')->group(function () {

    /*
    | Institute -> Hub (needs secret_key = PARENT_HUB_SECRET)
    */
    Route::middleware('hub.secret')->group(function () {
        Route::post('/register-institute', [InstituteController::class, 'register']);
        Route::post('/register-student', [StudentRegistryController::class, 'register']);
    });

    /*
    | Parent app
    */
    Route::post('/login', [StudentLoginDetailsController::class, 'studentLoginDetails'])
        ->middleware('throttle:20,1');

    Route::get('/app-version', [AppVersionController::class, 'check']);
});
