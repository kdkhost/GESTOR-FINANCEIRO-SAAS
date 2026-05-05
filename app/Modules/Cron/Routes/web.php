<?php
use Illuminate\Support\Facades\Route;
use App\Modules\Cron\Controllers\CronController;
use App\Modules\Cron\Controllers\CronApiController;

Route::prefix('admin/cron')->name('admin.cron.')->middleware(['auth','web','admin'])->group(function () {
    Route::get('/', [CronController::class, 'index'])->name('index');
});

// API de Cron
Route::prefix('api/admin/cron')->middleware(['auth','api'])->group(function () {
    Route::get('/', [CronApiController::class, 'index']);
    Route::post('/', [CronApiController::class, 'store']);
    Route::put('/{id}', [CronApiController::class, 'update']);
    Route::delete('/{id}', [CronApiController::class, 'destroy']);
    Route::post('/{id}/executar', [CronApiController::class, 'executar']);
    Route::get('/{id}/logs', [CronApiController::class, 'logs']);
    Route::get('/estatisticas', [CronApiController::class, 'estatisticas']);
});
