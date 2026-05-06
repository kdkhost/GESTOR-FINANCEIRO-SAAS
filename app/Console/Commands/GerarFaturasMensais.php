<?php

namespace App\Console\Commands;

use App\Modules\Saas\Models\Assinatura;
use App\Modules\Saas\Models\Fatura;
use App\Modules\Saas\Models\Plano;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;

class GerarFaturasMensais extends Command
{
    protected $signature = 'saas:gerar-faturas {--mes= : Mês para gerar (1-12)} {--ano= : Ano para gerar}';
    protected $description = 'Gera faturas mensais para assinaturas ativas';

    public function handle(): int
    {
        $mes = $this->option('mes') ?? now()->month;
        $ano = $this->option('ano') ?? now()->year;

        $this->info("Gerando faturas para {$mes}/{$ano}...");

        $competencia = sprintf('%02d/%04d', $mes, $ano);
        
        // Busca assinaturas ativas
        $assinaturas = Assinatura::where('status', 'ativa')
            ->with(['empresa', 'plano'])
            ->get();

        $geradas = 0;
        $existentes = 0;

        foreach ($assinaturas as $assinatura) {
            // Verifica se já existe fatura para esta competência
            $existente = Fatura::where('assinatura_id', $assinatura->id)
                ->where('competencia', $competencia)
                ->first();

            if ($existente) {
                $existentes++;
                continue;
            }

            // Determina valor (mensal ou anual)
            $valor = $assinatura->plano->valor_mensal;
            
            // Calcula data de vencimento (dia 10 do mês seguinte)
            $vencimento = now()->setYear($ano)->setMonth($mes)->addMonth()->day(10);

            // Gera fatura
            Fatura::create([
                'empresa_id' => $assinatura->empresa_id,
                'assinatura_id' => $assinatura->id,
                'status' => 'aberta',
                'competencia' => $competencia,
                'valor' => $valor,
                'vencimento_em' => $vencimento,
                'proxima_cobranca_em' => $vencimento,
            ]);

            $geradas++;
            $this->info("Fatura gerada: {$assinatura->empresa->nome_fantasia} - R$ {$valor}");
        }

        $this->info("Faturas geradas: {$geradas}");
        $this->info("Faturas já existentes: {$existentes}");
        $this->info('Processo concluído com sucesso!');

        return Command::SUCCESS;
    }
}
