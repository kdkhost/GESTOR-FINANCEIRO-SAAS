<?php
use Illuminate\Support\Facades\Route;
use App\Modules\Configuracoes\Controllers\ConfiguracaoController;
Route::prefix('admin/configuracoes')->name('admin.configuracoes.')->middleware(['auth','web'])->group(function () {
    Route::get('/', [ConfiguracaoController::class, 'index'])->name('index');
});