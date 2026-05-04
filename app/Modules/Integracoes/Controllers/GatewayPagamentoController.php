<?php

namespace App\Modules\Integracoes\Controllers;

use App\Http\Controllers\Controller;
use App\Modules\Integracoes\Models\GatewayPagamento;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

class GatewayPagamentoController extends Controller
{
    private array $padroes = [
        ['nome' => 'Mercado Pago', 'identificador' => 'mercadopago'],
        ['nome' => 'Stripe', 'identificador' => 'stripe'],
        ['nome' => 'Asaas', 'identificador' => 'asaas'],
    ];

    public function index()
    {
        foreach ($this->padroes as $padrao) {
            GatewayPagamento::firstOrCreate(
                ['identificador' => $padrao['identificador']],
                ['nome' => $padrao['nome'], 'ativo' => false, 'sandbox' => true, 'credenciais' => [], 'configuracoes' => []]
            );
        }

        $gateways = GatewayPagamento::orderBy('nome')->get();

        return view('admin.integracoes.gateways.index', compact('gateways'));
    }

    public function update(Request $request, GatewayPagamento $gateway): JsonResponse
    {
        $validated = $request->validate([
            'ativo'        => 'sometimes|boolean',
            'sandbox'      => 'sometimes|boolean',
            'credenciais'  => 'sometimes|array',
            'configuracoes' => 'sometimes|array',
        ]);

        $gateway->update([
            'ativo'         => $request->boolean('ativo'),
            'sandbox'       => $request->boolean('sandbox'),
            'credenciais'   => $request->input('credenciais', []),
            'configuracoes' => $request->input('configuracoes', []),
        ]);

        return response()->json([
            'sucesso' => true,
            'mensagem' => 'Gateway salvo com sucesso!',
            'gateway' => $gateway->fresh(),
        ]);
    }
}
