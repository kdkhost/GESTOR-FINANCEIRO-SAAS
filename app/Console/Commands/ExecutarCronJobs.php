<?php

namespace App\Console\Commands;

use App\Modules\Cron\Models\CronJob;
use Illuminate\Console\Command;

class ExecutarCronJobs extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'cron:executar {--id= : ID específico do job} {--manual : Executar mesmo fora do horário}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Executa tarefas cron agendadas';

    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        $jobId = $this->option('id');
        $manual = $this->option('manual');

        // Se especificou um ID, executa apenas aquele
        if ($jobId) {
            $job = CronJob::find($jobId);
            if (!$job) {
                $this->error("Job #{$jobId} não encontrado.");
                return self::FAILURE;
            }

            $this->info("Executando: {$job->nome}");
            $resultado = $job->executar();

            if ($resultado['sucesso']) {
                $this->info("✓ Sucesso em {$resultado['duracao_ms']}ms");
                return self::SUCCESS;
            } else {
                $this->error("✗ Falha: " . ($resultado['erro'] ?? 'Erro desconhecido'));
                return self::FAILURE;
            }
        }

        // Executa todos os jobs que devem rodar
        $jobs = CronJob::where('ativo', true)->get();
        $executados = 0;
        $falhas = 0;

        foreach ($jobs as $job) {
            // Se não é manual, só executa na hora certa
            if (!$manual && !$job->deveExecutar()) {
                continue;
            }

            $this->info("Executando: {$job->nome}");
            $resultado = $job->executar();

            if ($resultado['sucesso']) {
                $this->info("✓ Sucesso em {$resultado['duracao_ms']}ms");
                $executados++;
            } else {
                $this->error("✗ Falha: " . ($resultado['erro'] ?? 'Erro desconhecido'));
                $falhas++;
            }
        }

        $this->newLine();
        $this->info("Resumo: {$executados} sucesso(s), {$falhas} falha(s)");

        return $falhas > 0 ? self::FAILURE : self::SUCCESS;
    }
}
