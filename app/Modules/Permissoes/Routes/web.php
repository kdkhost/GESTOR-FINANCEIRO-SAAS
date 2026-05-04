<?php
use Illuminate\Support\Facades\Route;
use App\Modules\Permissoes\Controllers\PermissaoController;
Route::prefix('admin/permissoes')->name('admin.permissoes.')->middleware(['auth','web','admin'])->group(function () {
    Route::get('/', [PermissaoController::class, 'index'])->name('index');
    Route::get('/listar', [PermissaoController::class, 'listar'])->name('listar');
    Route::post('/roles', [PermissaoController::class, 'store'])->name('roles.store');
    Route::get('/roles/{role}', [PermissaoController::class, 'show'])->name('roles.show');
    Route::put('/roles/{role}', [PermissaoController::class, 'update'])->name('roles.update');
    Route::delete('/roles/{role}', [PermissaoController::class, 'destroy'])->name('roles.destroy');
    Route::get('/usuarios', [PermissaoController::class, 'usuarios'])->name('usuarios');
    Route::post('/usuarios/{user}/roles', [PermissaoController::class, 'sincronizarUsuario'])->name('usuarios.roles');
});
