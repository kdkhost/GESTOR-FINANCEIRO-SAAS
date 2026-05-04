<?php

namespace App\Modules\Manutencao\Controllers;

use App\Http\Controllers\Controller;
use App\Modules\Configuracoes\Models\Configuracao;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class ManutencaoController extends Controller
{
    public function index()
    {
        return view('admin.manutencao.index');
    }

    public function salvar(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'manutencao_ativa' => ['nullable', 'in:0,1'],
            'manutencao_liberar_em' => ['nullable', 'string', 'max:32'],
            'manutencao_ips' => ['nullable', 'string', 'max:4000'],
            'manutencao_dispositivos' => ['nullable', 'string', 'max:4000'],
            'manutencao_mensagem' => ['nullable', 'string', 'max:2000'],
        ]);

        $manutencaoAtiva = ($validated['manutencao_ativa'] ?? '0') === '1';

        Configuracao::definir('manutencao_ativa', $manutencaoAtiva ? '1' : '0', 'manutencao');
        Configuracao::definir('manutencao_liberar_em', (string) ($validated['manutencao_liberar_em'] ?? ''), 'manutencao');
        Configuracao::definir('manutencao_ips', (string) ($validated['manutencao_ips'] ?? ''), 'manutencao');
        Configuracao::definir('manutencao_dispositivos', (string) ($validated['manutencao_dispositivos'] ?? ''), 'manutencao');
        Configuracao::definir('manutencao_mensagem', (string) ($validated['manutencao_mensagem'] ?? ''), 'manutencao');

        return response()->json(['sucesso' => true, 'mensagem' => 'Configuracoes de manutencao salvas com sucesso.']);
    }
}

