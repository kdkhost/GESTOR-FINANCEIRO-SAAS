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
                'nome' => 'Verificar Faturas Vencidas',
                'descricao' => 'Verifica faturas vencidas e atualiza status',
                'comando' => 'saas:verificar-faturas',
                'expressao_cron' => '0 * * * *', // A cada hora
                'ativo' => true,
            ],
            [
                'nome' => 'Gerar Faturas Mensais',
                'descricao' => 'Gera faturas mensais para assinaturas ativas',
                'comando' => 'saas:gerar-faturas',
                'expressao_cron' => '0 0 1 * *', // Dia 1 de cada mês à meia-noite
                'ativo' => true,
            ],
            [
                'nome' => 'Backup Database',
                'descricao' => 'Realiza backup do banco de dados',
                'comando' => 'backup:database',
                'expressao_cron' => '0 3 * * *', // 3h da manhã diariamente
                'ativo' => true,
            ],
            [
                'nome' => 'Backup Arquivos',
                'descricao' => 'Realiza backup dos arquivos de upload',
                'comando' => 'backup:arquivos',
                'expressao_cron' => '0 4 * * *', // 4h da manhã diariamente
                'ativo' => true,
            ],
            [
                'nome' => 'Limpar Cache',
                'descricao' => 'Limpa cache do sistema',
                'comando' => 'cache:clear',
                'expressao_cron' => '0 2 * * *', // 2h da manhã diariamente
                'ativo' => true,
            ],
            [
                'nome' => 'Limpar View Cache',
                'descricao' => 'Limpa cache de views compiladas',
                'comando' => 'view:clear',
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
