<?php

use App\Http\Controllers\Api\Modules\Roles\RoleAPIController;
use Illuminate\Support\Facades\Route;

Route::middleware('auth:sanctum')->group(function () {

    Route::post('roles/create', [RoleAPIController::class, 'store'])->name('roles.create');
    Route::get('roles', [RoleAPIController::class, 'index'])->name('roles');
    Route::get('roles/{role}', [RoleAPIController::class, 'show'])->name('roles.show');
    Route::post('roles/{role}/edit', [RoleAPIController::class, 'update'])->name('roles.update');
    Route::post('roles/{role}/delete', [RoleAPIController::class, 'destroy'])->name('roles.delete');
});
