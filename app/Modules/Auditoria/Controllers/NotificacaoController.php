<?php

namespace App\Modules\Auditoria\Controllers;

use App\Http\Controllers\Controller;
use App\Modules\Auditoria\Models\Notificacao;
use Illuminate\Http\JsonResponse;

class NotificacaoController extends Controller
{


    public function naoLidas(): JsonResponse
    {
        $notificacoes = Notificacao::where('user_id', auth()->id())
            ->where('lida', false)
            ->orderByDesc('created_at')
            ->limit(20)
            ->get();

        return response()->json([
            'sucesso'       => true,
            'total'         => $notificacoes->count(),
            'notificacoes'  => $notificacoes,
        ]);
    }

    public function marcarLida(int $id): JsonResponse
    {
        $notificacao = Notificacao::where('id', $id)
            ->where('user_id', auth()->id())
            ->firstOrFail();

        $notificacao->update(['lida' => true, 'lida_em' => now()]);

        return response()->json(['sucesso' => true, 'mensagem' => 'Notificação marcada como lida.']);
    }
}
