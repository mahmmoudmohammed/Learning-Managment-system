<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\Modules\Topics\TopicAPIController;

Route::post('topics',  [TopicAPIController::class, 'store'])->name('topic.create');
Route::get('topics',   [TopicAPIController::class, 'index'])->name('topics');
Route::get('topics/{id}',    [TopicAPIController::class, 'show'])->name('topic.show');
Route::put('topics/{id}',  [TopicAPIController::class, 'update'])->name('topic.update');
Route::delete('topics/{id}',  [TopicAPIController::class, 'destroy'])->name('topic.delete');
