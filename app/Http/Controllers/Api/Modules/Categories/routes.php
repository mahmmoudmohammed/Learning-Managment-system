<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\Modules\Categories\CategoryAPIController;
Route::middleware('auth:sanctum')->group(function () {
    Route::post('categories', [CategoryAPIController::class, 'store']);
    Route::get('categories', [CategoryAPIController::class, 'index']);
    Route::get('categories/{id}', [CategoryAPIController::class, 'show']);
    Route::put('categories/{id}', [CategoryAPIController::class, 'update']);
    Route::delete('categories/{id}', [CategoryAPIController::class, 'destroy']);
});
