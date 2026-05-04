<?php

namespace Tests\Feature;

use App\Modules\Integracoes\Models\GatewayPagamento;
use App\Modules\Integracoes\Services\MercadoPagoOrderService;
use App\Modules\Saas\Models\Empresa;
use App\Modules\Saas\Models\Fatura;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Http;
use Illuminate\Validation\ValidationException;
use Tests\TestCase;

class MercadoPagoOrderServiceTest extends TestCase
{
    use RefreshDatabase;

    public function test_cria_cobranca_pix_e_atualiza_fatura(): void
    {
        $this->gatewayMercadoPago();
        $fatura = $this->fatura();

        Http::fake([
            'api.mercadopago.com/v1/orders' => Http::response([
                'id' => 'ORD-PIX-1',
                'status' => 'action_required',
                'status_detail' => 'waiting_transfer',
                'transactions' => [
                    'payments' => [[
                        'status' => 'action_required',
                        'payment_method' => [
                            'id' => 'pix',
                            'type' => 'bank_transfer',
                            'ticket_url' => 'https://www.mercadopago.com.br/payments/123/ticket',
                            'qr_code' => '000201PIX',
                            'qr_code_base64' => 'base64pix',
                        ],
                    ]],
                ],
            ], 201),
        ]);

        $resultado = app(MercadoPagoOrderService::class)->criarCobrancaFatura($fatura, 'pix');

        Http::assertSent(function ($request) {
            return $request->url() === 'https://api.mercadopago.com/v1/orders'
                && $request->hasHeader('Authorization', 'Bearer TEST-123')
                && $request->hasHeader('X-Idempotency-Key')
                && data_get($request->data(), 'transactions.payments.0.payment_method.id') === 'pix'
                && data_get($request->data(), 'transactions.payments.0.payment_method.type') === 'bank_transfer';
        });

        $this->assertSame('ORD-PIX-1', $resultado['order_id']);
        $this->assertSame('000201PIX', $fatura->fresh()->pix_copia_e_cola);
        $this->assertSame('mercadopago', $fatura->fresh()->gateway);
    }

    public function test_cria_cobranca_boleto_com_pagador_e_endereco(): void
    {
        $this->gatewayMercadoPago();
        $fatura = $this->fatura();

        Http::fake([
            'api.mercadopago.com/v1/orders' => Http::response([
                'id' => 'ORD-BOL-1',
                'status' => 'action_required',
                'transactions' => [
                    'payments' => [[
                        'payment_digitable_line' => '23790000000000000000000000000000000000000000',
                        'payment_method' => [
                            'id' => 'boleto',
                            'type' => 'ticket',
                            'ticket_url' => 'https://www.mercadopago.com.br/payments/456/ticket',
                        ],
                    ]],
                ],
            ], 201),
        ]);

        app(MercadoPagoOrderService::class)->criarCobrancaFatura($fatura, 'boleto');

        Http::assertSent(function ($request) {
            $payload = $request->data();

            return data_get($payload, 'payer.identification.type') === 'CNPJ'
                && data_get($payload, 'payer.address.zip_code') === '06233903'
                && data_get($payload, 'transactions.payments.0.payment_method.id') === 'boleto'
                && data_get($payload, 'transactions.payments.0.expiration_time') === 'P3D';
        });

        $this->assertSame('23790000000000000000000000000000000000000000', $fatura->fresh()->boleto_linha_digitavel);
        $this->assertSame('ORD-BOL-1', $fatura->fresh()->gateway_ref);
    }

    public function test_cartao_exige_token_do_mercado_pago_js(): void
    {
        $this->gatewayMercadoPago();

        $this->expectException(ValidationException::class);

        app(MercadoPagoOrderService::class)->criarCobrancaFatura($this->fatura(), 'cartao_credito');
    }

    private function gatewayMercadoPago(): GatewayPagamento
    {
        return GatewayPagamento::create([
            'nome' => 'Mercado Pago',
            'identificador' => 'mercadopago',
            'ativo' => true,
            'sandbox' => true,
            'credenciais' => [
                'access_token' => 'TEST-123',
                'public_key' => 'APP_USR-123',
            ],
            'configuracoes' => [
                'processing_mode' => 'automatic',
                'pix_expiration_time' => 'P1D',
                'boleto_expiration_time' => 'P3D',
            ],
        ]);
    }

    private function fatura(): Fatura
    {
        $empresa = Empresa::create([
            'nome_fantasia' => 'Cliente SaaS',
            'razao_social' => 'Cliente SaaS Ltda',
            'cnpj' => '10.573.521/0001-91',
            'email' => 'financeiro@cliente.test',
            'telefone' => '(11) 99999-9999',
            'cep' => '06233-903',
            'logradouro' => 'Av. das Nacoes Unidas',
            'numero' => '3003',
            'bairro' => 'Bonfim',
            'cidade' => 'Osasco',
            'estado' => 'SP',
            'status' => 'ativo',
        ]);

        return Fatura::create([
            'empresa_id' => $empresa->id,
            'status' => 'aberta',
            'competencia' => '2026-05',
            'valor' => 200.00,
            'vencimento_em' => now()->addDays(3),
        ]);
    }
}
