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
                'nome' => 'Otimizar Cache',
                'descricao' => 'Otimiza cache do sistema',
                'comando' => 'cache:optimize',
                'expressao_cron' => '0 3 * * *', // 3h da manhã diariamente
                'ativo' => true,
            ],
            [
                'nome' => 'Limpar View Cache',
                'descricao' => 'Limpa cache de views compiladas',
                'comando' => 'view:clear',
                'expressao_cron' => '0 4 * * *', // 4h da manhã diariamente
                'ativo' => true,
            ],
            [
                'nome' => 'Limpar Config Cache',
                'descricao' => 'Limpa cache de configurações',
                'comando' => 'config:clear',
                'expressao_cron' => '0 5 * * *', // 5h da manhã diariamente
                'ativo' => true,
            ],
        ];

        foreach ($tarefas as $tarefa) {
            // Verifica se já existe para não duplicar
            $existente = CronJob::where('comando', $tarefa['comando'])->first();
            
            if (!$existente) {
                // Calcula próxima execução
                $cron = \Cron\CronExpression::factory($tarefa['expressao_cron']);
                $tarefa['proxima_execucao'] = $cron->getNextRunDate();
                $tarefa['ultimo_status'] = 'pendente';

                CronJob::create($tarefa);
            }
        }
    }
}
