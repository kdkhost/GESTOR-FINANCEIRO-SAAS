<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

Route::get('/', function () {
    // Se usuario estiver logado, redireciona conforme o tipo
    if (Auth::check()) {
        // Se for admin/superadmin, vai para dashboard admin
        if (Auth::user()->is_admin) {
            return redirect('/admin/dashboard');
        }
        // Usuario comum vai para o painel
        return redirect('/painel');
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

// Painel para usuarios comuns (apenas auth, sem middleware admin)
Route::middleware(['auth', 'web'])->group(function () {
    Route::get('/painel', [\App\Modules\Financeiro\Controllers\HomeController::class, 'index'])->name('painel');
});
