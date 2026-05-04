<?php
use Illuminate\Support\Facades\Route;
use App\Modules\Relatorios\Controllers\RelatorioController;
Route::prefix('admin/relatorios')->name('admin.relatorios.')->middleware(['auth','web','admin'])->group(function () {
    Route::get('/fluxo-caixa',     [RelatorioController::class, 'fluxoCaixa'])->name('fluxo-caixa');
    Route::get('/dre',             [RelatorioController::class, 'dre'])->name('dre');
    Route::get('/evolucao',        [RelatorioController::class, 'evolucao'])->name('evolucao');
    Route::get('/inadimplencia',   [RelatorioController::class, 'inadimplencia'])->name('inadimplencia');
    Route::get('/saude-financeira',[RelatorioController::class, 'saudeFinanceira'])->name('saude-financeira');
});
