<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\File;

class ModuleServiceProvider extends ServiceProvider
{
    /**
     * Lista de módulos registrados no sistema.
     */
    protected array $modulos = [
        'Instalador',
        'Usuarios',
        'Permissoes',
        'Financeiro',
        'Relatorios',
        'Configuracoes',
        'Integracoes',
        'Pwa',
        'Cron',
        'Auditoria',
    ];

    /**
     * Registra bindings e serviços dos módulos.
     */
    public function register(): void
    {
        foreach ($this->modulos as $modulo) {
            $this->registrarServicos($modulo);
        }
    }

    /**
     * Inicializa rotas, views e outras configurações dos módulos.
     */
    public function boot(): void
    {
        foreach ($this->modulos as $modulo) {
            $this->carregarRotas($modulo);
        }
    }

    /**
     * Carrega as rotas web e API de cada módulo.
     */
    protected function carregarRotas(string $modulo): void
    {
        $baseModulo = app_path("Modules/{$modulo}/Routes");

        $rotaWeb = "{$baseModulo}/web.php";
        if (File::exists($rotaWeb)) {
            Route::middleware('web')
                ->group($rotaWeb);
        }

        $rotaApi = "{$baseModulo}/api.php";
        if (File::exists($rotaApi)) {
            Route::middleware('api')
                ->prefix('api')
                ->group($rotaApi);
        }
    }

    /**
     * Registra os service providers de cada módulo (se existirem).
     */
    protected function registrarServicos(string $modulo): void
    {
        $provider = "App\\Modules\\{$modulo}\\{$modulo}ServiceProvider";
        if (class_exists($provider)) {
            $this->app->register($provider);
        }
    }
}
