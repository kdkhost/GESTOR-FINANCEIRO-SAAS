<?php

namespace App\Modules\Financeiro\Services;

use App\Modules\Financeiro\Models\ContaPagar;
use App\Modules\Financeiro\Models\ContaReceber;
use App\Modules\Financeiro\Models\Receita;
use App\Modules\Financeiro\Models\Despesa;
use App\Modules\Financeiro\Models\ContaBancaria;
use App\Modules\Financeiro\Models\MetaFinanceira;
use Carbon\Carbon;

/**
 * Calcula o índice de saúde financeira do usuário (0-100).
 * Metodologia:
 *   Pontualidade de pagamentos  => 25 pts
 *   Equilíbrio receita/despesa  => 25 pts
 *   Reserva financeira          => 20 pts
 *   Dívidas em aberto           => 15 pts
 *   Evolução mensal             => 10 pts
 *   Cumprimento de metas        =>  5 pts
 */
class SaudeFinanceiraService
{
    public function __construct(private readonly int $userId) {}

    public function calcular(int $mes = 0, int $ano = 0): array
    {
        $mes = $mes ?: now()->month;
        $ano = $ano ?: now()->year;
        $inicio = Carbon::create($ano, $mes, 1)->startOfMonth();
        $fim    = Carbon::create($ano, $mes, 1)->endOfMonth();

        $fatores = [
            'pontualidade' => $this->calcularPontualidade($inicio, $fim),
            'equilibrio'   => $this->calcularEquilibrio($inicio, $fim),
            'reserva'      => $this->calcularReserva(),
            'dividas'      => $this->calcularDividas(),
            'evolucao'     => $this->calcularEvolucao($mes, $ano),
            'metas'        => $this->calcularMetas(),
        ];

        $indice = max(0, min(100, (int) array_sum(array_column($fatores, 'pontos'))));

        return [
            'indice'        => $indice,
            'classificacao' => sigla_status_saude($indice),
            'cor'           => cor_status_saude($indice),
            'fatores'       => $fatores,
            'recomendacoes' => $this->gerarRecomendacoes($fatores, $indice),
            'mes'           => $mes,
            'ano'           => $ano,
        ];
    }

    private function calcularPontualidade(Carbon $inicio, Carbon $fim): array
    {
        $total = ContaPagar::doUsuario($this->userId)->whereBetween('data_vencimento', [$inicio, $fim])->count();
        $pagas = ContaPagar::doUsuario($this->userId)->whereBetween('data_vencimento', [$inicio, $fim])
            ->where('status', 'pago')->whereColumn('data_pagamento', '<=', 'data_vencimento')->count();
        $taxa   = $total > 0 ? ($pagas / $total) * 100 : 100;
        $pontos = $total > 0 ? (int) round(($taxa / 100) * 25) : 25;
        return ['nome' => 'Pontualidade', 'pontos' => $pontos, 'maximo' => 25, 'taxa' => round($taxa, 1),
            'detalhes' => "{$pagas} de {$total} contas pagas no prazo"];
    }

    private function calcularEquilibrio(Carbon $inicio, Carbon $fim): array
    {
        $rec  = (float) Receita::doUsuario($this->userId)->whereBetween('data_receita', [$inicio, $fim])->sum('valor');
        $desp = (float) Despesa::doUsuario($this->userId)->whereBetween('data_despesa', [$inicio, $fim])->sum('valor');
        if ($rec <= 0) { $pontos = 0; $perc = 0; }
        else {
            $ratio  = $desp / $rec;
            $perc   = round($ratio * 100, 1);
            $pontos = match(true) {
                $ratio <= 0.50 => 25, $ratio <= 0.70 => 20,
                $ratio <= 0.85 => 15, $ratio <= 1.00 => 8, default => 0,
            };
        }
        return ['nome' => 'Equilíbrio', 'pontos' => $pontos, 'maximo' => 25,
            'receitas' => $rec, 'despesas' => $desp, 'taxa' => $perc ?? 0,
            'detalhes' => "Comprometimento: {$perc}%"];
    }

    private function calcularReserva(): array
    {
        $saldo = (float) ContaBancaria::doUsuario($this->userId)->where('incluir_no_total', true)->where('ativo', true)->sum('saldo_atual');
        $media = (float) (Despesa::doUsuario($this->userId)->where('data_despesa', '>=', now()->subMonths(3))->avg('valor') ?? 0);
        $mediaMensal = max($media * 30, 1);
        $meses = $saldo / $mediaMensal;
        $pontos = match(true) { $meses >= 6 => 20, $meses >= 3 => 15, $meses >= 1 => 10, $meses >= 0 => 5, default => 0 };
        return ['nome' => 'Reserva', 'pontos' => $pontos, 'maximo' => 20,
            'saldo_total' => $saldo, 'meses_reserva' => round($meses, 1),
            'detalhes' => round($meses, 1) . ' meses de reserva'];
    }

    private function calcularDividas(): array
    {
        $vencidas  = (float) ContaPagar::doUsuario($this->userId)->where('status', 'vencido')->sum('valor');
        $pendentes = (float) ContaPagar::doUsuario($this->userId)->where('status', 'pendente')->sum('valor');
        $pontos = match(true) {
            $vencidas <= 0 && $pendentes <= 0 => 15, $vencidas <= 0 => 12,
            $vencidas <= 500 => 8, $vencidas <= 2000 => 4, default => 0,
        };
        return ['nome' => 'Dívidas', 'pontos' => $pontos, 'maximo' => 15,
            'total_vencidas' => $vencidas, 'total_pendentes' => $pendentes,
            'detalhes' => moeda_br($vencidas) . ' em contas vencidas'];
    }

    private function calcularEvolucao(int $mes, int $ano): array
    {
        $atual    = $this->saldoLiquidoMes($mes, $ano);
        $anterior = $this->saldoLiquidoMes($mes === 1 ? 12 : $mes - 1, $mes === 1 ? $ano - 1 : $ano);
        $pontos = match(true) {
            $atual > $anterior && $atual > 0 => 10, $atual > 0 => 7,
            $atual == $anterior => 5, $atual > $anterior => 3, default => 0,
        };
        return ['nome' => 'Evolução', 'pontos' => $pontos, 'maximo' => 10,
            'mes_atual' => $atual, 'mes_anterior' => $anterior,
            'detalhes' => 'Resultado líquido: ' . moeda_br($atual)];
    }

    private function calcularMetas(): array
    {
        $metas  = MetaFinanceira::where('user_id', $this->userId)->where('status', 'ativa')->get();
        $emDia  = $metas->filter(fn ($m) => ($m->valor_atual / max(1, $m->valor_alvo)) >= 0.5)->count();
        $total  = $metas->count();
        $pontos = $total > 0 ? (int) round(($emDia / $total) * 5) : 5;
        return ['nome' => 'Metas', 'pontos' => $pontos, 'maximo' => 5,
            'em_dia' => $emDia, 'total' => $total,
            'detalhes' => "{$emDia}/{$total} metas no caminho certo"];
    }

    private function gerarRecomendacoes(array $fatores, int $indice): array
    {
        $rec = [];
        if ($fatores['pontualidade']['pontos'] < 20) {
            $rec[] = 'Quite suas contas em atraso para melhorar a pontualidade.';
        }
        if ($fatores['equilibrio']['taxa'] > 80) {
            $rec[] = 'Você compromete mais de 80% da renda. Revise seus gastos.';
        }
        if ($fatores['reserva']['meses_reserva'] < 3) {
            $rec[] = 'Construa uma reserva de emergência de pelo menos 3 meses.';
        }
        if ($fatores['dividas']['total_vencidas'] > 0) {
            $rec[] = 'Priorize o pagamento de contas vencidas para evitar juros.';
        }
        if ($fatores['evolucao']['mes_atual'] < 0) {
            $rec[] = 'Resultado negativo este mês. Analise onde pode cortar gastos.';
        }
        if ($fatores['metas']['total'] === 0) {
            $rec[] = 'Defina metas financeiras para manter o foco nos objetivos.';
        }
        if ($indice >= 80) {
            $rec[] = 'Ótima saúde financeira! Considere investir o excedente.';
        }
        return $rec;
    }

    private function saldoLiquidoMes(int $mes, int $ano): float
    {
        $mes = max(1, min(12, $mes));
        $inicio = Carbon::create($ano, $mes, 1)->startOfMonth();
        $fim    = Carbon::create($ano, $mes, 1)->endOfMonth();
        $rec  = (float) Receita::doUsuario($this->userId)->whereBetween('data_receita', [$inicio, $fim])->sum('valor');
        $desp = (float) Despesa::doUsuario($this->userId)->whereBetween('data_despesa', [$inicio, $fim])->sum('valor');
        return $rec - $desp;
    }
}
