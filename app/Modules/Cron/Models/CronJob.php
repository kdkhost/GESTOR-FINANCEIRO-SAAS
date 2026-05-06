<?php

namespace App\Modules\Cron\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class CronJob extends Model
{
    use HasFactory;

    protected $table = 'cron_jobs';

    protected $fillable = [
        'nome',
        'descricao',
        'comando',
        'expressao_cron',
        'ativo',
        'executar_manualmente',
        'ultima_execucao',
        'proxima_execucao',
        'ultimo_status',
        'duracao_ms',
    ];

    protected $casts = [
        'ativo' => 'boolean',
        'executar_manualmente' => 'boolean',
        'ultima_execucao' => 'datetime',
        'proxima_execucao' => 'datetime',
        'duracao_ms' => 'integer',
    ];

    public function logs(): HasMany
    {
        return $this->hasMany(CronLog::class, 'cron_job_id');
    }

    /**
     * Calcula a próxima execução baseado na expressão cron
     */
    public function calcularProximaExecucao(): ?\DateTime
    {
        if (!$this->ativo) {
            return null;
        }

        try {
            $cron = \Cron\CronExpression::factory($this->expressao_cron);
            // Usar o timezone do sistema (America/Sao_Paulo)
            return $cron->getNextRunDate(null, 0, false, config('app.timezone'));
        } catch (\Exception $e) {
            return null;
        }
    }

    /**
     * Verifica se está na hora de executar
     */
    public function deveExecutar(): bool
    {
        if (!$this->ativo) {
            return false;
        }

        if (!$this->proxima_execucao) {
            return true;
        }

        return now()->gte($this->proxima_execucao);
    }

    /**
     * Executa o comando e registra o log
     */
    public function executar(): array
    {
        $inicio = microtime(true);
        $this->update(['ultimo_status' => 'executando']);

        try {
            // Executa o comando Artisan
            $exitCode = \Artisan::call($this->comando);
            $saida = \Artisan::output();

            $duracao = round((microtime(true) - $inicio) * 1000);

            // Atualiza status
            $status = $exitCode === 0 ? 'sucesso' : 'erro';
            $this->update([
                'ultimo_status' => $status,
                'ultima_execucao' => now(),
                'duracao_ms' => $duracao,
                'proxima_execucao' => $this->calcularProximaExecucao(),
            ]);

            // Cria log
            $this->logs()->create([
                'status' => $status,
                'saida' => $saida,
                'erro' => $exitCode !== 0 ? "Exit code: {$exitCode}" : null,
                'duracao_ms' => $duracao,
                'executado_em' => now(),
            ]);

            return [
                'sucesso' => $exitCode === 0,
                'saida' => $saida,
                'duracao_ms' => $duracao,
            ];

        } catch (\Exception $e) {
            $duracao = round((microtime(true) - $inicio) * 1000);

            $this->update([
                'ultimo_status' => 'erro',
                'ultima_execucao' => now(),
                'duracao_ms' => $duracao,
                'proxima_execucao' => $this->calcularProximaExecucao(),
            ]);

            $this->logs()->create([
                'status' => 'erro',
                'saida' => null,
                'erro' => $e->getMessage(),
                'duracao_ms' => $duracao,
                'executado_em' => now(),
            ]);

            return [
                'sucesso' => false,
                'erro' => $e->getMessage(),
                'duracao_ms' => $duracao,
            ];
        }
    }
}
