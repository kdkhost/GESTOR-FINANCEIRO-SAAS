@extends('layouts.admin.app')

@section('titulo', 'Dashboard Financeiro')
@section('titulo_pagina', 'Dashboard Financeiro')

@section('breadcrumb')
    <li class="breadcrumb-item active">Dashboard</li>
@endsection

@push('styles')
<style>
.kpi-card { transition: transform .25s ease, box-shadow .25s ease; border-left-width: .45rem; border-left-style: solid; cursor: default; }
.kpi-card:hover { transform: translateY(-2px); box-shadow: 0 18px 40px rgba(15,23,42,.08); }
.kpi-icon { width: 3.5rem; height: 3.5rem; border-radius: 1rem; display: flex; align-items: center; justify-content: center; font-size: 1.4rem; }
.kpi-valor { font-size: 1.6rem; font-weight: 700; line-height: 1; }
.kpi-label { font-size: .8rem; text-transform: uppercase; letter-spacing: .05em; opacity: .8; }
.saude-gauge { min-height: 230px; display: flex; align-items: center; justify-content: center; flex-direction: column; }
.saude-indice { font-size: 3.5rem; font-weight: 800; line-height: 1; }
.filtro-periodo .btn { border-radius: 999px; }
.recomendacao-item { border-left: 3px solid #3b82f6; padding: .75rem 1rem; margin-bottom: .75rem; background: #eff6ff; border-radius: 0 1rem 1rem 0; }
.chart-placeholder { min-height: 250px; display: none; align-items: center; justify-content: center; color: #6b7280; font-weight: 600; }
</style>
@endpush

@section('conteudo')

{{-- Filtros de Período --}}
<div class="row mb-4">
    <div class="col-12">
        <div class="card card-outline card-secondary card-standard">
            <div class="card-body py-3">
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
<div class="row gx-4 gy-4 mb-4" id="kpis-container">

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

{{-- Segunda linha de KPIs --}}
<div class="row gx-4 gy-4 mb-4">

    <div class="col-6 col-sm-6 col-md-4 col-xl-2">
        <div class="card text-center border-0 shadow-sm card-standard h-100">
            <div class="card-body py-3">
                <div class="fs-2 fw-bold text-danger" id="kpi-cp-vencidas-qtd">—</div>
                <div class="small text-muted">Contas Vencidas<br><span class="text-danger fw-medium" id="kpi-cp-vencidas-valor">—</span></div>
            </div>
        </div>
    </div>

    <div class="col-6 col-sm-6 col-md-4 col-xl-2">
        <div class="card text-center border-0 shadow-sm card-standard h-100">
            <div class="card-body py-3">
                <div class="fs-2 fw-bold text-warning" id="kpi-vencendo-hoje">—</div>
                <div class="small text-muted">Vencem Hoje</div>
            </div>
        </div>
    </div>

    <div class="col-6 col-sm-6 col-md-4 col-xl-2">
        <div class="card text-center border-0 shadow-sm card-standard h-100">
            <div class="card-body py-3">
                <div class="fs-2 fw-bold text-info" id="kpi-vencendo-7dias">—</div>
                <div class="small text-muted">Vencem em 7 dias</div>
            </div>
        </div>
    </div>

    <div class="col-6 col-sm-6 col-md-4 col-xl-2">
        <div class="card text-center border-0 shadow-sm card-standard h-100">
            <div class="card-body py-3">
                <div class="fs-2 fw-bold text-success" id="kpi-total-recebido">—</div>
                <div class="small text-muted">Total Recebido</div>
            </div>
        </div>
    </div>

    <div class="col-6 col-sm-6 col-md-4 col-xl-2">
        <div class="card text-center border-0 shadow-sm card-standard h-100">
            <div class="card-body py-3">
                <div class="fs-3 fw-bold" id="kpi-economia">—</div>
                <div class="small text-muted">% Economia</div>
            </div>
        </div>
    </div>

    <div class="col-6 col-sm-6 col-md-4 col-xl-2">
        <div class="card text-center border-0 shadow-sm card-standard h-100">
            <div class="card-body py-3">
                <div class="fs-3 fw-bold text-danger" id="kpi-comprometimento">—</div>
                <div class="small text-muted">% Comprometimento</div>
            </div>
        </div>
    </div>

</div>

{{-- Gráficos e Saúde Financeira --}}
<div class="row gx-4 gy-4 mb-4">

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
<div class="row gx-4 gy-4 mb-4">
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
