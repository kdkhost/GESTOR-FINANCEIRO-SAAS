<?php

namespace App\Services;

use App\Modules\Notificacoes\Models\TemplateNotificacao;
use App\Modules\Saas\Models\Fatura;
use App\Modules\Saas\Models\Empresa;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class EmailService
{
    /**
     * Envia email usando template configurável
     */
    public function enviarComTemplate(string $chave, string $destinatario, array $dados, array $anexos = []): bool
    {
        $template = TemplateNotificacao::where('canal', 'email')
            ->where('chave', $chave)
            ->where('ativo', true)
            ->first();

        if (!$template) {
            Log::warning("Template de email não encontrado: {$chave}");
            return false;
        }

        // Substitui variáveis no assunto e conteúdo
        $assunto = $this->substituirVariaveis($template->assunto ?? '', $dados);
        $conteudo = $this->substituirVariaveis($template->conteudo, $dados);

        try {
            Mail::raw($conteudo, function ($message) use ($destinatario, $assunto, $anexos) {
                $message->to($destinatario)
                    ->subject($assunto);

                foreach ($anexos as $anexo) {
                    $message->attach($anexo['path'], [
                        'as' => $anexo['nome'] ?? basename($anexo['path']),
                        'mime' => $anexo['mime'] ?? 'application/pdf',
                    ]);
                }
            });

            return true;
        } catch (\Exception $e) {
            Log::error("Erro ao enviar email: {$e->getMessage()}");
            return false;
        }
    }

    /**
     * Substitui variáveis no template
     */
    private function substituirVariaveis(string $texto, array $dados): string
    {
        foreach ($dados as $chave => $valor) {
            $texto = str_replace("{{$chave}}", $valor, $texto);
        }
        return $texto;
    }

    /**
     * Envia email de cobrança com fatura anexada
     */
    public function enviarCobranca(Fatura $fatura): bool
    {
        $empresa = $fatura->empresa;
        
        $dados = [
            'nome_empresa' => $empresa->nome_fantasia,
            'numero_fatura' => $fatura->id,
            'competencia' => $fatura->competencia,
            'valor' => number_format($fatura->valor, 2, ',', '.'),
            'vencimento' => $fatura->vencimento_em ? $fatura->vencimento_em->format('d/m/Y') : '-',
            'link_pagamento' => $fatura->link_pagamento ?? '#',
            'pix_copia_e_cola' => $fatura->pix_copia_e_cola ?? '-',
        ];

        // Gera PDF da fatura (simulado - implementar geração real)
        $pdfPath = $this->gerarPdfFatura($fatura);
        $anexos = $pdfPath ? [['path' => $pdfPath, 'nome' => "fatura_{$fatura->id}.pdf", 'mime' => 'application/pdf']] : [];

        return $this->enviarComTemplate('cobranca_mensal', $empresa->email, $dados, $anexos);
    }

    /**
     * Gera PDF da fatura (implementação básica)
     */
    private function gerarPdfFatura(Fatura $fatura): ?string
    {
        // TODO: Implementar geração real de PDF com dompdf/snappy
        // Por enquanto, retorna null (sem anexo)
        return null;
    }

    /**
     * Retorna dados de exemplo para preview do template
     */
    public function getDadosPreview(string $chave): array
    {
        $dadosExemplo = [
            'cobranca_mensal' => [
                'nome_empresa' => 'Empresa Exemplo LTDA',
                'numero_fatura' => '123',
                'competencia' => '05/2026',
                'valor' => '99,90',
                'vencimento' => '10/06/2026',
                'link_pagamento' => 'https://exemplo.com/pagar/123',
                'pix_copia_e_cola' => '00020126580014BR.GOV.BCB.PIX0136123e4567-e89b-12d3-a456-426614174000520400005303986540599.905802BR5913Empresa Exemplo6008Sao Paulo62070503***6304ABCD',
            ],
            'fatura_vencida' => [
                'nome_empresa' => 'Empresa Exemplo LTDA',
                'numero_fatura' => '123',
                'competencia' => '05/2026',
                'valor' => '99,90',
                'vencimento' => '10/05/2026',
                'dias_atraso' => '5',
            ],
            'boas_vindas' => [
                'nome_empresa' => 'Empresa Exemplo LTDA',
                'nome_responsavel' => 'João Silva',
                'email' => 'joao@empresaexemplo.com',
            ],
        ];

        return $dadosExemplo[$chave] ?? [];
    }
}
