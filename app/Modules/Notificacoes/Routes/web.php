<?php

use Illuminate\Support\Facades\Route;
use App\Modules\Notificacoes\Controllers\TemplateNotificacaoController;

Route::prefix('admin/notificacoes')->name('admin.notificacoes.')->middleware(['auth', 'web', 'admin'])->group(function () {
    Route::get('/templates',          [TemplateNotificacaoController::class, 'index'])->name('templates.index');
    Route::get('/templates/listar',   [TemplateNotificacaoController::class, 'listar'])->name('templates.listar');
    Route::post('/templates',         [TemplateNotificacaoController::class, 'store'])->name('templates.store');
    Route::get('/templates/{id}',     [TemplateNotificacaoController::class, 'show'])->name('templates.show');
    Route::put('/templates/{id}',     [TemplateNotificacaoController::class, 'update'])->name('templates.update');
    Route::delete('/templates/{id}',  [TemplateNotificacaoController::class, 'destroy'])->name('templates.destroy');
    Route::post('/templates/preview', [TemplateNotificacaoController::class, 'preview'])->name('templates.preview');
});

