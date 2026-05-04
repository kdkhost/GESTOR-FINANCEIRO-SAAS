<?php
use Illuminate\Support\Facades\Route;
use App\Modules\Integracoes\Controllers\GatewayPagamentoController;

Route::prefix('admin/gateways')->name('admin.gateways.')->middleware(['auth','web'])->group(function () {
    Route::get('/', [GatewayPagamentoController::class, 'index'])->name('index');
    Route::put('/{gateway}', [GatewayPagamentoController::class, 'update'])->name('update');
});
