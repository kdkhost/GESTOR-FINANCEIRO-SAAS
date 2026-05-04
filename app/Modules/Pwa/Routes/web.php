<?php

use Illuminate\Support\Facades\Route;
use App\Modules\Pwa\Controllers\PwaController;

Route::get('/manifest.json', [PwaController::class, 'manifest'])->name('pwa.manifest');
Route::get('/sw.js',         [PwaController::class, 'serviceWorker'])->name('pwa.sw');