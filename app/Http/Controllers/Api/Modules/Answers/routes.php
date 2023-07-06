<?php

use App\Http\Controllers\Api\Modules\Answers\AnswerAPIController;
use Illuminate\Support\Facades\Route;

Route::middleware('auth:sanctum')->group(function () {

Route::post('answers/create', [AnswerAPIController::class, 'store'])->name('answers.create');
Route::get('answers', [AnswerAPIController::class, 'index'])->name('answers');
Route::get('answers/{answerId}', [AnswerAPIController::class, 'show'])->name('answers.show');
Route::post('answers/{answerId}/edit', [AnswerAPIController::class, 'update'])->name('answers.update');
Route::post('answers/{answerId}/delete', [AnswerAPIController::class, 'destroy'])->name('answers.delete');
});
