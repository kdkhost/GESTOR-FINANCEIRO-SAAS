@extends('layouts.admin.app')
@section('titulo', 'Inadimplencia')
@section('titulo_pagina', 'Relatorio de Inadimplencia')
@section('breadcrumb')
    <li class="breadcrumb-item">Relatorios</li>
    <li class="breadcrumb-item active">Inadimplencia</li>
@endsection
@section('conteudo')
<div class="row mb-4" id="cards-inadimp"></div>
<div class="card card-outline card-danger">
    <div class="card-header"><h3 class="card-title mb-0"><i class="bi bi-exclamation-triangle text-danger me-2"></i>Contas Vencidas</h3></div>
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover mb-0">
                <thead class="table-light"><tr><th>#</th><th>Descricao</th><th>Vencimento</th><th class="text-end">Valor</th><th class="text-center">Dias em Atraso</th></tr></thead>
                <tbody id="tbody-inadimp"><tr><td colspan="5" class="text-center py-4"><div class="spinner-border text-danger"></div></td></tr></tbody>
            </table>
        </div>
    </div>
</div>
@endsection
@push('scripts')
<script>
$.get('{{ route("admin.dashboard.kpis") }}', {periodo:'mes'}, function(r) {
    if (!r.sucesso) { toast('Erro ao carregar dados.','erro'); return; }
    const k = r.kpis;
    const fmt = v => 'R$ '+parseFloat(v||0).toLocaleString('pt-BR',{minimumFractionDigits:2});
    $('#cards-inadimp').html(`
        <div class="col-md-3"><div class="card bg-danger text-white"><div class="card-body text-center">
            <h6>Contas Vencidas</h6><h4>${k.cp_vencidas_qtd}</h4><small>${k.cp_vencidas_valor}</small>
        </div></div></div>
        <div class="col-md-3"><div class="card bg-warning text-dark"><div class="card-body text-center">
            <h6>A Receber Vencidas</h6><h4>${k.cr_vencidas_qtd}</h4><small>${k.cr_vencidas_valor}</small>
        </div></div></div>
        <div class="col-md-3"><div class="card bg-info text-white"><div class="card-body text-center">
            <h6>Vencem Hoje</h6><h4>${k.vencendo_hoje_pagar}</h4><small>contas a pagar</small>
        </div></div></div>
        <div class="col-md-3"><div class="card bg-secondary text-white"><div class="card-body text-center">
            <h6>Taxa Inadimplencia</h6><h4>${k.taxa_inadimplencia}</h4><small>do periodo</small>
        </div></div></div>
    `);
});

$.get('{{ route("admin.contas-pagar.listar") }}', {status:'vencido',per_page:50}, function(r) {
    const tbody = $('#tbody-inadimp'); tbody.empty();
    if (!r.sucesso||!r.dados.length) { tbody.html('<tr><td colspan="5" class="text-center py-4 text-muted"><i class="bi bi-check-circle text-success fs-3 d-block mb-2"></i>Nenhuma conta vencida!</td></tr>'); return; }
    r.dados.forEach(c => {
        const venc = c.data_vencimento ? c.data_vencimento.split('-').reverse().join('/') : '-';
        const dias = c.data_vencimento ? Math.floor((new Date()-new Date(c.data_vencimento))/86400000) : 0;
        tbody.append(`<tr>
            <td class="text-muted small">${c.id}</td>
            <td class="fw-medium">${c.descricao}</td>
            <td>${venc}</td>
            <td class="text-end text-danger fw-medium">R$ ${parseFloat(c.valor).toLocaleString('pt-BR',{minimumFractionDigits:2})}</td>
            <td class="text-center"><span class="badge bg-danger">${dias} dias</span></td>
        </tr>`);
    });
}).fail(()=>toast('Erro ao carregar contas vencidas.','erro'));
</script>
@endpush