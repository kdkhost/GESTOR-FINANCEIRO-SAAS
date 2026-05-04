<?php

use Illuminate\Support\Facades\Route;
use App\Modules\Instalador\Controllers\InstaladorController;

// Rota principal do instalador (sempre acessivel)
Route::get('/instalar', [InstaladorController::class, 'index'])->name('instalador.index');

// Rotas de acao - bloqueadas se sistema ja instalado
Route::prefix('instalar')->name('instalador.')->middleware('instalacao')->group(function () {
    Route::get('/requisitos',          [InstaladorController::class, 'verificarRequisitos'])->name('requisitos');
    Route::get('/permissoes',          [InstaladorController::class, 'verificarPermissoes'])->name('permissoes');
    Route::post('/testar-banco',       [InstaladorController::class, 'testarBanco'])->name('testar-banco');
    Route::post('/salvar-banco',       [InstaladorController::class, 'salvarConfiguracaoBanco'])->name('salvar-banco');
    Route::post('/migrations',         [InstaladorController::class, 'executarMigrations'])->name('migrations');
    Route::post('/seeders',            [InstaladorController::class, 'executarSeeders'])->name('seeders');
    Route::post('/permissoes-sistema', [InstaladorController::class, 'publicarPermissoes'])->name('permissoes-sistema');
    Route::post('/storage-link',       [InstaladorController::class, 'criarStorageLink'])->name('storage-link');
    Route::post('/superadmin',         [InstaladorController::class, 'criarSuperadmin'])->name('superadmin');
    Route::post('/configuracao',       [InstaladorController::class, 'salvarConfiguracaoInicial'])->name('configuracao');
    Route::post('/finalizar',          [InstaladorController::class, 'finalizar'])->name('finalizar');
});
