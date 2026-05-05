<?php

namespace App\Modules\Cron\Controllers;

use App\Http\Controllers\Controller;
use App\Modules\Cron\Models\CronJob;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class CronApiController extends Controller
{
    /**
     * Lista todas as tarefas cron
     */
    public function index(): JsonResponse
    {
        $jobs = CronJob::orderBy('nome')->get();

        // Adiciona informações extras
        $jobs->each(function ($job) {
            $job->frequencia_formatada = $this->formatarFrequencia($job->expressao_cron);
            $job->ultima_execucao_formatada = $job->ultima_execucao
                ? $job->ultima_execucao->format('d/m/Y H:i')
                : 'Nunca';
            $job->proxima_execucao_formatada = $job->proxima_execucao
                ? $job->proxima_execucao->format('d/m/Y H:i')
                : ($job->ativo ? 'Aguardando...' : 'Inativo');
        });

        return response()->json([
            'sucesso' => true,
            'dados' => $jobs,
        ]);
    }

    /**
     * Cria nova tarefa
     */
    public function store(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'nome' => 'required|string|max:100',
            'descricao' => 'nullable|string|max:255',
            'comando' => 'required|string|max:100',
            'expressao_cron' => 'required|string|max:50',
            'ativo' => 'boolean',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'sucesso' => false,
                'erros' => $validator->errors(),
            ], 422);
        }

        $dados = $request->only(['nome', 'descricao', 'comando', 'expressao_cron', 'ativo']);
        $dados['ativo'] = $request->boolean('ativo', true);
        $dados['ultimo_status'] = 'pendente';

        // Calcula próxima execução
        $cron = \Cron\CronExpression::factory($dados['expressao_cron']);
        $dados['proxima_execucao'] = $cron->getNextRunDate();

        $job = CronJob::create($dados);

        auditoria('criou', 'Cron', 'cron_jobs', $job->id, null, $dados);

        return response()->json([
            'sucesso' => true,
            'mensagem' => 'Tarefa criada com sucesso!',
            'dados' => $job,
        ]);
    }

    /**
     * Atualiza tarefa
     */
    public function update(Request $request, int $id): JsonResponse
    {
        $job = CronJob::find($id);
        if (!$job) {
            return response()->json([
                'sucesso' => false,
                'mensagem' => 'Tarefa não encontrada.',
            ], 404);
        }

        $validator = Validator::make($request->all(), [
            'nome' => 'required|string|max:100',
            'descricao' => 'nullable|string|max:255',
            'comando' => 'required|string|max:100',
            'expressao_cron' => 'required|string|max:50',
            'ativo' => 'boolean',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'sucesso' => false,
                'erros' => $validator->errors(),
            ], 422);
        }

        $anterior = $job->toArray();

        $dados = $request->only(['nome', 'descricao', 'comando', 'expressao_cron', 'ativo']);
        $dados['ativo'] = $request->boolean('ativo', true);

        // Recalcula próxima execução se mudou a expressão
        if ($dados['expressao_cron'] !== $job->expressao_cron || $dados['ativo'] !== $job->ativo) {
            if ($dados['ativo']) {
                $cron = \Cron\CronExpression::factory($dados['expressao_cron']);
                $dados['proxima_execucao'] = $cron->getNextRunDate();
            } else {
                $dados['proxima_execucao'] = null;
            }
        }

        $job->update($dados);

        auditoria('editou', 'Cron', 'cron_jobs', $job->id, $anterior, $job->toArray());

        return response()->json([
            'sucesso' => true,
            'mensagem' => 'Tarefa atualizada com sucesso!',
            'dados' => $job,
        ]);
    }

    /**
     * Exclui tarefa
     */
    public function destroy(int $id): JsonResponse
    {
        $job = CronJob::find($id);
        if (!$job) {
            return response()->json([
                'sucesso' => false,
                'mensagem' => 'Tarefa não encontrada.',
            ], 404);
        }

        $dados = $job->toArray();
        $job->delete();

        auditoria('excluiu', 'Cron', 'cron_jobs', $id, $dados, null);

        return response()->json([
            'sucesso' => true,
            'mensagem' => 'Tarefa excluída com sucesso!',
        ]);
    }

    /**
     * Executa tarefa manualmente
     */
    public function executar(int $id): JsonResponse
    {
        $job = CronJob::find($id);
        if (!$job) {
            return response()->json([
                'sucesso' => false,
                'mensagem' => 'Tarefa não encontrada.',
            ], 404);
        }

        $resultado = $job->executar();

        return response()->json([
            'sucesso' => $resultado['sucesso'],
            'mensagem' => $resultado['sucesso'] ? 'Tarefa executada com sucesso!' : 'Erro ao executar tarefa.',
            'duracao_ms' => $resultado['duracao_ms'],
            'saida' => $resultado['saida'] ?? null,
            'erro' => $resultado['erro'] ?? null,
        ]);
    }

    /**
     * Lista logs da tarefa
     */
    public function logs(int $id): JsonResponse
    {
        $job = CronJob::find($id);
        if (!$job) {
            return response()->json([
                'sucesso' => false,
                'mensagem' => 'Tarefa não encontrada.',
            ], 404);
        }

        $logs = $job->logs()
            ->orderBy('executado_em', 'desc')
            ->limit(50)
            ->get();

        // Formata datas para exibição
        $logs->transform(function ($log) {
            $log->executado_em_formatado = $log->executado_em ? $log->executado_em->format('d/m/Y H:i') : null;
            return $log;
        });

        return response()->json([
            'sucesso' => true,
            'dados' => $logs,
        ]);
    }

    /**
     * Estatísticas do cron
     */
    public function estatisticas(): JsonResponse
    {
        $hoje = now()->startOfDay();

        return response()->json([
            'sucesso' => true,
            'ativas' => CronJob::where('ativo', true)->count(),
            'executadas_hoje' => \App\Modules\Cron\Models\CronLog::whereDate('executado_em', $hoje)->count(),
            'falhas' => CronJob::where('ultimo_status', 'erro')->count(),
            'proxima_execucao' => CronJob::where('ativo', true)
                ->whereNotNull('proxima_execucao')
                ->orderBy('proxima_execucao')
                ->first()?->proxima_execucao?->format('H:i'),
        ]);
    }

    /**
     * Formata expressão cron para texto legível
     */
    private function formatarFrequencia(string $expressao): string
    {
        $map = [
            '* * * * *' => 'A cada minuto',
            '*/5 * * * *' => 'A cada 5 minutos',
            '*/10 * * * *' => 'A cada 10 minutos',
            '*/15 * * * *' => 'A cada 15 minutos',
            '*/30 * * * *' => 'A cada 30 minutos',
            '0 * * * *' => 'A cada hora',
            '0 */2 * * *' => 'A cada 2 horas',
            '0 */6 * * *' => 'A cada 6 horas',
            '0 */12 * * *' => 'A cada 12 horas',
            '0 0 * * *' => 'Diário',
            '0 0 * * 0' => 'Semanal',
            '0 0 1 * *' => 'Mensal',
            '0 0 1 1 *' => 'Anual',
        ];

        return $map[$expressao] ?? $expressao;
    }
}
