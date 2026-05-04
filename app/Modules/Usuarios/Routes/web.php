<?php
use Illuminate\Support\Facades\Route;
use App\Modules\Usuarios\Controllers\AuthController;
use App\Modules\Usuarios\Controllers\PerfilController;
use App\Modules\Usuarios\Controllers\UsuarioController;

Route::middleware('guest')->group(function () {
    Route::get('/login',  [AuthController::class, 'showLogin'])->name('login');
    Route::post('/login', [AuthController::class, 'login'])->name('auth.login.post');
    Route::get('/esqueci-senha',  [AuthController::class, 'showEsqueciSenha'])->name('auth.esqueci-senha');
    Route::post('/esqueci-senha', [AuthController::class, 'enviarLinkRedefinicao'])->name('auth.enviar-link');
    Route::get('/redefinir-senha/{token}', [AuthController::class, 'showRedefinirSenha'])->name('auth.redefinir-senha');
    Route::post('/redefinir-senha', [AuthController::class, 'redefinirSenha'])->name('auth.salvar-senha');
});

Route::middleware('auth')->group(function () {
    Route::post('/logout', [AuthController::class, 'logout'])->name('auth.logout');
    Route::get('/admin/perfil',         [PerfilController::class, 'index'])->name('admin.perfil');
    Route::put('/admin/perfil',         [PerfilController::class, 'update'])->name('admin.perfil.update');
    Route::post('/admin/perfil/avatar', [PerfilController::class, 'uploadAvatar'])->name('admin.perfil.avatar');
    Route::get('/admin/notificacoes/nao-lidas', [\App\Modules\Auditoria\Controllers\NotificacaoController::class, 'naoLidas'])->name('admin.notificacoes.nao-lidas');
    Route::post('/admin/notificacoes/{id}/marcar-lida', [\App\Modules\Auditoria\Controllers\NotificacaoController::class, 'marcarLida'])->name('admin.notificacoes.marcar-lida');
});

Route::prefix('admin/usuarios')->name('admin.usuarios.')->middleware(['auth','web'])->group(function () {
    Route::get('/', [UsuarioController::class, 'index'])->name('index');
});