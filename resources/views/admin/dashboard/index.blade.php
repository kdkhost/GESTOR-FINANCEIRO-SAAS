@extends('layouts.admin.app')

@section('titulo', 'Dashboard Financeiro')
@section('titulo_pagina', 'Dashboard Financeiro')

@section('breadcrumb')
    <li class="breadcrumb-item active">Dashboard</li>
@endsection

@push('styles')
<style>
.kpi-card { transition: transform .2s ease, box-shadow .2s ease; border-left-width: 4px; border-left-style: solid; cursor: default; }
.kpi-card:hover { transform: translateY(-3px); box-shadow: 0 4px 12px rgba(15,23,42,.1); }
.kpi-card .card-body { padding: .75rem .875rem; }
.kpi-icon { width: 1.75rem; height: 1.75rem; border-radius: .5rem; display: flex; align-items: center; justify-content: center; font-size: .875rem; }
.kpi-valor { font-size: 1.05rem; font-weight: 700; line-height: 1.2; }
.kpi-label { font-size: .65rem; text-transform: uppercase; letter-spacing: .04em; opacity: .75; margin-top: .25rem; }
.saude-gauge { min-height: 160px; display: flex; align-items: center; justify-content: center; flex-direction: column; }
.saude-indice { font-size: 2.2rem; font-weight: 800; line-height: 1; }
.filtro-periodo .btn { border-radius: 999px; padding: .25rem .75rem; font-size: .8rem; margin: 0 .1rem; }
.recomendacao-item { border-left: 3px solid #3b82f6; padding: .5rem .75rem; margin-bottom: .375rem; background: #eff6ff; border-radius: 0 .75rem .75rem 0; font-size: .8rem; }
.chart-placeholder { min-height: 200px; display: none; align-items: center; justify-content: center; color: #6b7280; font-weight: 600; }
.card-standard { border-radius: .75rem; }
.card-header { padding: .75rem 1rem; }
.card-title { font-size: .95rem; font-weight: 600; }
.mini-kpi .card-body { padding: .625rem .75rem; }
.mini-kpi .fs-2 { font-size: 1.4rem !important; }
.mini-kpi .fs-3 { font-size: 1.25rem !important; }
</style>
@endpush

@section('conteudo')

{{-- Filtros de Período --}}
<div class="row mb-3">
    <div class="col-12">
        <div class="card card-outline card-secondary card-standard">
            <div class="card-body py-2">
                <div class="d-flex align-items-center flex-wrap gap-2">
                    <span class="fw-semibold me-2 text-muted small">PERÍODO:</span>
                    <div class="filtro-periodo btn-group" role="group" id="filtros-periodo">
                        <button type="button" class="btn btn-sm btn-outline-primary" data-periodo="hoje">Hoje</button>
                        <button type="button" class="btn btn-sm btn-outline-primary" data-periodo="semana">Semana</button>
                        <button type="button" class="btn btn-sm btn-outline-primary active" data-periodo="mes">Mês</button>
                        <button type="button" class="btn btn-sm btn-outline-primary" data-periodo="trimestre">Trimestre</button>
                        <button type="button" class="btn btn-sm btn-outline-primary" data-periodo="semestre">Semestre</button>
                        <button type="button" class="btn btn-sm btn-outline-primary" data-periodo="ano">Ano</button>
                    </div>
                    <div class="d-flex align-items-center gap-2 ms-2">
                        <input type="text" id="periodo-inicio" class="form-control form-control-sm mask-data" placeholder="dd/mm/aaaa" style="width:130px;">
                        <span class="text-muted">até</span>
                        <input type="text" id="periodo-fim" class="form-control form-control-sm mask-data" placeholder="dd/mm/aaaa" style="width:130px;">
                        <button class="btn btn-sm btn-primary" id="btn-periodo-custom">
                            <i class="bi bi-search me-1"></i>Aplicar
                        </button>
                    </div>
                    <span class="ms-auto text-muted small" id="periodo-label">
                        <i class="bi bi-calendar3 me-1"></i>Carregando...
                    </span>
                </div>
            </div>
        </div>
    </div>
</div>

{{-- KPIs Principais --}}
<div class="row g-3 mb-3" id="kpis-container">

    {{-- Saldo Atual --}}
    <div class="col-xl-3 col-md-6">
        <div class="card kpi-card card-standard h-100" style="border-color:#3b82f6;">
            <div class="card-body">
                <div class="d-flex align-items-center justify-content-between mb-2">
                    <div class="kpi-icon bg-primary bg-opacity-10 text-primary">
                        <i class="bi bi-wallet2"></i>
                    </div>
                    <span class="badge bg-primary-subtle text-primary small">Saldo</span>
                </div>
                <div class="kpi-valor text-primary" id="kpi-saldo-atual">R$ —</div>
                <div class="kpi-label mt-1">Saldo Atual</div>
            </div>
        </div>
    </div>

    {{-- Receita Total --}}
    <div class="col-xl-3 col-md-6">
        <div class="card kpi-card card-standard h-100" style="border-color:#22c55e;">
            <div class="card-body">
                <div class="d-flex align-items-center justify-content-between mb-2">
                    <div class="kpi-icon bg-success bg-opacity-10 text-success">
                        <i class="bi bi-arrow-down-circle"></i>
                    </div>
                    <span class="badge bg-success-subtle text-success small">Receitas</span>
                </div>
                <div class="kpi-valor text-success" id="kpi-receita-total">R$ —</div>
                <div class="kpi-label mt-1">Receita Total</div>
            </div>
        </div>
    </div>

    {{-- Despesa Total --}}
    <div class="col-xl-3 col-md-6">
        <div class="card kpi-card card-standard h-100" style="border-color:#ef4444;">
            <div class="card-body">
                <div class="d-flex align-items-center justify-content-between mb-2">
                    <div class="kpi-icon bg-danger bg-opacity-10 text-danger">
                        <i class="bi bi-arrow-up-circle"></i>
                    </div>
                    <span class="badge bg-danger-subtle text-danger small">Despesas</span>
                </div>
                <div class="kpi-valor text-danger" id="kpi-despesa-total">R$ —</div>
                <div class="kpi-label mt-1">Despesa Total</div>
            </div>
        </div>
    </div>

    {{-- Lucro / Prejuízo --}}
    <div class="col-xl-3 col-md-6">
        <div class="card kpi-card card-standard h-100" style="border-color:#8b5cf6;">
            <div class="card-body">
                <div class="d-flex align-items-center justify-content-between mb-2">
                    <div class="kpi-icon bg-purple bg-opacity-10" style="background:#ede9fe;color:#7c3aed;">
                        <i class="bi bi-graph-up-arrow"></i>
                    </div>
                    <span id="kpi-lucro-badge" class="badge small">Resultado</span>
                </div>
                <div class="kpi-valor" id="kpi-lucro">R$ —</div>
                <div class="kpi-label mt-1">Lucro / Prejuízo</div>
            </div>
        </div>
    </div>

</div>

{{-- Segunda linha de KPIs (mini) --}}
<div class="row gx-2 gy-3 mb-3">

    <div class="col-4 col-sm-4 col-md-2">
        <div class="card mini-kpi text-center border-0 shadow-sm card-standard h-100">
            <div class="card-body">
                <div class="fs-2 fw-bold text-danger" id="kpi-cp-vencidas-qtd">—</div>
                <div class="small text-muted">Vencidas</div>
                <div class="small text-danger fw-medium" id="kpi-cp-vencidas-valor">—</div>
            </div>
        </div>
    </div>

    <div class="col-4 col-sm-4 col-md-2">
        <div class="card mini-kpi text-center border-0 shadow-sm card-standard h-100">
            <div class="card-body">
                <div class="fs-2 fw-bold text-warning" id="kpi-vencendo-hoje">—</div>
                <div class="small text-muted">Hoje</div>
            </div>
        </div>
    </div>

    <div class="col-4 col-sm-4 col-md-2">
        <div class="card mini-kpi text-center border-0 shadow-sm card-standard h-100">
            <div class="card-body">
                <div class="fs-2 fw-bold text-info" id="kpi-vencendo-7dias">—</div>
                <div class="small text-muted">+7 dias</div>
            </div>
        </div>
    </div>

    <div class="col-4 col-sm-4 col-md-2">
        <div class="card mini-kpi text-center border-0 shadow-sm card-standard h-100">
            <div class="card-body">
                <div class="fs-2 fw-bold text-success" id="kpi-total-recebido">—</div>
                <div class="small text-muted">Recebido</div>
            </div>
        </div>
    </div>

    <div class="col-4 col-sm-4 col-md-2">
        <div class="card mini-kpi text-center border-0 shadow-sm card-standard h-100">
            <div class="card-body">
                <div class="fs-3 fw-bold text-primary" id="kpi-economia">—</div>
                <div class="small text-muted">Economia</div>
            </div>
        </div>
    </div>

    <div class="col-4 col-sm-4 col-md-2">
        <div class="card mini-kpi text-center border-0 shadow-sm card-standard h-100">
            <div class="card-body">
                <div class="fs-3 fw-bold text-danger" id="kpi-comprometimento">—</div>
                <div class="small text-muted">Comprom.</div>
            </div>
        </div>
    </div>

</div>

{{-- Ações Rápidas e Controles --}}
<div class="row gx-3 gy-3 mb-3">
    <div class="col-md-8">
        <div class="card shadow-sm card-standard h-100">
            <div class="card-header border-0 d-flex justify-content-between align-items-center">
                <h5 class="card-title mb-0"><i class="bi bi-lightning-charge me-2 text-warning"></i>Ações Rápidas</h5>
            </div>
            <div class="card-body">
                <div class="row g-2">
                    <div class="col-6 col-sm-3">
                        <a href="{{ route('admin.financeiro.contas-pagar.index') }}" class="btn btn-outline-danger w-100 d-flex flex-column align-items-center py-2">
                            <i class="bi bi-arrow-up-circle fs-4 mb-1"></i>
                            <span class="small">Nova Despesa</span>
                        </a>
                    </div>
                    <div class="col-6 col-sm-3">
                        <a href="{{ route('admin.financeiro.contas-receber.index') }}" class="btn btn-outline-success w-100 d-flex flex-column align-items-center py-2">
                            <i class="bi bi-arrow-down-circle fs-4 mb-1"></i>
                            <span class="small">Nova Receita</span>
                        </a>
                    </div>
                    <div class="col-6 col-sm-3">
                        <a href="{{ route('admin.saas.faturas.index') }}" class="btn btn-outline-primary w-100 d-flex flex-column align-items-center py-2">
                            <i class="bi bi-receipt fs-4 mb-1"></i>
                            <span class="small">Faturas</span>
                        </a>
                    </div>
                    <div class="col-6 col-sm-3">
                        <a href="{{ route('admin.cron.index') }}" class="btn btn-outline-dark w-100 d-flex flex-column align-items-center py-2">
                            <i class="bi bi-gear fs-4 mb-1"></i>
                            <span class="small">Automações</span>
                        </a>
                    </div>
                </div>
                <div class="row mt-3">
                    <div class="col-12">
                        <div class="p-2 bg-light rounded">
                            <div class="d-flex justify-content-between align-items-center small text-muted">
                                <span><i class="bi bi-clock-history me-1"></i>Última atualização: <span id="ultima-atualizacao">agora</span></span>
                                <span><i class="bi bi-arrow-repeat me-1"></i>Auto-refresh: 5 min</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="card shadow-sm card-standard h-100 border-start border-4 border-info">
            <div class="card-header border-0">
                <h5 class="card-title mb-0"><i class="bi bi-bell me-2 text-info"></i>Alertas</h5>
            </div>
            <div class="card-body p-2">
                <div id="dashboard-alertas" class="small">
                    <div class="d-flex align-items-center p-2 mb-1 rounded bg-warning-subtle">
                        <i class="bi bi-exclamation-triangle text-warning me-2"></i>
                        <span class="text-muted">Verifique contas a vencer</span>
                    </div>
                    <div class="d-flex align-items-center p-2 mb-1 rounded bg-info-subtle">
                        <i class="bi bi-info-circle text-info me-2"></i>
                        <span class="text-muted">Saúde financeira estável</span>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

{{-- Gráficos e Saúde Financeira --}}
<div class="row gx-3 gy-3 mb-3">

    {{-- Evolução Mensal --}}
    <div class="col-xl-8">
        <div class="card shadow-sm card-standard h-100">
            <div class="card-header border-0 pb-0">
                <h5 class="card-title mb-0"><i class="bi bi-graph-up me-2 text-primary"></i>Evolução Mensal (12 meses)</h5>
            </div>
            <div class="card-body position-relative">
                <canvas id="grafico-evolucao" height="280"></canvas>
                <div class="chart-placeholder" id="grafico-evolucao-placeholder">Nenhum histórico disponível.</div>
            </div>
        </div>
    </div>

    {{-- Saúde Financeira --}}
    <div class="col-xl-4">
        <div class="card shadow-sm card-standard h-100">
            <div class="card-header border-0 pb-0">
                <h5 class="card-title mb-0"><i class="bi bi-heart-pulse me-2 text-danger"></i>Saúde Financeira</h5>
            </div>
            <div class="card-body text-center">
                <div class="saude-gauge py-2">
                    <div class="saude-indice" id="saude-indice" style="color:#22c55e;">—</div>
                    <div class="fs-5 fw-semibold mt-1" id="saude-classificacao">Calculando...</div>
                    <div class="small text-muted">de 100 pontos</div>
                </div>
                <div class="progress mb-3" style="height:12px;border-radius:6px;">
                    <div class="progress-bar" id="saude-barra" role="progressbar" style="width:0%;border-radius:6px;" aria-valuenow="0" aria-valuemin="0" aria-valuemax="100"></div>
                </div>
                {{-- Fatores --}}
                <div id="saude-fatores" class="text-start small"></div>
                {{-- Recomendações --}}
                <div class="text-start mt-3" id="saude-recomendacoes"></div>
            </div>
        </div>
    </div>

</div>

{{-- Gráficos de categorias --}}
<div class="row gx-3 gy-3 mb-3">
    <div class="col-md-6">
        <div class="card shadow-sm card-standard">
            <div class="card-header border-0">
                <h5 class="card-title mb-0"><i class="bi bi-pie-chart me-2 text-danger"></i>Top Categorias de Gasto</h5>
            </div>
            <div class="card-body position-relative">
                <canvas id="grafico-categorias-gasto" height="250"></canvas>
                <div class="chart-placeholder" id="grafico-categorias-gasto-placeholder">Nenhuma categoria de gasto disponível.</div>
            </div>
        </div>
    </div>
    <div class="col-md-6">
        <div class="card shadow-sm card-standard">
            <div class="card-header border-0">
                <h5 class="card-title mb-0"><i class="bi bi-pie-chart me-2 text-success"></i>Top Categorias de Receita</h5>
            </div>
            <div class="card-body position-relative">
                <canvas id="grafico-categorias-receita" height="250"></canvas>
                <div class="chart-placeholder" id="grafico-categorias-receita-placeholder">Nenhuma categoria de receita disponível.</div>
            </div>
        </div>
    </div>
</div>

@endsection

@push('scripts')
<script>
let periodoAtual   = 'mes';
let graficoEvolucao, graficoCatGasto, graficoCatReceita;

// -----------------------------------------------
// Carrega KPIs via AJAX
// -----------------------------------------------
function carregarKpis(periodo, inicio, fim) {
    let params = { periodo };
    if (periodo === 'personalizado') { params.inicio = inicio; params.fim = fim; }

    $.getJSON('{{ route("admin.dashboard.kpis") }}', params, function(r) {
        if (!r.sucesso) { toast('Erro ao carregar KPIs.', 'erro'); return; }
        const k = r.kpis;

        // KPIs principais
        $('#kpi-saldo-atual').text(k.saldo_atual_fmt);
        $('#kpi-receita-total').text(k.receita_total_fmt);
        $('#kpi-despesa-total').text(k.despesa_total_fmt);
        $('#kpi-lucro').text(k.lucro_fmt)
            .toggleClass('text-success', k.lucro >= 0)
            .toggleClass('text-danger', k.lucro < 0);
        $('#kpi-lucro-badge').text(k.lucro >= 0 ? 'Lucro' : 'Prejuízo')
            .attr('class', 'badge small ' + (k.lucro >= 0 ? 'bg-success-subtle text-success' : 'bg-danger-subtle text-danger'));

        // Segunda linha
        $('#kpi-cp-vencidas-qtd').text(k.cp_vencidas_qtd);
        $('#kpi-cp-vencidas-valor').text(k.cp_vencidas_valor);
        $('#kpi-vencendo-hoje').text(k.vencendo_hoje_pagar + k.vencendo_hoje_receber);
        $('#kpi-vencendo-7dias').text(k.vencendo_7dias_pagar + k.vencendo_7dias_receber);
        $('#kpi-total-recebido').text(k.total_recebido_fmt);
        $('#kpi-economia').text(k.percentual_economia);
        $('#kpi-comprometimento').text(k.comprometimento_renda);

        // Período
        $('#periodo-label').html('<i class="bi bi-calendar3 me-1"></i>' + r.periodo.inicio + ' — ' + r.periodo.fim);

        // Gráficos
        renderizarEvolucao(k.evolucao_mensal);
        renderizarCategorias(k.categoria_maior_gasto, k.categoria_maior_receita);

    }).fail(function() { toast('Falha na conexão.', 'erro'); });
}

// -----------------------------------------------
// Saúde Financeira via AJAX
// -----------------------------------------------
function carregarSaude() {
    $.getJSON('{{ route("admin.dashboard.saude") }}', function(r) {
        if (!r.sucesso) return;
        const s = r.saude;

        $('#saude-indice').text(s.indice).css('color', {
            'success': '#22c55e', 'info': '#3b82f6', 'warning': '#f59e0b',
            'danger': '#ef4444', 'dark': '#6b7280'
        }[s.cor] || '#22c55e');

        $('#saude-classificacao').text(s.classificacao);
        $('#saude-barra').css('width', s.indice + '%').attr('class',
            'progress-bar bg-' + s.cor);

        // Fatores
        let html = '';
        Object.values(s.fatores).forEach(f => {
            const pct = Math.round((f.pontos / f.maximo) * 100);
            html += `<div class="mb-2">
                <div class="d-flex justify-content-between"><small>${f.nome}</small><small>${f.pontos}/${f.maximo}</small></div>
                <div class="progress" style="height:5px;"><div class="progress-bar" style="width:${pct}%;"></div></div>
            </div>`;
        });
        $('#saude-fatores').html(html);

        // Recomendações
        if (s.recomendacoes.length) {
            let recHtml = '<div class="fw-semibold small mb-2 text-muted">RECOMENDAÇÕES</div>';
            s.recomendacoes.forEach(rec => {
                recHtml += `<div class="recomendacao-item small">${rec}</div>`;
            });
            $('#saude-recomendacoes').html(recHtml);
        }
    });
}

// -----------------------------------------------
// Gráfico de Evolução Mensal
// -----------------------------------------------
function renderizarEvolucao(dados) {
    const temDados = Array.isArray(dados) && dados.length && dados.some(d => d.receita || d.despesa || d.saldo);
    $('#grafico-evolucao').toggle(temDados);
    $('#grafico-evolucao-placeholder').toggle(!temDados);
    if (!temDados) {
        if (graficoEvolucao) graficoEvolucao.destroy();
        return;
    }

    const labels  = dados.map(d => d.mes);
    const receitas = dados.map(d => d.receita);
    const despesas = dados.map(d => d.despesa);
    const saldos   = dados.map(d => d.saldo);

    if (graficoEvolucao) graficoEvolucao.destroy();
    const ctx = document.getElementById('grafico-evolucao').getContext('2d');
    graficoEvolucao = new Chart(ctx, {
        type: 'bar',
        data: {
            labels,
            datasets: [
                { label: 'Receitas',  data: receitas, backgroundColor: 'rgba(34,197,94,.7)',  borderRadius: 6 },
                { label: 'Despesas',  data: despesas, backgroundColor: 'rgba(239,68,68,.7)',   borderRadius: 6 },
                { label: 'Resultado', data: saldos,   type: 'line', borderColor: '#3b82f6', backgroundColor: 'rgba(59,130,246,.1)', tension:.4, fill:true, borderWidth:2, pointRadius:3 }
            ]
        },
        options: {
            responsive: true, maintainAspectRatio: false,
            plugins: { legend: { position: 'top' }, tooltip: {
                callbacks: { label: ctx => 'R$ ' + ctx.raw.toLocaleString('pt-BR', {minimumFractionDigits:2}) }
            }},
            scales: {
                y: { ticks: { callback: v => 'R$ ' + v.toLocaleString('pt-BR') } }
            }
        }
    });
}

// -----------------------------------------------
// Gráficos de Categorias
// -----------------------------------------------
function renderizarCategorias(gasto, receita) {
    const temGasto = Array.isArray(gasto) && gasto.length && gasto.some(c => c.total);
    const temReceita = Array.isArray(receita) && receita.length && receita.some(c => c.total);

    $('#grafico-categorias-gasto').toggle(temGasto);
    $('#grafico-categorias-gasto-placeholder').toggle(!temGasto);
    $('#grafico-categorias-receita').toggle(temReceita);
    $('#grafico-categorias-receita-placeholder').toggle(!temReceita);

    if (temGasto) {
        if (graficoCatGasto) graficoCatGasto.destroy();
        graficoCatGasto = new Chart(document.getElementById('grafico-categorias-gasto'), {
            type: 'doughnut',
            data: {
                labels: gasto.map(c => c.categoria),
                datasets: [{ data: gasto.map(c => c.total),
                    backgroundColor: ['#ef4444','#f97316','#f59e0b','#84cc16','#06b6d4'] }]
            },
            options: { responsive: true, plugins: { legend: { position: 'bottom' } } }
        });
    } else if (graficoCatGasto) {
        graficoCatGasto.destroy();
        graficoCatGasto = null;
    }

    if (temReceita) {
        if (graficoCatReceita) graficoCatReceita.destroy();
        graficoCatReceita = new Chart(document.getElementById('grafico-categorias-receita'), {
            type: 'doughnut',
            data: {
                labels: receita.map(c => c.categoria),
                datasets: [{ data: receita.map(c => c.total),
                    backgroundColor: ['#22c55e','#3b82f6','#8b5cf6','#06b6d4','#f59e0b'] }]
            },
            options: { responsive: true, plugins: { legend: { position: 'bottom' } } }
        });
    } else if (graficoCatReceita) {
        graficoCatReceita.destroy();
        graficoCatReceita = null;
    }
}

// -----------------------------------------------
// Eventos dos filtros de período
// -----------------------------------------------
$('#filtros-periodo .btn').on('click', function() {
    $('#filtros-periodo .btn').removeClass('active');
    $(this).addClass('active');
    periodoAtual = $(this).data('periodo');
    carregarKpis(periodoAtual);
    carregarSaude();
});

$('#btn-periodo-custom').on('click', function() {
    const ini = $('#periodo-inicio').val();
    const fim = $('#periodo-fim').val();
    if (!ini || !fim) { toast('Selecione as datas de início e fim.', 'alerta'); return; }
    $('#filtros-periodo .btn').removeClass('active');
    periodoAtual = 'personalizado';
    carregarKpis('personalizado', ini, fim);
});

// -----------------------------------------------
// Inicialização
// -----------------------------------------------
$(document).ready(function() {
    carregarKpis(periodoAtual);
    carregarSaude();

    // Auto-refresh a cada 5 minutos
    setInterval(function() {
        carregarKpis(periodoAtual);
        carregarSaude();
    }, 300000);
});
</script>
@endpush
