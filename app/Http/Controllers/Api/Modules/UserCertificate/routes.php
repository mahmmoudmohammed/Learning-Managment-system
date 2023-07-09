<?php

use App\Http\Controllers\Api\Modules\UserCertificate\CertificateAPIController;
use App\Http\Controllers\Api\Modules\UserCertificate\UserCertificatesAPIController;
use Illuminate\Support\Facades\Route;



// Certificates route
Route::prefix('Certificate')->middleware(auth('sanctum'))->group(function () {
    Route::post('create', [CertificateAPIController::class, 'create'])->name('certificateCreate');
    Route::post('Edit',  [CertificateAPIController::class, 'edit'])->name('certificateEdit');
    Route::post('Show',    [CertificateAPIController::class, 'show'])->name('certificateShow');
    Route::post('Delete',  [CertificateAPIController::class, 'delete'])->name('certificateDelete');
    Route::post('Index',   [CertificateAPIController::class, 'index'])->name('certificateIndex');
});

Route::prefix('UserCertificate')->middleware(auth('sanctum'))->group(function () {
    Route::post('start', [UserCertificatesAPIController::class, 'startCertificate'])->name('start');
    Route::post('navigate', [UserCertificatesAPIController::class, 'questionNevigator'])->name('navigate');
    Route::post('finish', [UserCertificatesAPIController::class, 'finishCertificate'])->name('finish');
});
