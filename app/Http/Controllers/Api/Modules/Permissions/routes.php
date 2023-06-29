<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\Modules\Permissions\PermissionAPIController;


// // permissions route
Route::prefix('permission')->middleware(auth('sanctum'))->group(function () {
    Route::post('create',  [PermissionAPIController::class, 'create'])->name('permission.create');
    Route::post('update',  [PermissionAPIController::class, 'update'])->name('permission.update');
    Route::post('view',    [PermissionAPIController::class, 'view'])->name('permission.view');
    Route::post('delete',  [PermissionAPIController::class, 'delete'])->name('permission.delete');
    Route::post('index',   [PermissionAPIController::class, 'index'])->name('permission.index');
});
