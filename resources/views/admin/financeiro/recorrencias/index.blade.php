@extends('layouts.admin.app')
@section('titulo', 'Recorrencias')
@section('titulo_pagina', 'Lancamentos Recorrentes')
@section('breadcrumb')
    <li class="breadcrumb-item">Financeiro</li>
    <li class="breadcrumb-item active">Recorrencias</li>
@endsection
@section('conteudo')
<div class="card card-outline card-warning">
    <div class="card-header d-flex align-items-center justify-content-between">
        <h3 class="card-title mb-0"><i class="bi bi-arrow-repeat text-warning me-2"></i>Recorrencias</h3>
        <button class="btn btn-warning btn-sm" id="btn-novo"><i class="bi bi-plus-lg me-1"></i>Nova Recorrencia</button>
    </div>
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover mb-0">
                <thead class="table-light">
                    <tr><th>#</th><th>Descricao</th><th>Tipo</th><th>Dia Venc.</th><th class="text-end">Valor</th><th class="text-center">Status</th><th class="text-end" style="width:100px">Acoes</th></tr>
                </thead>
                <tbody id="tbody-recorrencias">
                    <tr><td colspan="7" class="text-center py-4 text-muted"><i class="bi bi-hourglass-split me-2"></i>Carregando...</td></tr>
                </tbody>
            </table>
        </div>
    </div>
</div>
<div class="modal fade" id="modal-rec" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header bg-warning text-dark">
                <h5 class="modal-title"><i class="bi bi-arrow-repeat me-2"></i><span id="modal-rec-titulo">Nova Recorrencia</span></h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form id="form-rec">
                <div class="modal-body">
                    <input type="hidden" id="rec-id">
                    <div class="row g-3">
                        <div class="col-md-8">
                            <label class="form-label fw-medium">Descricao <span class="text-danger">*</span></label>
                            <input type="text" name="descricao" class="form-control" required placeholder="Ex: Aluguel mensal, Salario...">
                        </div>
                        <div class="col-md-4">
                            <label class="form-label fw-medium">Tipo <span class="text-danger">*</span></label>
                            <select name="tipo" class="form-select" required>
                                <option value="">Selecione...</option>
                                <option value="pagar">A Pagar</option>
                                <option value="receber">A Receber</option>
                            </select>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label fw-medium">Valor <span class="text-danger">*</span></label>
                            <div class="input-group"><span class="input-group-text">R$</span>
                                <input type="text" name="valor" class="form-control mask-moeda" required placeholder="0,00"></div>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label fw-medium">Dia de Vencimento <span class="text-danger">*</span></label>
                            <input type="number" name="dia_vencimento" class="form-control" required min="1" max="31" placeholder="1-31">
                        </div>
                        <div class="col-md-4">
                            <label class="form-label fw-medium">Data Inicio <span class="text-danger">*</span></label>
                            <input type="text" name="data_inicio" class="form-control mask-data" required placeholder="dd/mm/aaaa">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-medium">Categoria</label>
                            <select name="categoria_id" class="form-select" id="sel-cat-rec"><option value="">Selecione...</option></select>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-medium">Conta Bancaria</label>
                            <select name="conta_bancaria_id" class="form-select" id="sel-cb-rec"><option value="">Selecione...</option></select>
                        </div>
                        <div class="col-12">
                            <div class="form-check form-switch">
                                <input class="form-check-input" type="checkbox" name="ativo" id="rec-ativo" value="1" checked>
                                <label class="form-check-label" for="rec-ativo">Recorrencia ativa</label>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Cancelar</button>
                    <button type="submit" class="btn btn-warning"><i class="bi bi-check-lg me-1"></i>Salvar</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
@push('scripts')
<script>
$(document).ready(function() {
const URLS_R = {
    index:  '{{ route("admin.recorrencias.index") }}',
    store:  '{{ route("admin.recorrencias.store") }}',
    show:   '/admin/recorrencias/',
    update: '/admin/recorrencias/',
    destroy:'/admin/recorrencias/',
    cats:   '{{ route("admin.categorias.index") }}',
    contas: '{{ route("admin.contas-bancarias.listar") }}',
};

function carregarSelects() {
    $.get(URLS_R.cats, r => { if(r.sucesso) $('#sel-cat-rec').append(r.dados.map(c=>`<option value="${c.id}">${c.nome}</option>`).join('')); });
    $.get(URLS_R.contas, r => { if(r.sucesso) $('#sel-cb-rec').append(r.dados.map(c=>`<option value="${c.id}">${c.nome}</option>`).join('')); });
}

function carregarTabela() {
    $.get(URLS_R.index, function(r) {
        const tbody = $('#tbody-recorrencias'); tbody.empty();
        if (!r.sucesso||!r.dados.length) { tbody.html('<tr><td colspan="7" class="text-center py-4 text-muted"><i class="bi bi-inbox fs-3 d-block mb-2"></i>Nenhuma recorrencia cadastrada.</td></tr>'); return; }
        r.dados.forEach(rec => {
            const valor = 'R$ '+parseFloat(rec.valor||0).toLocaleString('pt-BR',{minimumFractionDigits:2});
            tbody.append(`<tr>
                <td class="text-muted small">${rec.id}</td>
                <td><div class="fw-medium">${rec.descricao}</div>${rec.categoria?'<small class="text-muted">'+rec.categoria.nome+'</small>':''}</td>
                <td><span class="badge bg-${rec.tipo==='pagar'?'danger':'success'}">${rec.tipo==='pagar'?'A Pagar':'A Receber'}</span></td>
                <td>Dia ${rec.dia_vencimento}</td>
                <td class="text-end fw-medium">${valor}</td>
                <td class="text-center"><span class="badge bg-${rec.ativo?'success':'secondary'}">${rec.ativo?'Ativa':'Inativa'}</span></td>
                <td class="text-end"><div class="btn-group btn-group-sm">
                    <button class="btn btn-outline-primary btn-editar-rec" data-id="${rec.id}"><i class="bi bi-pencil"></i></button>
                    <button class="btn btn-outline-danger btn-excluir-rec" data-id="${rec.id}"><i class="bi bi-trash"></i></button>
                </div></td>
            </tr>`);
        });
    }).fail(()=>toast('Erro ao carregar recorrencias.','erro'));
}

$('#btn-novo').on('click',()=>{$('#modal-rec-titulo').text('Nova Recorrencia');$('#rec-id').val('');$('#form-rec')[0].reset();$('#rec-ativo').prop('checked',true);$('#form-rec [name="data_inicio"]').val(new Date().toLocaleDateString('pt-BR'));$('#modal-rec').modal('show');});

$(document).on('click','.btn-editar-rec',function(){
    $.get(URLS_R.show+$(this).data('id'),r=>{
        if(!r.sucesso)return;
        const rec=r.dado;
        $('#modal-rec-titulo').text('Editar Recorrencia');$('#rec-id').val(rec.id);
        const f=$('#form-rec');
        f.find('[name="descricao"]').val(rec.descricao);
        f.find('[name="tipo"]').val(rec.tipo);
        f.find('[name="valor"]').val(parseFloat(rec.valor||0).toLocaleString('pt-BR',{minimumFractionDigits:2}));
        f.find('[name="dia_vencimento"]').val(rec.dia_vencimento);
        f.find('[name="data_inicio"]').val(rec.data_inicio?rec.data_inicio.split('-').reverse().join('/'):'');
        f.find('[name="categoria_id"]').val(rec.categoria_id);
        f.find('[name="conta_bancaria_id"]').val(rec.conta_bancaria_id);
        $('#rec-ativo').prop('checked',!!rec.ativo);
        $('#modal-rec').modal('show');
    });
});

$('#form-rec').on('submit',function(e){
    e.preventDefault();
    const id=$('#rec-id').val();
    const dados={}; $(this).serializeArray().forEach(f=>dados[f.name]=f.value);
    dados.ativo=$('#rec-ativo').is(':checked')?1:0;
    $.ajax({url:id?URLS_R.update+id:URLS_R.store,type:id?'PUT':'POST',data:dados,
        success:r=>{if(r.sucesso){toast(r.mensagem,'sucesso');$('#modal-rec').modal('hide');carregarTabela();}else toast(r.mensagem||'Erro.','erro');},
        error:r=>toast(r.responseJSON?.mensagem||'Erro ao salvar.','erro'),
    });
});

$(document).on('click','.btn-excluir-rec',function(){
    const id=$(this).data('id');
    confirmarExclusao(URLS_R.destroy+id,()=>{
        $.ajax({url:URLS_R.destroy+id,type:'DELETE',
            success:r=>{toast(r.mensagem,'sucesso');carregarTabela();},
            error:r=>toast(r.responseJSON?.mensagem||'Erro.','erro'),
        });
    });
});

    carregarSelects();
    carregarTabela();
}); // fecha $(document).ready
</script>
@endpush