<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Storage;
use Symfony\Component\HttpFoundation\BinaryFileResponse;

class BackupController extends Controller
{
    public function index()
    {
        $backups = $this->listarBackups();
        return view('admin.backup.index', compact('backups'));
    }

    public function listar()
    {
        $backups = $this->listarBackups();
        return response()->json([
            'sucesso' => true,
            'backups' => $backups
        ]);
    }

    public function executar(Request $request)
    {
        $tipo = $request->input('tipo', 'database');
        
        try {
            if ($tipo === 'database') {
                $exitCode = \Artisan::call('backup:database');
                $output = \Artisan::output();
            } else {
                $exitCode = \Artisan::call('backup:arquivos');
                $output = \Artisan::output();
            }

            if ($exitCode === 0) {
                return response()->json([
                    'sucesso' => true,
                    'mensagem' => 'Backup realizado com sucesso!',
                    'saida' => $output
                ]);
            } else {
                return response()->json([
                    'sucesso' => false,
                    'mensagem' => 'Erro ao executar backup',
                    'saida' => $output
                ], 500);
            }
        } catch (\Exception $e) {
            return response()->json([
                'sucesso' => false,
                'mensagem' => 'Erro: ' . $e->getMessage()
            ], 500);
        }
    }

    public function download($arquivo)
    {
        $path = storage_path('app/backups/' . $arquivo);
        
        if (!File::exists($path)) {
            abort(404, 'Arquivo não encontrado');
        }

        return response()->download($path);
    }

    public function deletar($arquivo)
    {
        $path = storage_path('app/backups/' . $arquivo);
        
        if (!File::exists($path)) {
            return response()->json([
                'sucesso' => false,
                'mensagem' => 'Arquivo não encontrado'
            ], 404);
        }

        try {
            File::delete($path);
            return response()->json([
                'sucesso' => true,
                'mensagem' => 'Backup removido com sucesso!'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'sucesso' => false,
                'mensagem' => 'Erro ao remover: ' . $e->getMessage()
            ], 500);
        }
    }

    private function listarBackups(): array
    {
        $backupDir = storage_path('app/backups');
        $backups = [];

        if (File::exists($backupDir)) {
            $files = File::files($backupDir);
            
            usort($files, function ($a, $b) {
                return $b->getMTime() - $a->getMTime();
            });

            foreach ($files as $file) {
                $backups[] = [
                    'nome' => $file->getFilename(),
                    'tamanho' => $this->formatarTamanho($file->getSize()),
                    'tamanho_bytes' => $file->getSize(),
                    'criado_em' => date('d/m/Y H:i', $file->getMTime()),
                    'tipo' => str_contains($file->getFilename(), 'database') ? 'database' : 'arquivos'
                ];
            }
        }

        return $backups;
    }

    private function formatarTamanho(int $bytes): string
    {
        $units = ['B', 'KB', 'MB', 'GB', 'TB'];
        $unitIndex = 0;
        
        while ($bytes >= 1024 && $unitIndex < count($units) - 1) {
            $bytes /= 1024;
            $unitIndex++;
        }
        
        return round($bytes, 2) . ' ' . $units[$unitIndex];
    }
}
