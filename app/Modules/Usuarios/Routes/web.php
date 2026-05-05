<?php
use Illuminate\Support\Facades\Route;
use App\Modules\Usuarios\Controllers\AuthController;
use App\Modules\Usuarios\Controllers\PerfilController;
use App\Modules\Usuarios\Controllers\UsuarioController;
use App\Modules\Usuarios\Controllers\SocialAuthController;

Route::middleware('guest')->group(function () {
    Route::get('/login',  [AuthController::class, 'showLogin'])->name('login');
    Route::post('/login', [AuthController::class, 'login'])->name('auth.login.post');
    Route::get('/register', [AuthController::class, 'showRegister'])->name('register');
    Route::post('/register', [AuthController::class, 'register'])->name('auth.register.post');
    Route::get('/esqueci-senha',  [AuthController::class, 'showEsqueciSenha'])->name('auth.esqueci-senha');
    Route::post('/esqueci-senha', [AuthController::class, 'enviarLinkRedefinicao'])->name('auth.enviar-link');
    Route::get('/redefinir-senha/{token}', [AuthController::class, 'showRedefinirSenha'])->name('auth.redefinir-senha');
    Route::post('/redefinir-senha', [AuthController::class, 'redefinirSenha'])->name('auth.salvar-senha');

    // Login Social
    Route::get('/auth/{provider}', [SocialAuthController::class, 'redirect'])->name('auth.social.redirect');
    Route::get('/auth/{provider}/callback', [SocialAuthController::class, 'callback'])->name('auth.social.callback');
});

Route::middleware('auth')->group(function () {
    Route::post('/logout', [AuthController::class, 'logout'])->name('auth.logout');
    Route::get('/admin/perfil',         [PerfilController::class, 'index'])->name('admin.perfil');
    Route::put('/admin/perfil',         [PerfilController::class, 'update'])->name('admin.perfil.update');
    Route::post('/admin/perfil/avatar', [PerfilController::class, 'uploadAvatar'])->name('admin.perfil.avatar');
    Route::get('/admin/notificacoes/nao-lidas', [\App\Modules\Auditoria\Controllers\NotificacaoController::class, 'naoLidas'])->name('admin.notificacoes.nao-lidas');
    Route::post('/admin/notificacoes/{id}/marcar-lida', [\App\Modules\Auditoria\Controllers\NotificacaoController::class, 'marcarLida'])->name('admin.notificacoes.marcar-lida');
});

Route::prefix('admin/usuarios')->name('admin.usuarios.')->middleware(['auth','web','admin'])->group(function () {
    Route::get('/',          [UsuarioController::class, 'index'])->name('index');
    Route::get('/listar',    [UsuarioController::class, 'listar'])->name('listar');
    Route::post('/',         [UsuarioController::class, 'store'])->name('store');
    Route::get('/{id}',      [UsuarioController::class, 'show'])->name('show');
    Route::put('/{id}',      [UsuarioController::class, 'update'])->name('update');
    Route::delete('/{id}',   [UsuarioController::class, 'destroy'])->name('destroy');

    // Acesso supervisionado (impersonate)
    Route::post('/{id}/impersonate', [UsuarioController::class, 'impersonate'])->name('impersonate');
});

// Sair do modo supervisionado (acessível a qualquer usuário logado que esteja em modo supervisionado)
Route::post('/admin/stop-impersonating', [UsuarioController::class, 'stopImpersonating'])
    ->middleware(['auth'])
    ->name('admin.stop-impersonating');

// Status de impersonate (para verificar no frontend)
Route::get('/admin/impersonate-status', [UsuarioController::class, 'impersonateStatus'])
    ->middleware(['auth'])
    ->name('admin.impersonate-status');
