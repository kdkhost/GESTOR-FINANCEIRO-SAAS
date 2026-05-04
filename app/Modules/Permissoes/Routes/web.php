<?php
use Illuminate\Support\Facades\Route;
use App\Modules\Permissoes\Controllers\PermissaoController;
Route::prefix('admin/permissoes')->name('admin.permissoes.')->middleware(['auth','web'])->group(function () {
    Route::get('/', [PermissaoController::class, 'index'])->name('index');
});