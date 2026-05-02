<?php

namespace App\Modules\Financeiro\Services;

use App\Modules\Financeiro\Models\ContaPagar;
use App\Modules\Financeiro\Models\ContaReceber;
use App\Modules\Financeiro\Models\Receita;
use App\Modules\Financeiro\Models\Despesa;
use App\Modules\Financeiro\Models\ContaBancaria;
use Carbon\Carbon;

/**
 * Agrega todos os KPIs do dashboard financeiro.
 */
class DashboardService
{
    public function __construct(private readonly int $userId) {}

    public function kpis(string $inicio, string $fim): array
    {
        $dtInicio = Carbon::parse($inicio)->startOfDay();
        $dtFim    = Carbon::parse($fim)->endOfDay();

        $totalRecebido  = $this->totalRecebido($dtInicio, $dtFim);
        $totalPago      = $this->totalPago($dtInicio, $dtFim);
        $totalReceitas  = $this->totalReceitas($dtInicio, $dtFim);
        $totalDespesas  = $this->totalDespesas($dtInicio, $dtFim);
        $saldoAtual     = $this->saldoAtual();
        $lucro          = $totalReceitas - $totalDespesas;

        $ticketMedioRec  = $this->ticketMedio('receitas', $dtInicio, $dtFim);
        $ticketMedioDesp = $this->ticketMedio('despesas', $dtInicio, $dtFim);

        $qtdPendentesReceber  = ContaReceber::doUsuario($this->userId)->whereBetween('data_vencimento', [$dtInicio, $dtFim])->where('status', 'pendente')->count();
        $totalPendenteReceber = ContaReceber::doUsuario($this->userId)->whereBetween('data_vencimento', [$dtInicio, $dtFim])->where('status', 'pendente')->sum('valor');

        return [
            // Saldos
            'saldo_atual'               => $saldoAtual,
            'saldo_atual_fmt'           => moeda_br($saldoAtual),
            'receita_total'             => $totalReceitas,
            'receita_total_fmt'         => moeda_br($totalReceitas),
            'despesa_total'             => $totalDespesas,
            'despesa_total_fmt'         => moeda_br($totalDespesas),
            'lucro'                     => $lucro,
            'lucro_fmt'                 => moeda_br($lucro),

            // Recebimentos/Pagamentos
            'total_recebido'            => $totalRecebido,
            'total_recebido_fmt'        => moeda_br($totalRecebido),
            'total_pago'                => $totalPago,
            'total_pago_fmt'            => moeda_br($totalPago),

            // Contas vencidas
            'cp_vencidas_qtd'           => ContaPagar::doUsuario($this->userId)->vencidas()->count(),
            'cp_vencidas_valor'         => moeda_br(ContaPagar::doUsuario($this->userId)->vencidas()->sum('valor')),
            'cr_vencidas_qtd'           => ContaReceber::doUsuario($this->userId)->vencidas()->count(),
            'cr_vencidas_valor'         => moeda_br(ContaReceber::doUsuario($this->userId)->vencidas()->sum('valor')),

            // Vencem hoje
            'vencendo_hoje_pagar'       => ContaPagar::doUsuario($this->userId)->vencendoHoje()->count(),
            'vencendo_hoje_receber'     => ContaReceber::doUsuario($this->userId)->vencendoHoje()->count(),

            // Vencem em 7 dias
            'vencendo_7dias_pagar'      => ContaPagar::doUsuario($this->userId)->vencendoEm(7)->count(),
            'vencendo_7dias_receber'    => ContaReceber::doUsuario($this->userId)->vencendoEm(7)->count(),

            // Pendentes
            'total_pendente'            => $totalPendenteReceber,
            'total_pendente_fmt'        => moeda_br($totalPendenteReceber),
            'qtd_pendente_receber'      => $qtdPendentesReceber,

            // Tickets médios
            'ticket_medio_receita'      => moeda_br($ticketMedioRec),
            'ticket_medio_despesa'      => moeda_br($ticketMedioDesp),

            // Percentuais
            'percentual_economia'       => $totalReceitas > 0
                ? percentual((($totalReceitas - $totalDespesas) / $totalReceitas) * 100)
                : '0,00%',
            'comprometimento_renda'     => $totalReceitas > 0
                ? percentual(($totalDespesas / $totalReceitas) * 100)
                : '0,00%',

            // Inadimplência
            'taxa_inadimplencia'        => $this->taxaInadimplencia($dtInicio, $dtFim),

            // Gráficos
            'evolucao_mensal'           => $this->evolucaoMensal(),
            'fluxo_realizado'           => $this->fluxoCaixaRealizado($dtInicio, $dtFim),
            'categoria_maior_gasto'     => $this->categoriaMaiorGasto($dtInicio, $dtFim),
            'categoria_maior_receita'   => $this->categoriaMaiorReceita($dtInicio, $dtFim),
            'melhor_mes'                => $this->melhorMes(),
            'pior_mes'                  => $this->piorMes(),
        ];
    }

    private function saldoAtual(): float
    {
        return (float) ContaBancaria::doUsuario($this->userId)
            ->where('incluir_no_total', true)->where('ativo', true)->sum('saldo_atual');
    }

    private function totalReceitas(Carbon $inicio, Carbon $fim): float
    {
        return (float) Receita::doUsuario($this->userId)
            ->whereBetween('data_receita', [$inicio, $fim])->sum('valor');
    }

    private function totalDespesas(Carbon $inicio, Carbon $fim): float
    {
        return (float) Despesa::doUsuario($this->userId)
            ->whereBetween('data_despesa', [$inicio, $fim])->sum('valor');
    }

    private function totalRecebido(Carbon $inicio, Carbon $fim): float
    {
        return (float) ContaReceber::doUsuario($this->userId)
            ->whereBetween('data_recebimento', [$inicio, $fim])
            ->where('status', 'recebido')->sum('valor_recebido');
    }

    private function totalPago(Carbon $inicio, Carbon $fim): float
    {
        return (float) ContaPagar::doUsuario($this->userId)
            ->whereBetween('data_pagamento', [$inicio, $fim])
            ->where('status', 'pago')->sum('valor_pago');
    }

    private function ticketMedio(string $tipo, Carbon $inicio, Carbon $fim): float
    {
        if ($tipo === 'receitas') {
            return (float) Receita::doUsuario($this->userId)
                ->whereBetween('data_receita', [$inicio, $fim])->avg('valor') ?? 0;
        }
        return (float) Despesa::doUsuario($this->userId)
            ->whereBetween('data_despesa', [$inicio, $fim])->avg('valor') ?? 0;
    }

    private function taxaInadimplencia(Carbon $inicio, Carbon $fim): string
    {
        $total    = ContaReceber::doUsuario($this->userId)->whereBetween('data_vencimento', [$inicio, $fim])->count();
        $vencidas = ContaReceber::doUsuario($this->userId)->whereBetween('data_vencimento', [$inicio, $fim])->where('status', 'vencido')->count();
        return $total > 0 ? percentual(($vencidas / $total) * 100) : '0,00%';
    }

    private function evolucaoMensal(): array
    {
        $resultado = [];
        for ($i = 11; $i >= 0; $i--) {
            $data   = now()->subMonths($i);
            $inicio = $data->copy()->startOfMonth();
            $fim    = $data->copy()->endOfMonth();
            $rec    = (float) Receita::doUsuario($this->userId)->whereBetween('data_receita', [$inicio, $fim])->sum('valor');
            $desp   = (float) Despesa::doUsuario($this->userId)->whereBetween('data_despesa', [$inicio, $fim])->sum('valor');
            $resultado[] = [
                'mes'      => $data->format('M/y'),
                'receita'  => $rec,
                'despesa'  => $desp,
                'saldo'    => $rec - $desp,
            ];
        }
        return $resultado;
    }

    private function fluxoCaixaRealizado(Carbon $inicio, Carbon $fim): array
    {
        $dias = [];
        $current = $inicio->copy();
        while ($current <= $fim && count($dias) < 60) {
            $rec  = (float) Receita::doUsuario($this->userId)->whereDate('data_receita', $current)->sum('valor');
            $desp = (float) Despesa::doUsuario($this->userId)->whereDate('data_despesa', $current)->sum('valor');
            if ($rec > 0 || $desp > 0) {
                $dias[] = ['data' => $current->format('d/m'), 'entrada' => $rec, 'saida' => $desp];
            }
            $current->addDay();
        }
        return $dias;
    }

    private function categoriaMaiorGasto(Carbon $inicio, Carbon $fim): array
    {
        return Despesa::doUsuario($this->userId)
            ->with('categoria:id,nome')
            ->whereBetween('data_despesa', [$inicio, $fim])
            ->selectRaw('categoria_id, SUM(valor) as total')
            ->groupBy('categoria_id')
            ->orderByDesc('total')
            ->limit(5)
            ->get()
            ->map(fn ($d) => ['categoria' => $d->categoria?->nome ?? 'Sem categoria', 'total' => $d->total])
            ->toArray();
    }

    private function categoriaMaiorReceita(Carbon $inicio, Carbon $fim): array
    {
        return Receita::doUsuario($this->userId)
            ->with('categoria:id,nome')
            ->whereBetween('data_receita', [$inicio, $fim])
            ->selectRaw('categoria_id, SUM(valor) as total')
            ->groupBy('categoria_id')
            ->orderByDesc('total')
            ->limit(5)
            ->get()
            ->map(fn ($r) => ['categoria' => $r->categoria?->nome ?? 'Sem categoria', 'total' => $r->total])
            ->toArray();
    }

    private function melhorMes(): array
    {
        $meses = $this->evolucaoMensal();
        return collect($meses)->sortByDesc('saldo')->first() ?? [];
    }

    private function piorMes(): array
    {
        $meses = $this->evolucaoMensal();
        return collect($meses)->sortBy('saldo')->first() ?? [];
    }
}
