<?php

namespace App\Modules\Integracoes\Services;

use App\Modules\Integracoes\Models\GatewayPagamento;
use App\Modules\Saas\Models\Fatura;
use Illuminate\Http\Client\Response;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;

class MercadoPagoOrderService
{
    private string $baseUrl = 'https://api.mercadopago.com';

    public function criarCobrancaFatura(Fatura $fatura, string $metodo, array $dados = []): array
    {
        $gateway = $this->gatewayAtivo();
        $payload = $this->montarPayload($gateway, $fatura->loadMissing('empresa'), $metodo, $dados);
        $resposta = $this->postOrder($gateway, $payload, $this->idempotencyKey($fatura, $metodo, $payload));
        $json = $resposta->json();

        $pagamento = data_get($json, 'transactions.payments.0', []);
        $forma = data_get($pagamento, 'payment_method', []);
        $status = (string) data_get($json, 'status', data_get($pagamento, 'status', ''));

        $fatura->update([
            'gateway' => 'mercadopago',
            'gateway_ref' => data_get($json, 'id'),
            'link_pagamento' => data_get($forma, 'ticket_url', $fatura->link_pagamento),
            'pix_copia_e_cola' => $metodo === 'pix' ? data_get($forma, 'qr_code', $fatura->pix_copia_e_cola) : $fatura->pix_copia_e_cola,
            'boleto_linha_digitavel' => $metodo === 'boleto'
                ? data_get($pagamento, 'payment_digitable_line', data_get($forma, 'barcode_content', $fatura->boleto_linha_digitavel))
                : $fatura->boleto_linha_digitavel,
            'status' => in_array($status, ['processed', 'paid', 'approved', 'accredited'], true) ? 'paga' : $fatura->status,
            'pago_em' => in_array($status, ['processed', 'paid', 'approved', 'accredited'], true) ? now() : $fatura->pago_em,
        ]);

        return [
            'order_id' => data_get($json, 'id'),
            'status' => $status,
            'status_detail' => data_get($json, 'status_detail', data_get($pagamento, 'status_detail')),
            'metodo' => $metodo,
            'link_pagamento' => data_get($forma, 'ticket_url'),
            'pix_copia_e_cola' => data_get($forma, 'qr_code'),
            'pix_qr_code_base64' => data_get($forma, 'qr_code_base64'),
            'boleto_linha_digitavel' => data_get($pagamento, 'payment_digitable_line', data_get($forma, 'barcode_content')),
            'resposta' => Arr::except($json, ['collector', 'metadata']),
        ];
    }

    public function testarCredenciais(GatewayPagamento $gateway): array
    {
        $token = trim((string) $gateway->credential('access_token'));

        if ($token === '') {
            throw ValidationException::withMessages([
                'credenciais.access_token' => 'Informe o Access Token do Mercado Pago antes de testar.',
            ]);
        }

        $resposta = Http::timeout(20)
            ->acceptJson()
            ->withToken($token)
            ->get($this->baseUrl.'/v1/payment_methods');

        $this->validarResposta($resposta, 'Nao foi possivel validar as credenciais do Mercado Pago.');

        return [
            'total_meios' => count($resposta->json() ?? []),
            'sandbox' => $gateway->sandbox,
        ];
    }

    private function gatewayAtivo(): GatewayPagamento
    {
        $gateway = GatewayPagamento::query()
            ->where('identificador', 'mercadopago')
            ->where('ativo', true)
            ->first();

        if (! $gateway) {
            throw ValidationException::withMessages([
                'gateway' => 'Ative e configure o gateway Mercado Pago antes de gerar cobrancas.',
            ]);
        }

        if (trim((string) $gateway->credential('access_token')) === '') {
            throw ValidationException::withMessages([
                'gateway' => 'Informe o Access Token no gateway Mercado Pago.',
            ]);
        }

        return $gateway;
    }

    private function montarPayload(GatewayPagamento $gateway, Fatura $fatura, string $metodo, array $dados): array
    {
        $this->garantirMetodoAtivo($gateway, $metodo);

        $valor = number_format((float) $fatura->valor, 2, '.', '');
        $empresa = $fatura->empresa;

        if (! $empresa) {
            throw ValidationException::withMessages(['empresa_id' => 'A fatura precisa estar vinculada a uma empresa.']);
        }

        $email = trim((string) (($dados['payer_email'] ?? null) ?: $empresa->email));
        if ($email === '') {
            throw ValidationException::withMessages(['payer_email' => 'Informe o e-mail do pagador antes de gerar a cobranca.']);
        }

        $payload = [
            'type' => 'online',
            'total_amount' => $valor,
            'external_reference' => 'saas_fatura_'.$fatura->id,
            'processing_mode' => $gateway->configuration('processing_mode', 'automatic'),
            'payer' => [
                'email' => $email,
            ],
            'transactions' => [
                'payments' => [
                    [
                        'amount' => $valor,
                        'payment_method' => $this->paymentMethod($metodo, $dados),
                    ],
                ],
            ],
        ];

        if ($metodo === 'pix') {
            $payload['transactions']['payments'][0]['expiration_time'] = $gateway->configuration('pix_expiration_time', 'P1D');
        }

        if ($metodo === 'boleto') {
            $payload['payer'] = array_merge($payload['payer'], $this->payerBoleto($empresa));
            $payload['transactions']['payments'][0]['expiration_time'] = $gateway->configuration('boleto_expiration_time', 'P3D');
        }

        return $payload;
    }

    private function garantirMetodoAtivo(GatewayPagamento $gateway, string $metodo): void
    {
        $chave = match ($metodo) {
            'pix' => 'pix_ativo',
            'boleto' => 'boleto_ativo',
            'cartao_credito' => 'cartao_credito_ativo',
            'cartao_debito' => 'cartao_debito_ativo',
            default => null,
        };

        if ($chave && in_array($gateway->configuration($chave, true), [false, 0, '0'], true)) {
            throw ValidationException::withMessages([
                'metodo' => 'Este metodo de pagamento esta desativado no gateway Mercado Pago.',
            ]);
        }
    }

    private function paymentMethod(string $metodo, array $dados): array
    {
        return match ($metodo) {
            'pix' => [
                'id' => 'pix',
                'type' => 'bank_transfer',
            ],
            'boleto' => [
                'id' => 'boleto',
                'type' => 'ticket',
            ],
            'cartao_credito', 'cartao_debito' => $this->paymentMethodCartao($metodo, $dados),
            default => throw ValidationException::withMessages(['metodo' => 'Metodo de pagamento invalido.']),
        };
    }

    private function paymentMethodCartao(string $metodo, array $dados): array
    {
        $token = trim((string) ($dados['token'] ?? ''));
        $bandeira = trim((string) ($dados['payment_method_id'] ?? ''));

        if ($token === '' || $bandeira === '') {
            throw ValidationException::withMessages([
                'token' => 'Para cartao, envie o token gerado pelo MercadoPago.js/Card Payment Brick e a bandeira.',
            ]);
        }

        return [
            'id' => $bandeira,
            'type' => $metodo === 'cartao_credito' ? 'credit_card' : 'debit_card',
            'token' => $token,
            'installments' => max(1, (int) ($dados['installments'] ?? 1)),
        ];
    }

    private function payerBoleto(object $empresa): array
    {
        $documento = limpar_numero($empresa->cnpj);

        if ($documento === '' || ! in_array(strlen($documento), [11, 14], true)) {
            throw ValidationException::withMessages([
                'documento' => 'Para boleto, informe CPF ou CNPJ valido no cadastro da empresa.',
            ]);
        }

        foreach (['cep', 'logradouro', 'bairro', 'cidade', 'estado'] as $campo) {
            if (trim((string) $empresa->{$campo}) === '') {
                throw ValidationException::withMessages([
                    $campo => 'Para boleto, preencha o endereco completo da empresa.',
                ]);
            }
        }

        $nome = trim((string) ($empresa->razao_social ?: $empresa->nome_fantasia));
        [$primeiroNome, $sobrenome] = $this->separarNome($nome);

        return [
            'first_name' => $primeiroNome,
            'last_name' => $sobrenome,
            'identification' => [
                'type' => strlen($documento) === 14 ? 'CNPJ' : 'CPF',
                'number' => $documento,
            ],
            'address' => [
                'street_name' => (string) $empresa->logradouro,
                'street_number' => trim((string) $empresa->numero) ?: 'S/N',
                'zip_code' => limpar_numero($empresa->cep),
                'neighborhood' => (string) $empresa->bairro,
                'state' => strtoupper(substr((string) $empresa->estado, 0, 2)),
                'city' => (string) $empresa->cidade,
            ],
        ];
    }

    private function separarNome(string $nome): array
    {
        $partes = preg_split('/\s+/', trim($nome)) ?: [];
        $primeiro = array_shift($partes) ?: 'Cliente';
        $sobrenome = trim(implode(' ', $partes)) ?: 'SaaS';

        return [$primeiro, $sobrenome];
    }

    private function postOrder(GatewayPagamento $gateway, array $payload, string $idempotencyKey): Response
    {
        $resposta = Http::timeout(30)
            ->acceptJson()
            ->asJson()
            ->withToken((string) $gateway->credential('access_token'))
            ->withHeaders([
                'X-Idempotency-Key' => $idempotencyKey,
            ])
            ->post($this->baseUrl.'/v1/orders', $payload);

        $this->validarResposta($resposta, 'Nao foi possivel criar a order no Mercado Pago.');

        return $resposta;
    }

    private function validarResposta(Response $resposta, string $mensagemPadrao): void
    {
        if ($resposta->successful()) {
            return;
        }

        $mensagem = data_get($resposta->json(), 'message')
            ?: data_get($resposta->json(), 'error')
            ?: $mensagemPadrao;

        throw ValidationException::withMessages([
            'mercadopago' => $mensagem,
        ]);
    }

    private function idempotencyKey(Fatura $fatura, string $metodo, array $payload): string
    {
        $base = sprintf('saas-fatura-%s-%s-%s', $fatura->id, $metodo, md5(json_encode($payload)));

        return Str::limit($base, 64, '');
    }
}
