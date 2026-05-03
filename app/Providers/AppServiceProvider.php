<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        // Se o sistema ainda não foi instalado (tabela sessions não existe),
        // força SESSION_DRIVER=file para o instalador funcionar sem banco.
        // Isso precisa acontecer no register() — antes do StartSession middleware.
        $this->forcarSessionFileSeNaoInstalado();
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        //
    }

    /**
     * Detecta se o sistema está instalado e se o driver de sessão é compatível.
     * Se SESSION_DRIVER=database mas o banco/tabela não existe, muda para file
     * e persiste a correção no .env para evitar o erro nas próximas requisições.
     */
    private function forcarSessionFileSeNaoInstalado(): void
    {
        // Só age se o driver configurado for "database"
        if (config('session.driver') !== 'database') {
            return;
        }

        // Se o arquivo "installed" existe, o sistema está instalado — não interfere
        if (file_exists(storage_path('installed'))) {
            return;
        }

        // Sistema não instalado com SESSION_DRIVER=database: força file
        $this->corrigirDriverNoEnv();
        config(['session.driver' => 'file']);
        config(['cache.default'  => 'file']);
    }

    /**
     * Persiste SESSION_DRIVER=file e CACHE_STORE=file no .env do servidor.
     */
    private function corrigirDriverNoEnv(): void
    {
        $envPath = base_path('.env');

        if (! file_exists($envPath)) {
            return;
        }

        $env = file_get_contents($envPath);
        $alterado = false;

        if (str_contains($env, 'SESSION_DRIVER=database')) {
            $env      = preg_replace('/^SESSION_DRIVER=.*/m', 'SESSION_DRIVER=file', $env);
            $alterado = true;
        }

        if (str_contains($env, 'CACHE_STORE=database')) {
            $env      = preg_replace('/^CACHE_STORE=.*/m', 'CACHE_STORE=file', $env);
            $alterado = true;
        }

        if ($alterado) {
            file_put_contents($envPath, $env);
        }
    }
}
