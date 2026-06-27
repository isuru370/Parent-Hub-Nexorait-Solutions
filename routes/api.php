<?php

use App\Http\Controllers\Api\Auth\StudentLoginDetailsController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\InstituteController;
use App\Http\Controllers\Api\StudentRegistryController;

Route::prefix('v1')->group(function () {

    Route::post(
        '/register-institute',
        [InstituteController::class, 'register']
    );

    Route::post(
        '/register-student',
        [StudentRegistryController::class, 'register']
    );

    Route::post(
        '/login',
        [StudentLoginDetailsController::class, 'studentLoginDetails']
    );
});
