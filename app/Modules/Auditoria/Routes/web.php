<?php

use Illuminate\Support\Facades\Route;
use App\Modules\Auditoria\Controllers\AuditoriaController;

Route::prefix('admin/auditoria')->name('admin.auditoria.')->middleware(['auth','web','admin'])->group(function () {
    Route::get('/', [AuditoriaController::class, 'index'])->name('index');
    Route::get('/listar', [AuditoriaController::class, 'listar'])->name('listar');
    Route::get('/detalhes/{id}', [AuditoriaController::class, 'detalhes'])->name('detalhes');
    Route::get('/estatisticas', [AuditoriaController::class, 'estatisticas'])->name('estatisticas');
    Route::get('/entidades', [AuditoriaController::class, 'entidades'])->name('entidades');
    Route::post('/limpar', [AuditoriaController::class, 'limpar'])->name('limpar');
});
