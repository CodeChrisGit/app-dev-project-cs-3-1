<?php

use App\Http\Controllers\Admin\PropertyController as AdminPropertyController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\EstimateController;
use App\Http\Controllers\LookupController;
use App\Http\Controllers\PropertyController;
use Illuminate\Support\Facades\Route;

// Public
Route::get('/locations', [LookupController::class, 'locations']);
Route::get('/property-types', [LookupController::class, 'types']);

Route::get('/properties', [PropertyController::class, 'index']);
Route::get('/properties/compare', [PropertyController::class, 'compare']);
Route::get('/properties/{property}', [PropertyController::class, 'show']);

Route::post('/estimate', EstimateController::class);

// Admin
Route::post('/admin/login', [AuthController::class, 'login']);

Route::middleware(['auth:sanctum', 'can:admin'])->prefix('admin')->group(function () {
    Route::post('/logout', [AuthController::class, 'logout']);
    Route::get('/me', [AuthController::class, 'me']);
    Route::apiResource('properties', AdminPropertyController::class)->names('admin.properties');
});
