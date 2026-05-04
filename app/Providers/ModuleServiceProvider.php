<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Schema;
use App\Modules\Modulos\Models\Modulo;

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
        'Modulos',
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

        foreach ($this->modulosAdicionaisAtivos() as $modulo) {
            if (! in_array($modulo, $this->modulos, true)) {
                $this->carregarRotas($modulo);
            }
        }

        $this->sincronizarModulosNativos();
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

    protected function modulosAdicionaisAtivos(): array
    {
        if (! Schema::hasTable('modulos')) {
            return [];
        }

        return Modulo::query()
            ->where('ativo', true)
            ->where('nativo', false)
            ->whereNotNull('diretorio')
            ->pluck('diretorio')
            ->filter()
            ->unique()
            ->values()
            ->all();
    }

    protected function sincronizarModulosNativos(): void
    {
        if (! Schema::hasTable('modulos')) {
            return;
        }

        foreach ($this->modulos as $modulo) {
            Modulo::query()->firstOrCreate(
                ['slug' => str($modulo)->kebab()->toString()],
                [
                    'nome' => $modulo,
                    'versao' => '1.0.0',
                    'diretorio' => $modulo,
                    'descricao' => 'Modulo nativo do sistema',
                    'ativo' => true,
                    'nativo' => true,
                ]
            );
        }
    }
}
