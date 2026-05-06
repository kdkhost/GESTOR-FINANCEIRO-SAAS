<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Storage;

class BackupDatabase extends Command
{
    protected $signature = 'backup:database';
    protected $description = 'Realiza backup do banco de dados';

    public function handle(): int
    {
        $this->info('Iniciando backup do banco de dados...');

        $database = config('database.connections.mysql.database');
        $username = config('database.connections.mysql.username');
        $password = config('database.connections.mysql.password');
        $host = config('database.connections.mysql.host');

        $filename = "backup_{$database}_" . now()->format('Y-m-d_H-i-s') . '.sql';
        $path = storage_path('app/backups/' . $filename);

        // Cria diretório se não existir
        if (!File::exists(dirname($path))) {
            File::makeDirectory(dirname($path), 0755, true);
        }

        // Comando mysqldump
        $command = sprintf(
            'mysqldump -h%s -u%s -p%s %s > %s',
            $host,
            $username,
            $password,
            $database,
            $path
        );

        $output = shell_exec($command);

        if (File::exists($path)) {
            $size = File::size($path);
            $this->info("Backup concluído: {$filename} ({$size} bytes)");

            // Remove backups antigos (mantém últimos 7 dias)
            $this->limparBackupsAntigos();

            return Command::SUCCESS;
        }

        $this->error('Falha ao realizar backup');
        return Command::FAILURE;
    }

    private function limparBackupsAntigos(): void
    {
        $backupDir = storage_path('app/backups');
        $files = File::files($backupDir);
        
        // Ordena por data (mais antigos primeiro)
        usort($files, function ($a, $b) {
            return $a->getMTime() - $b->getMTime();
        });

        // Remove arquivos mais antigos mantendo apenas os 7 mais recentes
        $manter = 7;
        if (count($files) > $manter) {
            $remover = count($files) - $manter;
            for ($i = 0; $i < $remover; $i++) {
                File::delete($files[$i]->getPathname());
                $this->info("Removido backup antigo: {$files[$i]->getFilename()}");
            }
        }
    }
}
