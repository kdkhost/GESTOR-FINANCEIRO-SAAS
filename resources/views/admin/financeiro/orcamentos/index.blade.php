@extends('layouts.admin.app')
@section('titulo', 'Orcamentos')
@section('titulo_pagina', 'Orcamentos por Categoria')
@section('breadcrumb')
    <li class="breadcrumb-item">Planejamento</li>
    <li class="breadcrumb-item active">Orcamentos</li>
@endsection
@section('conteudo')
<div class="card card-outline card-primary">
    <div class="card-header d-flex align-items-center justify-content-between">
        <h3 class="card-title mb-0"><i class="bi bi-pie-chart text-primary me-2"></i>Orcamentos</h3>
        <button class="btn btn-primary btn-sm" id="btn-novo"><i class="bi bi-plus-lg me-1"></i>Novo Orcamento</button>
    </div>
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover mb-0">
                <thead class="table-light">
                    <tr><th>#</th><th>Categoria</th><th>Mes/Ano</th><th class="text-end">Limite</th><th>Progresso</th><th class="text-end" style="width:100px">Acoes</th></tr>
                </thead>
                <tbody id="tbody-orcamentos">
                    <tr><td colspan="6" class="text-center py-4 text-muted"><i class="bi bi-hourglass-split me-2"></i>Carregando...</td></tr>
                </tbody>
            </table>
        </div>
    </div>
</div>
<div class="modal fade" id="modal-orc" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header bg-primary text-white">
                <h5 class="modal-title"><i class="bi bi-pie-chart me-2"></i><span id="modal-orc-titulo">Novo Orcamento</span></h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <form id="form-orc">
                <div class="modal-body">
                    <input type="hidden" id="orc-id">
                    <div class="row g-3">
                        <div class="col-12">
                            <label class="form-label fw-medium">Categoria <span class="text-danger">*</span></label>
                            <select name="categoria_id" class="form-select" required id="sel-cat-orc"><option value="">Selecione...</option></select>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-medium">Mes <span class="text-danger">*</span></label>
                            <select name="mes" class="form-select" required>
                                @foreach(['Janeiro','Fevereiro','Marco','Abril','Maio','Junho','Julho','Agosto','Setembro','Outubro','Novembro','Dezembro'] as $i => $m)
                                    <option value="{{ $i+1 }}" {{ ($i+1)==date('n')?'selected':'' }}>{{ $m }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-medium">Ano <span class="text-danger">*</span></label>
                            <input type="number" name="ano" class="form-control" value="{{ date('Y') }}" required min="2020" max="2030">
                        </div>
                        <div class="col-12">
                            <label class="form-label fw-medium">Valor Limite <span class="text-danger">*</span></label>
                            <div class="input-group"><span class="input-group-text">R$</span>
                                <input type="text" name="valor_limite" class="form-control mask-moeda" required placeholder="0,00"></div>
                        </div>
                        <div class="col-12">
                            <label class="form-label fw-medium">Alertar quando atingir (%)</label>
                            <input type="number" name="alertar_em" class="form-control" value="80" min="1" max="100">
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Cancelar</button>
                    <button type="submit" class="btn btn-primary"><i class="bi bi-check-lg me-1"></i>Salvar</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
@push('scripts')
<script>
const URLS_O = {
    index:  '{{ route("admin.orcamentos.index") }}',
    store:  '{{ route("admin.orcamentos.store") }}',
    show:   '/admin/orcamentos/',
    update: '/admin/orcamentos/',
    destroy:'/admin/orcamentos/',
    cats:   '{{ route("admin.categorias.index") }}',
};
const meses = ['','Janeiro','Fevereiro','Marco','Abril','Maio','Junho','Julho','Agosto','Setembro','Outubro','Novembro','Dezembro'];

function carregarTabela() {
    $.get(URLS_O.index, function(r) {
        const tbody = $('#tbody-orcamentos'); tbody.empty();
        if (!r.sucesso||!r.dados.length) { tbody.html('<tr><td colspan="6" class="text-center py-4 text-muted"><i class="bi bi-inbox fs-3 d-block mb-2"></i>Nenhum orcamento cadastrado.</td></tr>'); return; }
        r.dados.forEach(o => {
            const limite = parseFloat(o.valor_limite||0);
            tbody.append(`<tr>
                <td class="text-muted small">${o.id}</td>
                <td>${o.categoria?o.categoria.nome:'<span class="text-muted">-</span>'}</td>
                <td>${meses[o.mes]||o.mes}/${o.ano}</td>
                <td class="text-end fw-medium">R$ ${limite.toLocaleString('pt-BR',{minimumFractionDigits:2})}</td>
                <td><div class="progress" style="height:6px;"><div class="progress-bar bg-primary" style="width:0%"></div></div></td>
                <td class="text-end"><div class="btn-group btn-group-sm">
                    <button class="btn btn-outline-primary btn-editar-orc" data-id="${o.id}"><i class="bi bi-pencil"></i></button>
                    <button class="btn btn-outline-danger btn-excluir-orc" data-id="${o.id}"><i class="bi bi-trash"></i></button>
                </div></td>
            </tr>`);
        });
    }).fail(()=>toast('Erro ao carregar orcamentos.','erro'));
}

$.get(URLS_O.cats, r => {
    if (!r.sucesso) return;
    $('#sel-cat-orc').append(r.dados.map(c=>`<option value="${c.id}">${c.nome}</option>`).join(''));
});

$('#btn-novo').on('click',()=>{$('#modal-orc-titulo').text('Novo Orcamento');$('#orc-id').val('');$('#form-orc')[0].reset();$('#modal-orc').modal('show');});

$(document).on('click','.btn-editar-orc',function(){
    $.get(URLS_O.show+$(this).data('id'),r=>{
        if(!r.sucesso)return;
        const o=r.dado;
        $('#modal-orc-titulo').text('Editar Orcamento');$('#orc-id').val(o.id);
        const f=$('#form-orc');
        f.find('[name="categoria_id"]').val(o.categoria_id);
        f.find('[name="mes"]').val(o.mes);
        f.find('[name="ano"]').val(o.ano);
        f.find('[name="valor_limite"]').val(parseFloat(o.valor_limite||0).toLocaleString('pt-BR',{minimumFractionDigits:2}));
        f.find('[name="alertar_em"]').val(o.alertar_em||80);
        $('#modal-orc').modal('show');
    });
});

$('#form-orc').on('submit',function(e){
    e.preventDefault();
    const id=$('#orc-id').val();
    const dados={}; $(this).serializeArray().forEach(f=>dados[f.name]=f.value);
    $.ajax({url:id?URLS_O.update+id:URLS_O.store,type:id?'PUT':'POST',data:dados,
        success:r=>{if(r.sucesso){toast(r.mensagem,'sucesso');$('#modal-orc').modal('hide');carregarTabela();}else toast(r.mensagem||'Erro.','erro');},
        error:r=>toast(r.responseJSON?.mensagem||'Erro ao salvar.','erro'),
    });
});

$(document).on('click','.btn-excluir-orc',function(){
    const id=$(this).data('id');
    confirmarExclusao(URLS_O.destroy+id,()=>{
        $.ajax({url:URLS_O.destroy+id,type:'DELETE',
            success:r=>{toast(r.mensagem,'sucesso');carregarTabela();},
            error:r=>toast(r.responseJSON?.mensagem||'Erro.','erro'),
        });
    });
});

carregarTabela();
</script>
@endpush