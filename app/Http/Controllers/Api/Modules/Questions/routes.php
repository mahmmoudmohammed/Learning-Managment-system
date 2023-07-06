<?php

use App\Http\Controllers\Api\Modules\Questions\QuestionAPIController;
use Illuminate\Support\Facades\Route;

Route::middleware('auth:sanctum')->group(function () {

    Route::post('questions/create', [QuestionAPIController::class, 'store'])->name('questions.create');
    Route::get('questions', [QuestionAPIController::class, 'index'])->name('questions');
    Route::get('questions/{questionId}', [QuestionAPIController::class, 'show'])->name('questions.show');
    Route::post('questions/{questionId}/edit', [QuestionAPIController::class, 'update'])->name('questions.update');
    Route::post('questions/{questionId}/delete', [QuestionAPIController::class, 'destroy'])->name('questions.delete');
});
