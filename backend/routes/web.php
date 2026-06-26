<?php

use App\Features\Certificate\Http\Controllers\PublicCertificateController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

Route::get('/verify/{certificate_number}', [PublicCertificateController::class, 'verify'])
    ->name('certificates.verify');
