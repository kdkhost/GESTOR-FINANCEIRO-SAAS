@extends('layouts.admin.app')
@section('titulo', 'Transferencias')
@section('titulo_pagina', 'Transferencias entre Contas')
@section('breadcrumb')
    <li class="breadcrumb-item">Financeiro</li>
    <li class="breadcrumb-item active">Transferencias</li>
@endsection
@section('conteudo')
<div class="card card-outline card-info mb-3">
    <div class="card-body py-2">
        <div class="row g-2 align-items-end">
            <div class="col-md-2"><label class="form-label small mb-1">De</label>
                <input type="text" id="filtro-inicio" class="form-control form-control-sm mask-data" placeholder="dd/mm/aaaa"></div>
            <div class="col-md-2"><label class="form-label small mb-1">Ate</label>
                <input type="text" id="filtro-fim" class="form-control form-control-sm mask-data" placeholder="dd/mm/aaaa"></div>
            <div class="col-md-2 d-flex gap-2 align-items-end">
                <button class="btn btn-info btn-sm text-white" id="btn-filtrar"><i class="bi bi-search me-1"></i>Filtrar</button>
                <button class="btn btn-outline-secondary btn-sm" id="btn-limpar"><i class="bi bi-x-lg"></i></button>
            </div>
        </div>
    </div>
</div>
<div class="card card-outline card-info">
    <div class="card-header d-flex align-items-center justify-content-between">
        <h3 class="card-title mb-0"><i class="bi bi-arrow-left-right text-info me-2"></i>Transferencias</h3>
        <button class="btn btn-info btn-sm text-white" id="btn-novo"><i class="bi bi-plus-lg me-1"></i>Nova Transferencia</button>
    </div>
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover mb-0">
                <thead class="table-light">
                    <tr><th>#</th><th>Descricao</th><th>Origem</th><th>Destino</th><th>Data</th><th class="text-end">Valor</th><th class="text-end" style="width:80px">Acoes</th></tr>
                </thead>
                <tbody id="tbody-transferencias">
                    <tr><td colspan="7" class="text-center py-4 text-muted"><i class="bi bi-hourglass-split me-2"></i>Carregando...</td></tr>
                </tbody>
            </table>
        </div>
    </div>
    <div class="card-footer d-flex align-items-center justify-content-between">
        <span class="text-muted small" id="info-paginacao">0 registros</span>
        <nav><ul class="pagination pagination-sm mb-0" id="paginacao"></ul></nav>
    </div>
</div>
<div class="modal fade" id="modal-transf" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header bg-info text-white">
                <h5 class="modal-title"><i class="bi bi-arrow-left-right me-2"></i>Nova Transferencia</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <form id="form-transf">
                <div class="modal-body">
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label fw-medium">Conta Origem <span class="text-danger">*</span></label>
                            <select name="conta_origem_id" class="form-select" required id="sel-origem"><option value="">Selecione...</option></select>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-medium">Conta Destino <span class="text-danger">*</span></label>
                            <select name="conta_destino_id" class="form-select" required id="sel-destino"><option value="">Selecione...</option></select>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-medium">Valor <span class="text-danger">*</span></label>
                            <div class="input-group"><span class="input-group-text">R$</span>
                                <input type="text" name="valor" class="form-control mask-moeda" required placeholder="0,00"></div>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-medium">Data <span class="text-danger">*</span></label>
                            <input type="text" name="data_transferencia" class="form-control mask-data" required placeholder="dd/mm/aaaa">
                        </div>
                        <div class="col-12">
                            <label class="form-label fw-medium">Descricao</label>
                            <input type="text" name="descricao" class="form-control" placeholder="Motivo da transferencia...">
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Cancelar</button>
                    <button type="submit" class="btn btn-info text-white"><i class="bi bi-check-lg me-1"></i>Transferir</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
@push('scripts')
<script>
$(document).ready(function() {
const URLS_T = {
    listar: '{{ route("admin.transferencias.listar") }}',
    store:  '{{ route("admin.transferencias.store") }}',
    destroy:'/admin/transferencias/',
    contas: '{{ route("admin.contas-bancarias.listar") }}',
};
let paginaAtual = 1; const perPage = 10;

function carregarContas() {
    $.get(URLS_T.contas, r => {
        if (!r.sucesso) return;
        const opts = r.dados.map(c=>`<option value="${c.id}">${c.nome} (R$ ${parseFloat(c.saldo_atual||0).toLocaleString('pt-BR',{minimumFractionDigits:2})})</option>`).join('');
        $('#sel-origem, #sel-destino').append(opts);
    });
}

function carregarTabela(pagina=1) {
    paginaAtual = pagina;
    $.get(URLS_T.listar, {page:pagina,per_page:perPage,inicio:$('#filtro-inicio').val(),fim:$('#filtro-fim').val()}, function(r) {
        const tbody = $('#tbody-transferencias'); tbody.empty();
        if (!r.dados.length) { tbody.html('<tr><td colspan="7" class="text-center py-4 text-muted"><i class="bi bi-inbox fs-3 d-block mb-2"></i>Nenhuma transferencia encontrada.</td></tr>'); $('#info-paginacao').text('0 registros'); return; }
        r.dados.forEach(t => {
            const data = t.data_transferencia ? t.data_transferencia.split('-').reverse().join('/') : '-';
            const valor = 'R$ '+parseFloat(t.valor).toLocaleString('pt-BR',{minimumFractionDigits:2});
            tbody.append(`<tr>
                <td class="text-muted small">${t.id}</td>
                <td>${t.descricao||'<span class="text-muted">-</span>'}</td>
                <td>${t.conta_origem?t.conta_origem.nome:'<span class="text-muted">-</span>'}</td>
                <td>${t.conta_destino?t.conta_destino.nome:'<span class="text-muted">-</span>'}</td>
                <td>${data}</td>
                <td class="text-end fw-medium text-info">${valor}</td>
                <td class="text-end"><button class="btn btn-outline-danger btn-sm btn-excluir" data-id="${t.id}"><i class="bi bi-trash"></i></button></td>
            </tr>`);
        });
        const ini=(pagina-1)*perPage+1, fim=Math.min(pagina*perPage,r.total);
        $('#info-paginacao').text(`Exibindo ${ini}-${fim} de ${r.total} registros`);
        renderPag(r.paginas, pagina);
    }).fail(()=>toast('Erro ao carregar transferencias.','erro'));
}

function renderPag(total,atual) {
    const ul=$('#paginacao'); ul.empty(); if(total<=1) return;
    ul.append(`<li class="page-item ${atual===1?'disabled':''}"><a class="page-link" href="#" data-p="${atual-1}">&laquo;</a></li>`);
    for(let i=1;i<=total;i++){if(i===1||i===total||Math.abs(i-atual)<=2)ul.append(`<li class="page-item ${i===atual?'active':''}"><a class="page-link" href="#" data-p="${i}">${i}</a></li>`);else if(Math.abs(i-atual)===3)ul.append('<li class="page-item disabled"><span class="page-link">...</span></li>');}
    ul.append(`<li class="page-item ${atual===total?'disabled':''}"><a class="page-link" href="#" data-p="${atual+1}">&raquo;</a></li>`);
}
$(document).on('click','#paginacao a[data-p]',function(e){e.preventDefault();carregarTabela(parseInt($(this).data('p')));});

$('#btn-novo').on('click',()=>{$('#form-transf')[0].reset();$('#form-transf [name="data_transferencia"]').val(new Date().toLocaleDateString('pt-BR'));$('#modal-transf').modal('show');});

$('#form-transf').on('submit',function(e){
    e.preventDefault();
    const dados={}; $(this).serializeArray().forEach(f=>dados[f.name]=f.value);
    if(dados.conta_origem_id===dados.conta_destino_id){toast('Conta de origem e destino nao podem ser iguais.','erro');return;}
    $.ajax({url:URLS_T.store,type:'POST',data:dados,
        success:r=>{if(r.sucesso){toast(r.mensagem,'sucesso');$('#modal-transf').modal('hide');carregarTabela(paginaAtual);}else toast(r.mensagem||'Erro.','erro');},
        error:r=>toast(r.responseJSON?.mensagem||'Erro ao transferir.','erro'),
    });
});

$(document).on('click','.btn-excluir',function(){
    const id=$(this).data('id');
    confirmarExclusao(URLS_T.destroy+id,()=>{
        $.ajax({url:URLS_T.destroy+id,type:'DELETE',
            success:r=>{toast(r.mensagem,'sucesso');carregarTabela(paginaAtual);},
            error:r=>toast(r.responseJSON?.mensagem||'Erro.','erro'),
        });
    });
});

$('#btn-filtrar').on('click',()=>carregarTabela(1));
$('#btn-limpar').on('click',()=>{$('#filtro-inicio,#filtro-fim').val('');carregarTabela(1);});

    carregarContas();
    carregarTabela();
}); // fecha $(document).ready
</script>
@endpush