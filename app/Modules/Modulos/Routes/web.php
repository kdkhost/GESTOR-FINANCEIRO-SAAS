<?php

use App\Modules\Modulos\Controllers\ModuloController;
use Illuminate\Support\Facades\Route;

Route::prefix('admin/modulos')->name('admin.modulos.')->middleware(['auth', 'web'])->group(function () {
    Route::get('/', [ModuloController::class, 'index'])->name('index');
    Route::get('/listar', [ModuloController::class, 'listar'])->name('listar');
    Route::post('/', [ModuloController::class, 'store'])->name('store');
    Route::get('/{id}', [ModuloController::class, 'show'])->name('show');
    Route::put('/{id}', [ModuloController::class, 'update'])->name('update');
    Route::delete('/{id}', [ModuloController::class, 'destroy'])->name('destroy');
    Route::post('/{id}/status', [ModuloController::class, 'alternarStatus'])->name('status');
});

