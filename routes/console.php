<?php

use App\Console\Commands\ExecutarCronJobs;
use App\Modules\Cron\Models\CronJob;
use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;

// Comando para exibir citação inspiradora
Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote')->hourly();

// Agenda execução automática de tarefas cron a cada minuto
Schedule::call(function () {
    $jobs = CronJob::where('ativo', true)->get();
    foreach ($jobs as $job) {
        if ($job->deveExecutar()) {
            $job->executar();
        }
    }
})->everyMinute()->name('auto-cron-jobs');
