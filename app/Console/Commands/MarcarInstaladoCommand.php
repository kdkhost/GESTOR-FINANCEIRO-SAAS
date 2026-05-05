<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class MarcarInstaladoCommand extends Command
{
    protected $signature = 'sistema:marcar-instalado';
    protected $description = 'Marca o sistema como instalado (cria arquivo e flag no banco)';

    public function handle(): int
    {
        // Cria arquivo installed
        $path = storage_path('installed');
        if (! file_exists($path)) {
            file_put_contents($path, date('Y-m-d H:i:s'));
            $this->info('Arquivo storage/installed criado.');
        } else {
            $this->warn('Arquivo storage/installed ja existe.');
        }

        // Cria flag no banco
        try {
            DB::table('configuracoes')->updateOrInsert(
                ['chave' => 'instalacao_concluida'],
                [
                    'valor' => '1',
                    'grupo' => 'sistema',
                    'updated_at' => now(),
                    'created_at' => now(),
                ]
            );
            $this->info('Flag instalacao_concluida salva no banco.');
        } catch (\Throwable $e) {
            $this->error('Erro ao salvar no banco: ' . $e->getMessage());
        }

        $this->info('Sistema marcado como instalado!');
        return self::SUCCESS;
    }
}
