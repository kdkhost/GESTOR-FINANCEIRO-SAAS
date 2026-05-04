<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Symfony\Component\HttpFoundation\Response;

class EnsureAdmin
{
    public function handle(Request $request, Closure $next): Response
    {
        // Para rotas administrativas, quando nao estiver autenticado devemos
        // redirecionar para login (em vez de retornar 403).
        if (!auth()->check()) {
            try {
                return redirect()->route('login');
            } catch (\Throwable $e) {
                return redirect('/login');
            }
        }

        $user = auth()->user();

        if ((bool) ($user?->is_admin ?? false)) {
            return $next($request);
        }

        $tipo = (string) ($user?->tipo ?? '');
        if (in_array($tipo, ['admin', 'superadmin'], true)) {
            return $next($request);
        }

        $id = $user?->getAuthIdentifier();
        if ($id) {
            $registro = DB::table('users')->select('tipo')->where('id', $id)->first();
            if (in_array((string) ($registro->tipo ?? ''), ['admin', 'superadmin'], true)) {
                return $next($request);
            }
        }

        if (DB::table('users')->count() === 1) {
            return $next($request);
        }

        abort(403);
    }
}
