<?php
use Illuminate\Support\Facades\Route;
use App\Modules\Cron\Controllers\CronController;
Route::prefix('admin/cron')->name('admin.cron.')->middleware(['auth','web','admin'])->group(function () {
    Route::get('/', [CronController::class, 'index'])->name('index');
});
