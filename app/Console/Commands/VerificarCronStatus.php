<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Modules\Cron\Models\CronJob;
use Carbon\Carbon;

class VerificarCronStatus extends Command
{
    protected $signature = 'cron:verificar';
    protected $description = 'Verifica o status das tarefas cron e recalcula próximas execuções';

    public function handle(): int
    {
        $this->info('Verificando status do Cron...');
        $this->newLine();

        $jobs = CronJob::all();
        
        if ($jobs->isEmpty()) {
            $this->error('Nenhuma tarefa cron encontrada!');
            return Command::FAILURE;
        }

        $this->table(
            ['ID', 'Nome', 'Comando', 'Expressão', 'Ativo', 'Última Execução', 'Próxima Execução', 'Status'],
            $jobs->map(function ($job) {
                return [
                    $job->id,
                    $job->nome,
                    $job->comando,
                    $job->expressao_cron,
                    $job->ativo ? 'Sim' : 'Não',
                    $job->ultima_execucao ? $job->ultima_execucao->format('d/m/Y H:i:s') : 'Nunca',
                    $job->proxima_execucao ? $job->proxima_execucao->format('d/m/Y H:i:s') : '-',
                    $job->ultimo_status ?? 'pendente',
                ];
            })
        );

        $this->newLine();
        $this->info('Timezone do sistema: ' . config('app.timezone'));
        $this->info('Hora atual: ' . now()->format('d/m/Y H:i:s'));
        $this->newLine();

        // Verifica se há tarefas atrasadas
        $atrasadas = $jobs->filter(function ($job) {
            return $job->ativo && $job->proxima_execucao && now()->gt($job->proxima_execucao);
        });

        if ($atrasadas->isNotEmpty()) {
            $this->warn('Tarefas atrasadas (deveriam ter rodado):');
            foreach ($atrasadas as $job) {
                $this->warn("  - {$job->nome} (deveria rodar às {$job->proxima_execucao->format('H:i')})");
            }
        } else {
            $this->info('Nenhuma tarefa atrasada.');
        }

        // Recalcula próximas execuções para tarefas sem data
        $semProxima = $jobs->filter(function ($job) {
            return $job->ativo && !$job->proxima_execucao;
        });

        if ($semProxima->isNotEmpty()) {
            $this->newLine();
            $this->info('Recalculando próximas execuções...');
            foreach ($semProxima as $job) {
                $proxima = $job->calcularProximaExecucao();
                $job->update(['proxima_execucao' => $proxima]);
                $this->info("  ✓ {$job->nome} -> {$proxima->format('d/m/Y H:i:s')}");
            }
        }

        $this->newLine();
        $this->info('Verificação concluída!');

        return Command::SUCCESS;
    }
}
