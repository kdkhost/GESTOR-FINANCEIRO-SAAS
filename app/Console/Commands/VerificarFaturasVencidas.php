<?php

namespace App\Console\Commands;

use App\Modules\Saas\Models\Fatura;
use App\Modules\Saas\Models\Empresa;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;

class VerificarFaturasVencidas extends Command
{
    protected $signature = 'saas:verificar-faturas';
    protected $description = 'Verifica faturas vencidas e atualiza status';

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
            // Aqui você pode implementar envio de email/notificação
            Log::info("Fatura #{$fatura->id} vence em 3 dias - Empresa: {$fatura->empresa->nome_fantasia}");
        }

        $this->info("Faturas vencendo em 3 dias: {$vencendo->count()}");

        $this->info('Verificação concluída com sucesso!');
        return Command::SUCCESS;
    }
}
