<?php

use Illuminate\Support\Facades\Route;
use App\Modules\Financeiro\Controllers\DashboardController;
use App\Modules\Financeiro\Controllers\ContaPagarController;
use App\Modules\Financeiro\Controllers\ContaReceberController;
use App\Modules\Financeiro\Controllers\ReceitaController;
use App\Modules\Financeiro\Controllers\DespesaController;
use App\Modules\Financeiro\Controllers\TransferenciaController;
use App\Modules\Financeiro\Controllers\CategoriaController;
use App\Modules\Financeiro\Controllers\SubcategoriaController;
use App\Modules\Financeiro\Controllers\ContaBancariaController;
use App\Modules\Financeiro\Controllers\ClienteController;
use App\Modules\Financeiro\Controllers\FornecedorController;
use App\Modules\Financeiro\Controllers\MetaController;
use App\Modules\Financeiro\Controllers\OrcamentoController;
use App\Modules\Financeiro\Controllers\RecorrenciaController;
use App\Modules\Financeiro\Controllers\AnexoController;

Route::prefix('admin')->name('admin.')->middleware(['auth', 'web', 'admin'])->group(function () {

    // Dashboard
    Route::prefix('dashboard')->name('dashboard.')->group(function () {
        Route::get('/', [DashboardController::class, 'index'])->name('index');
        Route::get('/kpis', [DashboardController::class, 'kpis'])->name('kpis');
        Route::get('/saude', [DashboardController::class, 'saude'])->name('saude');
    });

    // Contas a Pagar
    Route::prefix('contas-pagar')->name('contas-pagar.')->group(function () {
        Route::get('/', [ContaPagarController::class, 'index'])->name('index');
        Route::get('/listar', [ContaPagarController::class, 'listar'])->name('listar');
        Route::post('/', [ContaPagarController::class, 'store'])->name('store');
        Route::get('/{id}', [ContaPagarController::class, 'show'])->name('show');
        Route::put('/{id}', [ContaPagarController::class, 'update'])->name('update');
        Route::delete('/{id}', [ContaPagarController::class, 'destroy'])->name('destroy');
        Route::post('/{id}/pagar', [ContaPagarController::class, 'pagar'])->name('pagar');
        Route::post('/{id}/cancelar', [ContaPagarController::class, 'cancelar'])->name('cancelar');
    });

    // Contas a Receber
    Route::prefix('contas-receber')->name('contas-receber.')->group(function () {
        Route::get('/', [ContaReceberController::class, 'index'])->name('index');
        Route::get('/listar', [ContaReceberController::class, 'listar'])->name('listar');
        Route::post('/', [ContaReceberController::class, 'store'])->name('store');
        Route::get('/{id}', [ContaReceberController::class, 'show'])->name('show');
        Route::put('/{id}', [ContaReceberController::class, 'update'])->name('update');
        Route::delete('/{id}', [ContaReceberController::class, 'destroy'])->name('destroy');
        Route::post('/{id}/receber', [ContaReceberController::class, 'receber'])->name('receber');
    });

    // Receitas e Despesas
    Route::get('receitas/listar', [ReceitaController::class, 'listar'])->name('receitas.listar');
    Route::get('despesas/listar', [DespesaController::class, 'listar'])->name('despesas.listar');
    Route::apiResource('receitas', ReceitaController::class)->except(['create', 'edit']);
    Route::apiResource('despesas', DespesaController::class)->except(['create', 'edit']);

    // Transferencias
    Route::prefix('transferencias')->name('transferencias.')->group(function () {
        Route::get('/', [TransferenciaController::class, 'index'])->name('index');
        Route::get('/listar', [TransferenciaController::class, 'listar'])->name('listar');
        Route::post('/', [TransferenciaController::class, 'store'])->name('store');
        Route::delete('/{id}', [TransferenciaController::class, 'destroy'])->name('destroy');
    });

    // Categorias e Subcategorias
    Route::apiResource('categorias', CategoriaController::class)->except(['create', 'edit']);
    Route::apiResource('subcategorias', SubcategoriaController::class)->except(['create', 'edit']);

    // Contas Bancarias
    Route::prefix('contas-bancarias')->name('contas-bancarias.')->group(function () {
        Route::get('/', [ContaBancariaController::class, 'index'])->name('index');
        Route::get('/listar', [ContaBancariaController::class, 'listar'])->name('listar');
        Route::post('/', [ContaBancariaController::class, 'store'])->name('store');
        Route::get('/{id}', [ContaBancariaController::class, 'show'])->name('show');
        Route::put('/{id}', [ContaBancariaController::class, 'update'])->name('update');
        Route::delete('/{id}', [ContaBancariaController::class, 'destroy'])->name('destroy');
        Route::post('/{id}/ajustar-saldo', [ContaBancariaController::class, 'ajustarSaldo'])->name('ajustar-saldo');
    });

    // Clientes e Fornecedores
    Route::get('clientes/buscar', [ClienteController::class, 'buscar'])->name('clientes.buscar');
    Route::get('clientes/listar', [ClienteController::class, 'listar'])->name('clientes.listar');
    Route::get('fornecedores/buscar', [FornecedorController::class, 'buscar'])->name('fornecedores.buscar');
    Route::get('fornecedores/listar', [FornecedorController::class, 'listar'])->name('fornecedores.listar');
    Route::apiResource('clientes', ClienteController::class)->except(['create', 'edit']);
    Route::apiResource('fornecedores', FornecedorController::class)->except(['create', 'edit']);

    // Metas e Orcamentos
    Route::apiResource('metas', MetaController::class)->except(['create', 'edit']);
    Route::apiResource('orcamentos', OrcamentoController::class)->except(['create', 'edit']);

    // Recorrencias
    Route::apiResource('recorrencias', RecorrenciaController::class)->except(['create', 'edit']);

    // Anexos
    Route::post('/anexos', [AnexoController::class, 'store'])->name('anexos.store');
    Route::delete('/anexos/{id}', [AnexoController::class, 'destroy'])->name('anexos.destroy');
});
