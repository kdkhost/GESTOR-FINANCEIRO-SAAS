<?php

namespace App\Modules\Integracoes\Controllers;

use App\Http\Controllers\Controller;
use App\Modules\Integracoes\Models\GatewayPagamento;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

class GatewayPagamentoController extends Controller
{
    private function exigirAdmin(): void
    {
        abort_unless(auth()->check() && auth()->user()?->is_admin, 403);
    }

    private array $padroes = [
        ['nome' => 'Mercado Pago', 'identificador' => 'mercadopago'],
        ['nome' => 'Stripe', 'identificador' => 'stripe'],
        ['nome' => 'Asaas', 'identificador' => 'asaas'],
    ];

    public function index()
    {
        $this->exigirAdmin();
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
        $this->exigirAdmin();

        $validated = $request->validate([
            'ativo'        => 'sometimes|boolean',
            'sandbox'      => 'sometimes|boolean',
            'credenciais'  => 'sometimes|array',
            'configuracoes' => 'sometimes|array',
        ]);

        $gateway->update([
            'ativo'         => $request->has('ativo') ? $request->boolean('ativo') : $gateway->ativo,
            'sandbox'       => $request->has('sandbox') ? $request->boolean('sandbox') : $gateway->sandbox,
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
