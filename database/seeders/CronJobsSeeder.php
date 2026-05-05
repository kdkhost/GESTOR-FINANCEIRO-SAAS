<?php

namespace Database\Seeders;

use App\Modules\Cron\Models\CronJob;
use Illuminate\Database\Seeder;
use Carbon\Carbon;

class CronJobsSeeder extends Seeder
{
    public function run(): void
    {
        $tarefas = [
            [
                'nome' => 'Limpar Cache',
                'descricao' => 'Limpa cache do sistema',
                'comando' => 'cache:clear',
                'expressao_cron' => '0 2 * * *', // 2h da manhã diariamente
                'ativo' => true,
            ],
            [
                'nome' => 'Backup Database',
                'descricao' => 'Backup da base de dados',
                'comando' => 'backup:run',
                'expressao_cron' => '0 3 * * *', // 3h da manhã diariamente
                'ativo' => true,
            ],
            [
                'nome' => 'Verificar Faturas',
                'descricao' => 'Verifica faturas vencidas',
                'comando' => 'saas:verificar-faturas',
                'expressao_cron' => '0 * * * *', // A cada hora
                'ativo' => true,
            ],
            [
                'nome' => 'Limpar Auditoria Antiga',
                'descricao' => 'Remove logs antigos de auditoria',
                'comando' => 'auditoria:limpar --dias=30',
                'expressao_cron' => '0 4 * * 0', // Domingo 4h da manhã (semanal)
                'ativo' => true,
            ],
        ];

        foreach ($tarefas as $tarefa) {
            // Calcula próxima execução
            $cron = \Cron\CronExpression::factory($tarefa['expressao_cron']);
            $tarefa['proxima_execucao'] = $cron->getNextRunDate();
            $tarefa['ultimo_status'] = 'pendente';

            CronJob::create($tarefa);
        }
    }
}
