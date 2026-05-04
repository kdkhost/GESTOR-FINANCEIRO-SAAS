<?php

use Illuminate\Support\Facades\Route;
use App\Modules\Saas\Controllers\PlanoController;

Route::prefix('admin/saas')->name('admin.saas.')->middleware(['auth', 'web', 'admin'])->group(function () {
    Route::get('/planos',         [PlanoController::class, 'index'])->name('planos.index');
    Route::get('/planos/listar',  [PlanoController::class, 'listar'])->name('planos.listar');
    Route::post('/planos',        [PlanoController::class, 'store'])->name('planos.store');
    Route::get('/planos/{id}',    [PlanoController::class, 'show'])->name('planos.show');
    Route::put('/planos/{id}',    [PlanoController::class, 'update'])->name('planos.update');
    Route::delete('/planos/{id}', [PlanoController::class, 'destroy'])->name('planos.destroy');
});

