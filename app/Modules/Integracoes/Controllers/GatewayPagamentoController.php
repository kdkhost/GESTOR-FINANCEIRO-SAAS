<?php

namespace App\Modules\Integracoes\Controllers;

use App\Http\Controllers\Controller;
use App\Modules\Integracoes\Models\GatewayPagamento;
use App\Modules\Integracoes\Services\MercadoPagoOrderService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;

class GatewayPagamentoController extends Controller
{
    private function exigirAdmin(): void
    {
        abort_unless(auth()->check() && auth()->user()?->is_admin, 403);
    }

    private array $padroes = [
        [
            'nome' => 'Mercado Pago',
            'identificador' => 'mercadopago',
            'credenciais' => [
                'access_token' => '',
                'public_key' => '',
                'webhook_secret' => '',
            ],
            'configuracoes' => [
                'processing_mode' => 'automatic',
                'pix_ativo' => true,
                'boleto_ativo' => true,
                'cartao_credito_ativo' => true,
                'cartao_debito_ativo' => true,
                'pix_expiration_time' => 'P1D',
                'boleto_expiration_time' => 'P3D',
                'card_brick_enabled' => true,
            ],
        ],
        [
            'nome' => 'Stripe',
            'identificador' => 'stripe',
            'credenciais' => ['secret_key' => '', 'publishable_key' => ''],
            'configuracoes' => ['cartao_credito_ativo' => true],
        ],
        [
            'nome' => 'Asaas',
            'identificador' => 'asaas',
            'credenciais' => ['token' => '', 'account_id' => ''],
            'configuracoes' => ['pix_ativo' => true, 'boleto_ativo' => true],
        ],
    ];

    public function index()
    {
        $this->exigirAdmin();
        $this->sincronizarPadroes();

        $gateways = GatewayPagamento::orderBy('nome')->get();

        return view('admin.integracoes.gateways.index', compact('gateways'));
    }

    public function update(Request $request, GatewayPagamento $gateway): JsonResponse
    {
        $this->exigirAdmin();

        $validated = $request->validate([
            'ativo' => 'sometimes|boolean',
            'sandbox' => 'sometimes|boolean',
            'credenciais' => 'sometimes|array',
            'credenciais.*' => 'nullable|string|max:4000',
            'configuracoes' => 'sometimes|array',
            'configuracoes.*' => 'nullable',
        ]);

        $credenciais = array_filter($request->input('credenciais', []), fn ($valor) => $valor !== null);
        $configuracoes = array_replace_recursive($gateway->configuracoes ?? [], $request->input('configuracoes', []));

        if ($gateway->identificador === 'mercadopago' && $request->boolean('ativo') && blank($credenciais['access_token'] ?? null)) {
            throw ValidationException::withMessages([
                'credenciais.access_token' => 'Informe o Access Token para ativar o Mercado Pago.',
            ]);
        }

        $gateway->update([
            'ativo' => $request->has('ativo') ? $request->boolean('ativo') : $gateway->ativo,
            'sandbox' => $request->has('sandbox') ? $request->boolean('sandbox') : $gateway->sandbox,
            'credenciais' => $credenciais,
            'configuracoes' => $configuracoes,
        ]);

        return response()->json([
            'sucesso' => true,
            'mensagem' => 'Gateway salvo com sucesso!',
            'gateway' => $gateway->fresh(),
        ]);
    }

    public function testar(GatewayPagamento $gateway, MercadoPagoOrderService $mercadoPago): JsonResponse
    {
        $this->exigirAdmin();

        if ($gateway->identificador !== 'mercadopago') {
            throw ValidationException::withMessages([
                'gateway' => 'Teste automatico disponivel apenas para Mercado Pago nesta etapa.',
            ]);
        }

        $resultado = $mercadoPago->testarCredenciais($gateway);

        return response()->json([
            'sucesso' => true,
            'mensagem' => 'Credenciais Mercado Pago validadas com sucesso!',
            'resultado' => $resultado,
        ]);
    }

    private function sincronizarPadroes(): void
    {
        foreach ($this->padroes as $padrao) {
            $gateway = GatewayPagamento::firstOrCreate(
                ['identificador' => $padrao['identificador']],
                [
                    'nome' => $padrao['nome'],
                    'ativo' => false,
                    'sandbox' => true,
                    'credenciais' => $padrao['credenciais'] ?? [],
                    'configuracoes' => $padrao['configuracoes'] ?? [],
                ]
            );

            $credenciais = array_replace_recursive($padrao['credenciais'] ?? [], $gateway->credenciais ?? []);
            $configuracoes = array_replace_recursive($padrao['configuracoes'] ?? [], $gateway->configuracoes ?? []);

            if ($credenciais !== ($gateway->credenciais ?? []) || $configuracoes !== ($gateway->configuracoes ?? [])) {
                $gateway->forceFill([
                    'credenciais' => $credenciais,
                    'configuracoes' => $configuracoes,
                ])->save();
            }
        }
    }
}
