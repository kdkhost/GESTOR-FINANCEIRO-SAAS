<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware): void {
        $middleware->alias([
            'instalacao' => \App\Http\Middleware\VerificarInstalacao::class,
            'admin' => \App\Http\Middleware\EnsureAdmin::class,
        ]);

        // Substitui o StartSession padrão por versão que trata graciosamente
        // a ausência da tabela sessions (sistema não instalado ainda).
        $middleware->replace(
            \Illuminate\Session\Middleware\StartSession::class,
            \App\Http\Middleware\StartSessionSegura::class,
        );
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        // Se a tabela de sessões não existir (sistema não instalado ainda),
        // redireciona para o instalador em vez de mostrar erro 500.
        $exceptions->render(function (\Illuminate\Database\QueryException $e, \Illuminate\Http\Request $request) {
            if (str_contains($e->getMessage(), "sessions' doesn't exist")) {
                // Corrige o .env para usar file driver e redireciona para o instalador
                $envPath = base_path('.env');
                if (file_exists($envPath)) {
                    $env = file_get_contents($envPath);
                    if (str_contains($env, 'SESSION_DRIVER=database')) {
                        $env = preg_replace('/^SESSION_DRIVER=.*/m', 'SESSION_DRIVER=file', $env);
                        $env = preg_replace('/^CACHE_STORE=.*/m',    'CACHE_STORE=file',    $env);
                        file_put_contents($envPath, $env);
                    }
                }
                return redirect('/instalar');
            }
        });
    })->create();
