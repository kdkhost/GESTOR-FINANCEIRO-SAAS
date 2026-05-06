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
    // Log de execução do scheduler
    \Log::info('Cron scheduler executado em: ' . now()->format('d/m/Y H:i:s'));
    
    $jobs = CronJob::where('ativo', true)->get();
    $executados = 0;
    
    foreach ($jobs as $job) {
        if ($job->deveExecutar()) {
            \Log::info("Executando tarefa: {$job->nome} (ID: {$job->id})");
            $resultado = $job->executar();
            $executados++;
            
            if ($resultado['sucesso']) {
                \Log::info("Tarefa {$job->nome} executada com sucesso");
            } else {
                \Log::error("Tarefa {$job->nome} falhou: " . ($resultado['erro'] ?? 'Erro desconhecido'));
            }
        }
    }
    
    \Log::info("Cron scheduler finalizado. Tarefas executadas: {$executados}");
})->everyMinute()->name('auto-cron-jobs');
