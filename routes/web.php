<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

Route::get('/', function () {
    // Se usuario estiver logado, vai para dashboard
    if (Auth::check()) {
        return redirect('/admin/dashboard');
    }

    // Verifica se sistema esta instalado (arquivo ou banco)
    $arquivoExiste = file_exists(storage_path('installed'));
    $bdExiste = false;

    try {
        if (DB::connection()->getPdo()) {
            $bdExiste = DB::table('configuracoes')->where('chave', 'instalacao_concluida')->where('valor', '1')->exists();
        }
    } catch (\Throwable $e) {
        // Banco nao acessivel, considera nao instalado
    }

    // Se nao instalado, vai para instalador
    if (! $arquivoExiste && ! $bdExiste) {
        return redirect('/instalar');
    }

    // Se instalado mas nao logado, vai para login
    return redirect('/login');
});

