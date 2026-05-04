<?php
use Illuminate\Support\Facades\Route;
use App\Modules\Auditoria\Controllers\AuditoriaController;
Route::prefix('admin/auditoria')->name('admin.auditoria.')->middleware(['auth','web','admin'])->group(function () {
    Route::get('/', [AuditoriaController::class, 'index'])->name('index');
});
