<?php

namespace App\Console\Commands;

use App\Modules\Saas\Models\Fatura;
use App\Modules\Saas\Models\Empresa;
use App\Services\EmailService;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;

class VerificarFaturasVencidas extends Command
{
    protected $signature = 'saas:verificar-faturas';
    protected $description = 'Verifica faturas vencidas e atualiza status';

    public function __construct(private EmailService $emailService)
    {
        parent::__construct();
    }

    public function handle(): int
    {
        $this->info('Verificando faturas vencidas...');

        $hoje = now()->startOfDay();
        
        // Atualiza faturas vencidas
        $vencidas = Fatura::where('status', 'aberta')
            ->where('vencimento_em', '<', $hoje)
            ->update(['status' => 'vencida']);

        $this->info("Faturas vencidas atualizadas: {$vencidas}");

        // Envia alertas para faturas vencendo em 3 dias
        $alertaEm = now()->addDays(3)->startOfDay();
        $vencendo = Fatura::where('status', 'aberta')
            ->where('vencimento_em', $alertaEm)
            ->with('empresa')
            ->get();

        foreach ($vencendo as $fatura) {
            $this->emailService->enviarComTemplate('fatura_vencendo', $fatura->empresa->email, [
                'nome_empresa' => $fatura->empresa->nome_fantasia,
                'numero_fatura' => $fatura->id,
                'competencia' => $fatura->competencia,
                'valor' => number_format($fatura->valor, 2, ',', '.'),
                'vencimento' => $fatura->vencimento_em->format('d/m/Y'),
                'dias_restantes' => 3,
                'link_pagamento' => $fatura->link_pagamento ?? '#',
            ]);
            Log::info("Alerta enviado para fatura #{$fatura->id} - Empresa: {$fatura->empresa->nome_fantasia}");
        }

        $this->info("Faturas vencendo em 3 dias: {$vencendo->count()}");

        // Envia emails para faturas recém vencidas (hoje)
        $vencidasHoje = Fatura::where('status', 'vencida')
            ->whereDate('vencimento_em', $hoje)
            ->with('empresa')
            ->get();

        foreach ($vencidasHoje as $fatura) {
            $this->emailService->enviarComTemplate('fatura_vencida', $fatura->empresa->email, [
                'nome_empresa' => $fatura->empresa->nome_fantasia,
                'numero_fatura' => $fatura->id,
                'competencia' => $fatura->competencia,
                'valor' => number_format($fatura->valor, 2, ',', '.'),
                'vencimento' => $fatura->vencimento_em->format('d/m/Y'),
                'dias_atraso' => 0,
                'link_pagamento' => $fatura->link_pagamento ?? '#',
            ]);
            Log::info("Email de vencimento enviado para fatura #{$fatura->id}");
        }

        $this->info("Emails de vencimento enviados: {$vencidasHoje->count()}");

        $this->info('Verificação concluída com sucesso!');
        return Command::SUCCESS;
    }
}
