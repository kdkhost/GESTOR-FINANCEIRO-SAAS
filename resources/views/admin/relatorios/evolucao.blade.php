@extends('layouts.admin.app')
@section('titulo', 'Evolucao Mensal')
@section('titulo_pagina', 'Evolucao Financeira Mensal')
@section('breadcrumb')
    <li class="breadcrumb-item">Relatorios</li>
    <li class="breadcrumb-item active">Evolucao</li>
@endsection
@section('conteudo')
<div class="card card-outline card-primary">
    <div class="card-header"><h3 class="card-title mb-0"><i class="bi bi-graph-up me-2"></i>Evolucao dos Ultimos 12 Meses</h3></div>
    <div class="card-body"><canvas id="grafico-evolucao" height="80"></canvas></div>
</div>
<div class="card card-outline card-primary mt-3">
    <div class="card-header"><h3 class="card-title mb-0"><i class="bi bi-table me-2"></i>Detalhamento Mensal</h3></div>
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover mb-0">
                <thead class="table-light"><tr><th>Mes</th><th class="text-end">Receitas</th><th class="text-end">Despesas</th><th class="text-end">Saldo</th></tr></thead>
                <tbody id="tbody-evolucao"></tbody>
            </table>
        </div>
    </div>
</div>
@endsection
@push('scripts')
<script>
mostrarLoading();
$.get('{{ route("admin.dashboard.kpis") }}', {periodo:'ano'}, function(r) {
    ocultarLoading();
    if (!r.sucesso) { toast('Erro ao carregar dados.','erro'); return; }
    const evolucao = r.kpis.evolucao_mensal || [];
    const fmt = v => 'R$ '+parseFloat(v||0).toLocaleString('pt-BR',{minimumFractionDigits:2});

    new Chart(document.getElementById('grafico-evolucao'), {
        type: 'line',
        data: {
            labels: evolucao.map(e=>e.mes),
            datasets: [
                {label:'Receitas',data:evolucao.map(e=>e.receita),borderColor:'#22c55e',backgroundColor:'rgba(34,197,94,0.1)',fill:true,tension:0.3},
                {label:'Despesas',data:evolucao.map(e=>e.despesa),borderColor:'#ef4444',backgroundColor:'rgba(239,68,68,0.1)',fill:true,tension:0.3},
                {label:'Saldo',data:evolucao.map(e=>e.saldo),borderColor:'#3b82f6',backgroundColor:'rgba(59,130,246,0.1)',fill:true,tension:0.3},
            ]
        },
        options:{responsive:true,plugins:{legend:{position:'top'}},scales:{y:{beginAtZero:false}}}
    });

    const tbody = $('#tbody-evolucao'); tbody.empty();
    evolucao.forEach(e => {
        const saldo = parseFloat(e.saldo||0);
        tbody.append(`<tr>
            <td class="fw-medium">${e.mes}</td>
            <td class="text-end text-success">${fmt(e.receita)}</td>
            <td class="text-end text-danger">${fmt(e.despesa)}</td>
            <td class="text-end fw-bold ${saldo>=0?'text-primary':'text-danger'}">${fmt(saldo)}</td>
        </tr>`);
    });
}).fail(()=>{ocultarLoading();toast('Erro ao carregar evolucao.','erro');});
</script>
@endpush