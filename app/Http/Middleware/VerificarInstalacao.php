<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class VerificarInstalacao
{
    /**
     * Bloqueia acesso ao instalador se o sistema já foi instalado.
     */
    public function handle(Request $request, Closure $next): Response
    {
        if (file_exists(storage_path('installed'))) {
            return redirect('/admin/dashboard');
        }

        return $next($request);
    }
}
