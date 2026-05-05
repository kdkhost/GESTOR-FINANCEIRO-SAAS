<?php

namespace App\Modules\Financeiro\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

/**
 * Controller para o painel de usuarios comuns (nao-admin)
 */
class HomeController extends Controller
{
    public function index()
    {
        $user = auth()->user();

        // Dados basicos para o painel do usuario
        $dados = [
            'usuario' => $user,
            'resumo' => [
                'contas_pagar_pendentes' => 0,
                'contas_receber_pendentes' => 0,
                'saldo_total' => 0,
            ],
        ];

        return view('painel.index', $dados);
    }
}
