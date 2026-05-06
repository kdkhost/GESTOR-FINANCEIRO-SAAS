@extends('layouts.admin.app')

@section('titulo', 'Receitas')
@section('titulo_pagina', 'Receitas')

@section('breadcrumb')
    <li class="breadcrumb-item">Financeiro</li>
    <li class="breadcrumb-item active">Receitas</li>
@endsection

@section('conteudo')
<div class="card card-outline card-success mb-3">
    <div class="card-body py-2">
        <div class="row g-2 align-items-end">
            <div class="col-md-2">
                <label class="form-label small mb-1">De</label>
                <input type="text" id="filtro-inicio" class="form-control form-control-sm mask-data" placeholder="dd/mm/aaaa">
            </div>
            <div class="col-md-2">
                <label class="form-label small mb-1">Ate</label>
                <input type="text" id="filtro-fim" class="form-control form-control-sm mask-data" placeholder="dd/mm/aaaa">
            </div>
            <div class="col-md-3">
                <label class="form-label small mb-1">Categoria</label>
                <select id="filtro-categoria" class="form-select form-select-sm">
                    <option value="">Todas</option>
                </select>
            </div>
            <div class="col-md-3">
                <label class="form-label small mb-1">Buscar</label>
                <input type="text" id="filtro-search" class="form-control form-control-sm" placeholder="Descricao...">
            </div>
            <div class="col-md-2 d-flex gap-2">
                <button class="btn btn-success btn-sm" id="btn-filtrar"><i class="bi bi-search me-1"></i>Filtrar</button>
                <button class="btn btn-outline-secondary btn-sm" id="btn-limpar"><i class="bi bi-x-lg"></i></button>
            </div>
        </div>
    </div>
</div>

<div class="card card-outline card-success">
    <div class="card-header d-flex align-items-center justify-content-between">
        <h3 class="card-title mb-0"><i class="bi bi-plus-circle text-success me-2"></i>Receitas</h3>
        <button class="btn btn-success btn-sm" id="btn-novo"><i class="bi bi-plus-lg me-1"></i>Nova Receita</button>
    </div>
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover mb-0">
                <thead class="table-light">
                    <tr>
                        <th>#</th>
                        <th>Descricao</th>
                        <th>Categoria</th>
                        <th>Data</th>
                        <th class="text-end">Valor</th>
                        <th class="text-end" style="width:100px">Acoes</th>
                    </tr>
                </thead>
                <tbody id="tbody-receitas">
                    <tr><td colspan="6" class="text-center py-4 text-muted"><i class="bi bi-hourglass-split me-2"></i>Carregando...</td></tr>
                </tbody>
            </table>
        </div>
    </div>
    <div class="card-footer d-flex align-items-center justify-content-between">
        <span class="text-muted small" id="info-paginacao">0 registros</span>
        <nav><ul class="pagination pagination-sm mb-0" id="paginacao"></ul></nav>
    </div>
</div>

{{-- Modal --}}
<div class="modal fade" id="modal-receita" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header bg-success text-white">
                <h5 class="modal-title"><i class="bi bi-plus-circle me-2"></i><span id="modal-titulo">Nova Receita</span></h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <form id="form-receita">
                <div class="modal-body">
                    <input type="hidden" id="receita-id">
                    <div class="row g-3">
                        <div class="col-md-8">
                            <label class="form-label fw-medium">Descricao <span class="text-danger">*</span></label>
                            <input type="text" name="descricao" class="form-control" required placeholder="Ex: Venda de produto...">
                        </div>
                        <div class="col-md-4">
                            <label class="form-label fw-medium">Valor <span class="text-danger">*</span></label>
                            <div class="input-group">
                                <span class="input-group-text">R$</span>
                                <input type="text" name="valor" class="form-control mask-moeda" required placeholder="0,00">
                            </div>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label fw-medium">Data <span class="text-danger">*</span></label>
                            <input type="text" name="data_receita" class="form-control mask-data" required placeholder="dd/mm/aaaa">
                        </div>
                        <div class="col-md-4">
                            <label class="form-label fw-medium">Categoria</label>
                            <select name="categoria_id" class="form-select" id="sel-cat-receita">
                                <option value="">Selecione...</option>
                            </select>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label fw-medium">Conta Bancaria</label>
                            <select name="conta_bancaria_id" class="form-select" id="sel-cb-receita">
                                <option value="">Selecione...</option>
                            </select>
                        </div>
                        <div class="col-12">
                            <label class="form-label fw-medium">Observacoes</label>
                            <textarea name="observacoes" class="form-control" rows="2"></textarea>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Cancelar</button>
                    <button type="submit" class="btn btn-success"><i class="bi bi-check-lg me-1"></i>Salvar</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
$(document).ready(function() {
const URLS = {
    listar: '{{ route("admin.receitas.listar") }}',
    store:  '{{ route("admin.receitas.store") }}',
    show:   '/admin/receitas/',
    update: '/admin/receitas/',
    destroy:'/admin/receitas/',
    categorias: '{{ route("admin.categorias.index") }}',
    contasBancarias: '{{ route("admin.contas-bancarias.listar") }}',
};
let paginaAtual = 1;
const perPage = 10;

function carregarTabela(pagina = 1) {
    paginaAtual = pagina;
    $.get(URLS.listar, {
        page: pagina, per_page: perPage,
        inicio: $('#filtro-inicio').val(), fim: $('#filtro-fim').val(),
        categoria_id: $('#filtro-categoria').val(), search: $('#filtro-search').val(),
    }, function(r) {
        const tbody = $('#tbody-receitas');
        tbody.empty();
        if (!r.dados.length) {
            tbody.html('<tr><td colspan="6" class="text-center py-4 text-muted"><i class="bi bi-inbox fs-3 d-block mb-2"></i>Nenhum registro encontrado.</td></tr>');
            $('#info-paginacao').text('0 registros'); $('#paginacao').empty(); return;
        }
        r.dados.forEach(c => {
            const data = c.data_receita ? c.data_receita.split('-').reverse().join('/') : '-';
            const valor = 'R$ ' + parseFloat(c.valor).toLocaleString('pt-BR',{minimumFractionDigits:2});
            tbody.append(`<tr>
                <td class="text-muted small">${c.id}</td>
                <td><div class="fw-medium">${c.descricao}</div></td>
                <td>${c.categoria ? '<span class="badge bg-success-subtle text-success">'+c.categoria.nome+'</span>' : '<span class="text-muted">-</span>'}</td>
                <td>${data}</td>
                <td class="text-end fw-medium text-success">${valor}</td>
                <td class="text-end">
                    <div class="btn-group btn-group-sm">
                        <button class="btn btn-outline-primary btn-editar" data-id="${c.id}"><i class="bi bi-pencil"></i></button>
                        <button class="btn btn-outline-danger btn-excluir" data-id="${c.id}"><i class="bi bi-trash"></i></button>
                    </div>
                </td>
            </tr>`);
        });
        const ini = (pagina-1)*perPage+1, fim = Math.min(pagina*perPage, r.total);
        $('#info-paginacao').text(`Exibindo ${ini}-${fim} de ${r.total} registros`);
        renderPaginacao(r.paginas, pagina);
    }).fail(() => toast('Erro ao carregar receitas.', 'erro'));
}

function renderPaginacao(total, atual) {
    const ul = $('#paginacao'); ul.empty();
    if (total <= 1) return;
    ul.append(`<li class="page-item ${atual===1?'disabled':''}"><a class="page-link" href="#" data-p="${atual-1}">&laquo;</a></li>`);
    for (let i=1; i<=total; i++) {
        if (i===1||i===total||Math.abs(i-atual)<=2) ul.append(`<li class="page-item ${i===atual?'active':''}"><a class="page-link" href="#" data-p="${i}">${i}</a></li>`);
        else if (Math.abs(i-atual)===3) ul.append('<li class="page-item disabled"><span class="page-link">...</span></li>');
    }
    ul.append(`<li class="page-item ${atual===total?'disabled':''}"><a class="page-link" href="#" data-p="${atual+1}">&raquo;</a></li>`);
}
$(document).on('click', '#paginacao a[data-p]', function(e) { e.preventDefault(); carregarTabela(parseInt($(this).data('p'))); });

function carregarSelects() {
    $.get(URLS.categorias, r => {
        if (!r.sucesso) return;
        const opts = r.dados.filter(c=>c.tipo!=='despesa').map(c=>`<option value="${c.id}">${c.nome}</option>`).join('');
        $('#sel-cat-receita, #filtro-categoria').append(opts);
    });
    $.get(URLS.contasBancarias, r => {
        if (!r.sucesso) return;
        $('#sel-cb-receita').append(r.dados.map(c=>`<option value="${c.id}">${c.nome}</option>`).join(''));
    });
}

$('#btn-novo').on('click', () => { $('#modal-titulo').text('Nova Receita'); $('#receita-id').val(''); $('#form-receita')[0].reset(); $('#modal-receita').modal('show'); });

$(document).on('click', '.btn-editar', function() {
    $.get(URLS.show + $(this).data('id'), r => {
        if (!r.sucesso) return;
        const c = r.dado;
        $('#modal-titulo').text('Editar Receita'); $('#receita-id').val(c.id);
        const f = $('#form-receita');
        f.find('[name="descricao"]').val(c.descricao);
        f.find('[name="valor"]').val(parseFloat(c.valor).toLocaleString('pt-BR',{minimumFractionDigits:2}));
        f.find('[name="data_receita"]').val(c.data_receita ? c.data_receita.split('-').reverse().join('/') : '');
        f.find('[name="categoria_id"]').val(c.categoria_id);
        f.find('[name="conta_bancaria_id"]').val(c.conta_bancaria_id);
        f.find('[name="observacoes"]').val(c.observacoes);
        $('#modal-receita').modal('show');
    });
});

$('#form-receita').on('submit', function(e) {
    e.preventDefault();
    const id = $('#receita-id').val();
    const dados = {};
    $(this).serializeArray().forEach(f => dados[f.name] = f.value);
    $.ajax({
        url: id ? URLS.update + id : URLS.store,
        type: id ? 'PUT' : 'POST',
        data: dados,
        success: r => { if (r.sucesso) { toast(r.mensagem,'sucesso'); $('#modal-receita').modal('hide'); carregarTabela(paginaAtual); } else toast(r.mensagem||'Erro.','erro'); },
        error: r => toast(r.responseJSON?.mensagem||'Erro ao salvar.','erro'),
    });
});

$(document).on('click', '.btn-excluir', function() {
    const id = $(this).data('id');
    confirmarExclusao(URLS.destroy+id, () => {
        $.ajax({ url: URLS.destroy+id, type: 'DELETE',
            success: r => { toast(r.mensagem,'sucesso'); carregarTabela(paginaAtual); },
            error: r => toast(r.responseJSON?.mensagem||'Erro.','erro'),
        });
    });
});

$('#btn-filtrar').on('click', () => carregarTabela(1));
$('#btn-limpar').on('click', () => { $('#filtro-inicio,#filtro-fim,#filtro-categoria,#filtro-search').val(''); carregarTabela(1); });
$('#filtro-search').on('keypress', e => { if (e.which===13) carregarTabela(1); });

    carregarSelects();
    carregarTabela();
}); // fecha $(document).ready
</script>
@endpush
