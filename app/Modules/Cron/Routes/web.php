<?php
use Illuminate\Support\Facades\Route;
use App\Modules\Cron\Controllers\CronController;
Route::prefix('admin/cron')->name('admin.cron.')->middleware(['auth','web'])->group(function () {
    Route::get('/', [CronController::class, 'index'])->name('index');
});