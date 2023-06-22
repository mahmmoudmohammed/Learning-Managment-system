<?php

use App\Http\Controllers\Api\Modules\Users\UserAPIController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\Modules\Users\AdminController;

Route::post('login',  [UserAPIController::class, 'login'])->name('login');
Route::post('register',  [UserAPIController::class, 'register'])->name('register');
Route::post('logout',  [UserAPIController::class, 'logout'])->name('logout');
Route::post('profile',  [UserAPIController::class, 'profile'])->name('profile');
Route::post('profileEdit',  [UserAPIController::class, 'profileEdit'])->name('profileEdit');


Route::post('admins', [AdminController::class, 'store']);
Route::get('admins', [AdminController::class, 'index']);
Route::get('admins/{id}', [AdminController::class, 'show']);
Route::put('admins/{id}', [AdminController::class, 'update']);
Route::delete('admins/{id}', [AdminController::class, 'destroy'])->name('delete');
