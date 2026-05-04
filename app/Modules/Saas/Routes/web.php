<?php

use App\Modules\Saas\Controllers\AssinaturaController;
use App\Modules\Saas\Controllers\EmpresaController;
use App\Modules\Saas\Controllers\FaturaController;
use App\Modules\Saas\Controllers\PlanoController;
use Illuminate\Support\Facades\Route;

Route::prefix('admin/saas')->name('admin.saas.')->middleware(['auth', 'web', 'admin'])->group(function () {
    Route::get('/empresas', [EmpresaController::class, 'index'])->name('empresas.index');
    Route::get('/empresas/listar', [EmpresaController::class, 'listar'])->name('empresas.listar');
    Route::post('/empresas', [EmpresaController::class, 'store'])->name('empresas.store');
    Route::get('/empresas/{id}', [EmpresaController::class, 'show'])->name('empresas.show');
    Route::put('/empresas/{id}', [EmpresaController::class, 'update'])->name('empresas.update');
    Route::delete('/empresas/{id}', [EmpresaController::class, 'destroy'])->name('empresas.destroy');

    Route::get('/planos', [PlanoController::class, 'index'])->name('planos.index');
    Route::get('/planos/listar', [PlanoController::class, 'listar'])->name('planos.listar');
    Route::post('/planos', [PlanoController::class, 'store'])->name('planos.store');
    Route::get('/planos/{id}', [PlanoController::class, 'show'])->name('planos.show');
    Route::put('/planos/{id}', [PlanoController::class, 'update'])->name('planos.update');
    Route::delete('/planos/{id}', [PlanoController::class, 'destroy'])->name('planos.destroy');

    Route::get('/assinaturas', [AssinaturaController::class, 'index'])->name('assinaturas.index');
    Route::get('/assinaturas/listar', [AssinaturaController::class, 'listar'])->name('assinaturas.listar');
    Route::post('/assinaturas', [AssinaturaController::class, 'store'])->name('assinaturas.store');
    Route::get('/assinaturas/{id}', [AssinaturaController::class, 'show'])->name('assinaturas.show');
    Route::put('/assinaturas/{id}', [AssinaturaController::class, 'update'])->name('assinaturas.update');
    Route::delete('/assinaturas/{id}', [AssinaturaController::class, 'destroy'])->name('assinaturas.destroy');

    Route::get('/faturas', [FaturaController::class, 'index'])->name('faturas.index');
    Route::get('/faturas/listar', [FaturaController::class, 'listar'])->name('faturas.listar');
    Route::post('/faturas', [FaturaController::class, 'store'])->name('faturas.store');
    Route::post('/faturas/{id}/mercadopago', [FaturaController::class, 'gerarMercadoPago'])->name('faturas.mercadopago');
    Route::get('/faturas/{id}', [FaturaController::class, 'show'])->name('faturas.show');
    Route::put('/faturas/{id}', [FaturaController::class, 'update'])->name('faturas.update');
    Route::delete('/faturas/{id}', [FaturaController::class, 'destroy'])->name('faturas.destroy');
});
