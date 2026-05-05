<?php
use Illuminate\Support\Facades\Route;
use App\Modules\Configuracoes\Controllers\ConfiguracaoController;
use App\Modules\Configuracoes\Controllers\SocialLoginController;

Route::prefix('admin/configuracoes')->name('admin.configuracoes.')->middleware(['auth','web','admin'])->group(function () {
    Route::get('/',  [ConfiguracaoController::class, 'index'])->name('index');
    Route::post('/', [ConfiguracaoController::class, 'salvar'])->name('salvar');

    // Login Social (apenas superadmin)
    Route::get('/social-login', [SocialLoginController::class, 'index'])->name('social-login');
});

// API de Login Social
Route::prefix('api/admin/configuracoes/social-login')->middleware(['auth','api'])->group(function () {
    Route::get('/', [SocialLoginController::class, 'listar']);
    Route::post('/', [SocialLoginController::class, 'salvar']);
    Route::post('/testar', [SocialLoginController::class, 'testar']);
});
