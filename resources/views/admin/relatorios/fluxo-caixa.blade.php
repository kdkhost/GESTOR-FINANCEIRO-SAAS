@extends('layouts.admin.app')
@section('titulo', 'Fluxo de Caixa')
@section('titulo_pagina', 'Fluxo de Caixa')
@section('breadcrumb')
    <li class="breadcrumb-item">Relatorios</li>
    <li class="breadcrumb-item active">Fluxo de Caixa</li>
@endsection
@section('conteudo')
<div class="card card-outline card-secondary mb-3">
    <div class="card-body py-2">
        <div class="row g-2 align-items-end">
            <div class="col-md-2"><label class="form-label small mb-1">De</label>
                <input type="text" id="filtro-inicio" class="form-control form-control-sm mask-data" value="{{ now()->startOfMonth()->format('d/m/Y') }}"></div>
            <div class="col-md-2"><label class="form-label small mb-1">Ate</label>
                <input type="text" id="filtro-fim" class="form-control form-control-sm mask-data" value="{{ now()->endOfMonth()->format('d/m/Y') }}"></div>
            <div class="col-md-2 d-flex gap-2 align-items-end">
                <button class="btn btn-primary btn-sm" id="btn-gerar"><i class="bi bi-play-circle me-1"></i>Gerar</button>
            </div>
        </div>
    </div>
</div>

<div class="row mb-4" id="cards-resumo" style="display:none!important;"></div>

<div class="card card-outline card-primary" id="card-grafico" style="display:none;">
    <div class="card-header"><h3 class="card-title mb-0"><i class="bi bi-bar-chart me-2"></i>Evolucao do Periodo</h3></div>
    <div class="card-body"><canvas id="grafico-fluxo" height="100"></canvas></div>
</div>

<div class="card card-outline card-primary mt-3" id="card-tabela" style="display:none;">
    <div class="card-header d-flex align-items-center justify-content-between">
        <h3 class="card-title mb-0"><i class="bi bi-table me-2"></i>Detalhamento</h3>
    </div>
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover mb-0">
                <thead class="table-light">
                    <tr><th>Data</th><th>Descricao</th><th>Tipo</th><th class="text-end">Valor</th></tr>
                </thead>
                <tbody id="tbody-fluxo"></tbody>
            </table>
        </div>
    </div>
</div>
@endsection
@push('scripts')
<script>
let graficoFluxo = null;

$('#btn-gerar').on('click', function() {
    const inicio = $('#filtro-inicio').val();
    const fim = $('#filtro-fim').val();
    if (!inicio || !fim) { toast('Informe o periodo.', 'alerta'); return; }

    mostrarLoading();
    $.get('{{ route("admin.dashboard.kpis") }}', {periodo:'personalizado', inicio, fim}, function(r) {
        ocultarLoading();
        if (!r.sucesso) { toast('Erro ao gerar relatorio.', 'erro'); return; }
        const k = r.kpis;

        // Cards resumo
        $('#cards-resumo').html(`
            <div class="col-md-3"><div class="card bg-success text-white"><div class="card-body text-center">
                <h6>Total Receitas</h6><h4>${k.receita_total_fmt}</h4>
            </div></div></div>
            <div class="col-md-3"><div class="card bg-danger text-white"><div class="card-body text-center">
                <h6>Total Despesas</h6><h4>${k.despesa_total_fmt}</h4>
            </div></div></div>
            <div class="col-md-3"><div class="card bg-${k.lucro>=0?'primary':'warning'} text-white"><div class="card-body text-center">
                <h6>Resultado</h6><h4>${k.lucro_fmt}</h4>
            </div></div></div>
            <div class="col-md-3"><div class="card bg-info text-white"><div class="card-body text-center">
                <h6>Saldo Atual</h6><h4>${k.saldo_atual_fmt}</h4>
            </div></div></div>
        `).css('display','flex');

        // Grafico
        const evolucao = k.evolucao_mensal || [];
        if (evolucao.length) {
            $('#card-grafico').show();
            if (graficoFluxo) graficoFluxo.destroy();
            graficoFluxo = new Chart(document.getElementById('grafico-fluxo'), {
                type: 'bar',
                data: {
                    labels: evolucao.map(e=>e.mes),
                    datasets: [
                        {label:'Receitas',data:evolucao.map(e=>e.receita),backgroundColor:'rgba(34,197,94,0.7)'},
                        {label:'Despesas',data:evolucao.map(e=>e.despesa),backgroundColor:'rgba(239,68,68,0.7)'},
                        {label:'Saldo',data:evolucao.map(e=>e.saldo),type:'line',borderColor:'#3b82f6',fill:false,tension:0.3},
                    ]
                },
                options:{responsive:true,plugins:{legend:{position:'top'}}}
            });
        }

        // Tabela fluxo realizado
        const fluxo = k.fluxo_realizado || [];
        const tbody = $('#tbody-fluxo'); tbody.empty();
        if (fluxo.length) {
            $('#card-tabela').show();
            fluxo.forEach(f => {
                if (f.entrada > 0) tbody.append(`<tr><td>${f.data}</td><td>Receitas</td><td><span class="badge bg-success">Entrada</span></td><td class="text-end text-success fw-medium">R$ ${parseFloat(f.entrada).toLocaleString('pt-BR',{minimumFractionDigits:2})}</td></tr>`);
                if (f.saida > 0) tbody.append(`<tr><td>${f.data}</td><td>Despesas</td><td><span class="badge bg-danger">Saida</span></td><td class="text-end text-danger fw-medium">R$ ${parseFloat(f.saida).toLocaleString('pt-BR',{minimumFractionDigits:2})}</td></tr>`);
            });
        }
    }).fail(()=>{ocultarLoading();toast('Erro ao gerar relatorio.','erro');});
});

// Gera automaticamente ao carregar
$('#btn-gerar').trigger('click');
</script>
@endpush