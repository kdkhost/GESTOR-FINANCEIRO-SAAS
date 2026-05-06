<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Storage;
use ZipArchive;

class BackupArquivos extends Command
{
    protected $signature = 'backup:arquivos';
    protected $description = 'Realiza backup dos arquivos de upload';

    public function handle(): int
    {
        $this->info('Iniciando backup dos arquivos...');

        $filename = "backup_arquivos_" . now()->format('Y-m-d_H-i-s') . '.zip';
        $path = storage_path('app/backups/' . $filename);

        // Cria diretório se não existir
        if (!File::exists(dirname($path))) {
            File::makeDirectory(dirname($path), 0755, true);
        }

        $zip = new ZipArchive();
        if ($zip->open($path, ZipArchive::CREATE) !== true) {
            $this->error('Falha ao criar arquivo ZIP');
            return Command::FAILURE;
        }

        // Adiciona diretórios de upload
        $dirs = [
            public_path('uploads'),
            storage_path('app/public'),
        ];

        foreach ($dirs as $dir) {
            if (File::exists($dir)) {
                $this->info("Adicionando: {$dir}");
                $this->adicionarDiretorioAoZip($zip, $dir, basename($dir));
            }
        }

        $zip->close();

        if (File::exists($path)) {
            $size = File::size($path);
            $this->info("Backup concluído: {$filename} ({$size} bytes)");

            // Remove backups antigos
            $this->limparBackupsAntigos();

            return Command::SUCCESS;
        }

        $this->error('Falha ao realizar backup');
        return Command::FAILURE;
    }

    private function adicionarDiretorioAoZip(ZipArchive $zip, string $dir, string $baseName): void
    {
        $files = File::files($dir);
        foreach ($files as $file) {
            $zip->addFile($file->getPathname(), $baseName . '/' . $file->getFilename());
        }

        $directories = File::directories($dir);
        foreach ($directories as $directory) {
            $this->adicionarDiretorioAoZip($zip, $directory, $baseName . '/' . basename($directory));
        }
    }

    private function limparBackupsAntigos(): void
    {
        $backupDir = storage_path('app/backups');
        $files = File::files($backupDir);
        
        usort($files, function ($a, $b) {
            return $a->getMTime() - $b->getMTime();
        });

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
