<?php

use Illuminate\Support\Facades\Route;
use App\Modules\Manutencao\Controllers\ManutencaoController;

Route::prefix('admin/manutencao')->name('admin.manutencao.')->middleware(['auth', 'web', 'admin'])->group(function () {
    Route::get('/',  [ManutencaoController::class, 'index'])->name('index');
    Route::post('/', [ManutencaoController::class, 'salvar'])->name('salvar');
});

