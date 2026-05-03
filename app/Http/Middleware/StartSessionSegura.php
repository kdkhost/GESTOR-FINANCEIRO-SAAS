<?php

namespace App\Http\Middleware;

use Illuminate\Session\Middleware\StartSession;
use Illuminate\Http\Request;
use Closure;

/**
 * Substitui o StartSession padrão do Laravel para tratar graciosamente
 * o caso em que SESSION_DRIVER=database mas a tabela sessions ainda
 * não existe (sistema não instalado).
 *
 * Nesse caso, força o driver para "cookie" em memória e redireciona
 * para o instalador sem mostrar erro 500.
 */
class StartSessionSegura extends StartSession
{
    public function handle($request, Closure $next)
    {
        try {
            return parent::handle($request, $next);
        } catch (\Illuminate\Database\QueryException $e) {
            if ($this->ehErroDeTabelaSessao($e)) {
                return $this->tratarSessaoInexistente($request, $next, $e);
            }
            throw $e;
        } catch (\PDOException $e) {
            if ($this->ehErroDeTabelaSessao($e)) {
                return $this->tratarSessaoInexistente($request, $next, $e);
            }
            throw $e;
        }
    }

    private function ehErroDeTabelaSessao(\Throwable $e): bool
    {
        return str_contains($e->getMessage(), "sessions' doesn't exist")
            || str_contains($e->getMessage(), 'sessions')
               && str_contains($e->getMessage(), '1146');
    }

    private function tratarSessaoInexistente(Request $request, Closure $next, \Throwable $e)
    {
        // Corrige o .env permanentemente
        $this->corrigirEnv();

        // Força driver cookie em memória para esta requisição
        config(['session.driver' => 'cookie']);
        app()->forgetInstance('session');
        app()->forgetInstance('session.store');

        // Tenta processar com o novo driver
        try {
            return parent::handle($request, $next);
        } catch (\Throwable) {
            // Último recurso: redireciona direto para o instalador
            return redirect('/instalar');
        }
    }

    private function corrigirEnv(): void
    {
        $envPath = base_path('.env');
        if (! file_exists($envPath)) {
            return;
        }

        $env      = file_get_contents($envPath);
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