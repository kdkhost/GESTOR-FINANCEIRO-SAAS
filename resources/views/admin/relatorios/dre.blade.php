@extends('layouts.admin.app')
@section('titulo', 'DRE')
@section('titulo_pagina', 'Demonstrativo de Resultado')
@section('breadcrumb')
    <li class="breadcrumb-item">Relatorios</li>
    <li class="breadcrumb-item active">DRE</li>
@endsection
@section('conteudo')
<div class="card card-outline card-secondary mb-3">
    <div class="card-body py-2">
        <div class="row g-2 align-items-end">
            <div class="col-md-2"><label class="form-label small mb-1">Mes</label>
                <select id="sel-mes" class="form-select form-select-sm">
                    @foreach(['Janeiro','Fevereiro','Marco','Abril','Maio','Junho','Julho','Agosto','Setembro','Outubro','Novembro','Dezembro'] as $i => $m)
                        <option value="{{ $i+1 }}" {{ ($i+1)==date('n')?'selected':'' }}>{{ $m }}</option>
                    @endforeach
                </select></div>
            <div class="col-md-2"><label class="form-label small mb-1">Ano</label>
                <input type="number" id="sel-ano" class="form-control form-control-sm" value="{{ date('Y') }}"></div>
            <div class="col-md-2 d-flex align-items-end">
                <button class="btn btn-primary btn-sm" id="btn-gerar"><i class="bi bi-play-circle me-1"></i>Gerar DRE</button>
            </div>
        </div>
    </div>
</div>
<div id="dre-resultado" style="display:none;">
    <div class="card card-outline card-primary">
        <div class="card-header"><h3 class="card-title mb-0"><i class="bi bi-file-earmark-bar-graph me-2"></i>DRE — <span id="dre-periodo"></span></h3></div>
        <div class="card-body p-0">
            <table class="table mb-0">
                <tbody id="tbody-dre"></tbody>
            </table>
        </div>
    </div>
</div>
@endsection
@push('scripts')
<script>
$('#btn-gerar').on('click', function() {
    mostrarLoading();
    const mes = $('#sel-mes').val();
    const ano = $('#sel-ano').val();
    const meses = ['','Janeiro','Fevereiro','Marco','Abril','Maio','Junho','Julho','Agosto','Setembro','Outubro','Novembro','Dezembro'];
    $.get('{{ route("admin.dashboard.kpis") }}', {periodo:'personalizado', inicio:'01/'+String(mes).padStart(2,'0')+'/'+ano, fim:'31/'+String(mes).padStart(2,'0')+'/'+ano}, function(r) {
        ocultarLoading();
        if (!r.sucesso) { toast('Erro ao gerar DRE.','erro'); return; }
        const k = r.kpis;
        $('#dre-periodo').text(meses[mes]+'/'+ano);
        const tbody = $('#tbody-dre'); tbody.empty();
        const fmt = v => 'R$ '+parseFloat(v||0).toLocaleString('pt-BR',{minimumFractionDigits:2});
        const lucro = parseFloat(k.receita_total||0) - parseFloat(k.despesa_total||0);
        tbody.html(`
            <tr class="table-success"><td colspan="2" class="fw-bold ps-3">RECEITAS</td></tr>
            <tr><td class="ps-4">Receitas do Periodo</td><td class="text-end text-success fw-medium">${fmt(k.receita_total)}</td></tr>
            <tr class="table-danger"><td colspan="2" class="fw-bold ps-3">DESPESAS</td></tr>
            <tr><td class="ps-4">Despesas do Periodo</td><td class="text-end text-danger fw-medium">(${fmt(k.despesa_total)})</td></tr>
            <tr class="table-${lucro>=0?'primary':'warning'} fw-bold">
                <td class="ps-3 fs-6">RESULTADO LIQUIDO</td>
                <td class="text-end fs-6 ${lucro>=0?'text-primary':'text-danger'}">${fmt(lucro)}</td>
            </tr>
            <tr class="table-light"><td colspan="2" class="fw-bold ps-3">INDICADORES</td></tr>
            <tr><td class="ps-4">Comprometimento da Renda</td><td class="text-end">${k.comprometimento_renda}</td></tr>
            <tr><td class="ps-4">Margem de Economia</td><td class="text-end">${k.percentual_economia}</td></tr>
            <tr><td class="ps-4">Taxa de Inadimplencia</td><td class="text-end">${k.taxa_inadimplencia}</td></tr>
        `);
        $('#dre-resultado').show();
    }).fail(()=>{ocultarLoading();toast('Erro ao gerar DRE.','erro');});
});
$('#btn-gerar').trigger('click');
</script>
@endpush