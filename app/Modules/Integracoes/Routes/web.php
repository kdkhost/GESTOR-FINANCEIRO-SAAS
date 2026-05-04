<?php

use App\Modules\Integracoes\Controllers\GatewayPagamentoController;
use Illuminate\Support\Facades\Route;

Route::prefix('admin/gateways')->name('admin.gateways.')->middleware(['auth', 'web', 'admin'])->group(function () {
    Route::get('/', [GatewayPagamentoController::class, 'index'])->name('index');
    Route::put('/{gateway}', [GatewayPagamentoController::class, 'update'])->name('update');
    Route::post('/{gateway}/testar', [GatewayPagamentoController::class, 'testar'])->name('testar');
});
