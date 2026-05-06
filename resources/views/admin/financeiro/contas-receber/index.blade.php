@extends('layouts.admin.app')

@section('titulo', 'Contas a Receber')
@section('titulo_pagina', 'Contas a Receber')

@section('breadcrumb')
    <li class="breadcrumb-item">Financeiro</li>
    <li class="breadcrumb-item active">Contas a Receber</li>
@endsection

@section('conteudo')
{{-- Filtros --}}
<div class="card card-outline card-secondary mb-3">
    <div class="card-body py-2">
        <div class="row g-2 align-items-end">
            <div class="col-md-2">
                <label class="form-label small mb-1">Status</label>
                <select id="filtro-status" class="form-select form-select-sm">
                    <option value="">Todos</option>
                    <option value="pendente">Pendente</option>
                    <option value="vencido">Vencido</option>
                    <option value="recebido">Recebido</option>
                    <option value="parcialmente_recebido">Parcial</option>
                    <option value="cancelado">Cancelado</option>
                </select>
            </div>
            <div class="col-md-2">
                <label class="form-label small mb-1">De</label>
                <input type="text" id="filtro-inicio" class="form-control form-control-sm mask-data" placeholder="dd/mm/aaaa">
            </div>
            <div class="col-md-2">
                <label class="form-label small mb-1">Ate</label>
                <input type="text" id="filtro-fim" class="form-control form-control-sm mask-data" placeholder="dd/mm/aaaa">
            </div>
            <div class="col-md-3">
                <label class="form-label small mb-1">Buscar</label>
                <input type="text" id="filtro-search" class="form-control form-control-sm" placeholder="Descricao...">
            </div>
            <div class="col-md-3 d-flex gap-2">
                <button class="btn btn-primary btn-sm" id="btn-filtrar">
                    <i class="bi bi-search me-1"></i>Filtrar
                </button>
                <button class="btn btn-outline-secondary btn-sm" id="btn-limpar-filtros">
                    <i class="bi bi-x-lg me-1"></i>Limpar
                </button>
            </div>
        </div>
    </div>
</div>

{{-- Tabela --}}
<div class="card card-outline card-success">
    <div class="card-header d-flex align-items-center justify-content-between">
        <h3 class="card-title mb-0">
            <i class="bi bi-arrow-down-circle text-success me-2"></i>Contas a Receber
        </h3>
        <button class="btn btn-success btn-sm" id="btn-novo">
            <i class="bi bi-plus-lg me-1"></i>Nova Conta
        </button>
    </div>
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover mb-0" id="tabela-contas-receber">
                <thead class="table-light">
                    <tr>
                        <th style="width:50px">#</th>
                        <th>Descricao</th>
                        <th>Cliente</th>
                        <th>Vencimento</th>
                        <th class="text-end">Valor</th>
                        <th class="text-center">Status</th>
                        <th class="text-end" style="width:130px">Acoes</th>
                    </tr>
                </thead>
                <tbody id="tbody-contas-receber">
                    <tr><td colspan="7" class="text-center py-4 text-muted">
                        <i class="bi bi-hourglass-split me-2"></i>Carregando...
                    </td></tr>
                </tbody>
            </table>
        </div>
    </div>
    <div class="card-footer d-flex align-items-center justify-content-between">
        <span class="text-muted small" id="info-paginacao">0 registros</span>
        <nav><ul class="pagination pagination-sm mb-0" id="paginacao"></ul></nav>
    </div>
</div>

{{-- Modal Criar/Editar --}}
<div class="modal fade" id="modal-conta" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header bg-success text-white">
                <h5 class="modal-title"><i class="bi bi-arrow-down-circle me-2"></i><span id="modal-titulo">Nova Conta a Receber</span></h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <form id="form-conta">
                <div class="modal-body">
                    <input type="hidden" id="conta-id">
                    <div class="row g-3">
                        <div class="col-md-8">
                            <label class="form-label fw-medium">Descricao <span class="text-danger">*</span></label>
                            <input type="text" name="descricao" class="form-control" required placeholder="Ex: Venda, Servico...">
                        </div>
                        <div class="col-md-4">
                            <label class="form-label fw-medium">Valor <span class="text-danger">*</span></label>
                            <div class="input-group">
                                <span class="input-group-text">R$</span>
                                <input type="text" name="valor" class="form-control mask-moeda" required placeholder="0,00">
                            </div>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label fw-medium">Vencimento <span class="text-danger">*</span></label>
                            <input type="text" name="data_vencimento" class="form-control mask-data" required placeholder="dd/mm/aaaa">
                        </div>
                        <div class="col-md-4">
                            <label class="form-label fw-medium">Categoria</label>
                            <select name="categoria_id" class="form-select" id="sel-categoria-receber">
                                <option value="">Selecione...</option>
                            </select>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label fw-medium">Cliente</label>
                            <select name="cliente_id" class="form-select" id="sel-cliente">
                                <option value="">Selecione...</option>
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-medium">Conta Bancaria</label>
                            <select name="conta_bancaria_id" class="form-select" id="sel-conta-bancaria-receber">
                                <option value="">Selecione...</option>
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-medium">Numero do Documento</label>
                            <input type="text" name="numero_documento" class="form-control" placeholder="NF, contrato...">
                        </div>
                        <div class="col-12">
                            <label class="form-label fw-medium">Observacoes</label>
                            <textarea name="observacoes" class="form-control" rows="2"></textarea>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Cancelar</button>
                    <button type="submit" class="btn btn-success">
                        <i class="bi bi-check-lg me-1"></i>Salvar
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

{{-- Modal Receber --}}
<div class="modal fade" id="modal-receber" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header bg-success text-white">
                <h5 class="modal-title"><i class="bi bi-check-circle me-2"></i>Registrar Recebimento</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <form id="form-receber">
                <div class="modal-body">
                    <input type="hidden" id="receber-id">
                    <div class="alert alert-info small mb-3" id="info-conta-receber"></div>
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label fw-medium">Data do Recebimento <span class="text-danger">*</span></label>
                            <input type="text" name="data_recebimento" class="form-control mask-data" required placeholder="dd/mm/aaaa">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-medium">Valor Recebido <span class="text-danger">*</span></label>
                            <div class="input-group">
                                <span class="input-group-text">R$</span>
                                <input type="text" name="valor_recebido" class="form-control mask-moeda" required placeholder="0,00">
                            </div>
                        </div>
                        <div class="col-12">
                            <label class="form-label fw-medium">Conta Bancaria</label>
                            <select name="conta_bancaria_id" class="form-select" id="sel-conta-bancaria-receber2">
                                <option value="">Selecione...</option>
                            </select>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Cancelar</button>
                    <button type="submit" class="btn btn-success">
                        <i class="bi bi-check-lg me-1"></i>Confirmar Recebimento
                    </button>
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
    listar: '{{ route("admin.contas-receber.listar") }}',
    store:  '{{ route("admin.contas-receber.store") }}',
    show:   '/admin/contas-receber/',
    update: '/admin/contas-receber/',
    destroy:'/admin/contas-receber/',
    receber:'/admin/contas-receber/',
    categorias: '{{ route("admin.categorias.index") }}',
    clientes: '{{ route("admin.clientes.buscar") }}',
    contasBancarias: '{{ route("admin.contas-bancarias.listar") }}',
};

let paginaAtual = 1;
const perPage = 10;

function statusBadge(status) {
    const map = {
        pendente: ['warning','Pendente'],
        recebido: ['success','Recebido'],
        vencido: ['danger','Vencido'],
        cancelado: ['secondary','Cancelado'],
        parcialmente_recebido: ['info','Parcial'],
    };
    const [cor, label] = map[status] || ['secondary', status];
    return `<span class="badge bg-${cor}">${label}</span>`;
}

function carregarTabela(pagina = 1) {
    paginaAtual = pagina;
    const params = {
        page: pagina, per_page: perPage,
        status: $('#filtro-status').val(),
        inicio: $('#filtro-inicio').val(),
        fim: $('#filtro-fim').val(),
        search: $('#filtro-search').val(),
    };
    $.get(URLS.listar, params, function(r) {
        const tbody = $('#tbody-contas-receber');
        tbody.empty();
        if (!r.dados.length) {
            tbody.html('<tr><td colspan="7" class="text-center py-4 text-muted"><i class="bi bi-inbox fs-3 d-block mb-2"></i>Nenhum registro encontrado.</td></tr>');
            $('#info-paginacao').text('0 registros');
            $('#paginacao').empty();
            return;
        }
        r.dados.forEach(function(c) {
            const venc = c.data_vencimento ? c.data_vencimento.split('-').reverse().join('/') : '-';
            const valor = 'R$ ' + parseFloat(c.valor).toLocaleString('pt-BR', {minimumFractionDigits:2});
            const finalizado = c.status === 'recebido' || c.status === 'cancelado';
            tbody.append(`
                <tr>
                    <td class="text-muted small">${c.id}</td>
                    <td>
                        <div class="fw-medium">${c.descricao}</div>
                        ${c.categoria ? '<small class="text-muted"><i class="bi bi-tag me-1"></i>'+c.categoria.nome+'</small>' : ''}
                    </td>
                    <td>${c.cliente ? c.cliente.nome : '<span class="text-muted">-</span>'}</td>
                    <td>${venc}</td>
                    <td class="text-end fw-medium text-success">${valor}</td>
                    <td class="text-center">${statusBadge(c.status)}</td>
                    <td class="text-end">
                        <div class="btn-group btn-group-sm">
                            ${!finalizado ? `<button class="btn btn-outline-success btn-receber" data-id="${c.id}" title="Receber"><i class="bi bi-check-circle"></i></button>` : ''}
                            <button class="btn btn-outline-primary btn-editar" data-id="${c.id}" title="Editar"><i class="bi bi-pencil"></i></button>
                            <button class="btn btn-outline-danger btn-excluir" data-id="${c.id}" title="Excluir"><i class="bi bi-trash"></i></button>
                        </div>
                    </td>
                </tr>
            `);
        });
        const inicio = (pagina - 1) * perPage + 1;
        const fim = Math.min(pagina * perPage, r.total);
        $('#info-paginacao').text(`Exibindo ${inicio}-${fim} de ${r.total} registros`);
        renderPaginacao(r.paginas, pagina);
    }).fail(() => toast('Erro ao carregar dados.', 'erro'));
}

function renderPaginacao(totalPaginas, atual) {
    const ul = $('#paginacao');
    ul.empty();
    if (totalPaginas <= 1) return;
    ul.append(`<li class="page-item ${atual===1?'disabled':''}"><a class="page-link" href="#" data-p="${atual-1}">&laquo;</a></li>`);
    for (let i = 1; i <= totalPaginas; i++) {
        if (i === 1 || i === totalPaginas || Math.abs(i - atual) <= 2) {
            ul.append(`<li class="page-item ${i===atual?'active':''}"><a class="page-link" href="#" data-p="${i}">${i}</a></li>`);
        } else if (Math.abs(i - atual) === 3) {
            ul.append('<li class="page-item disabled"><span class="page-link">...</span></li>');
        }
    }
    ul.append(`<li class="page-item ${atual===totalPaginas?'disabled':''}"><a class="page-link" href="#" data-p="${atual+1}">&raquo;</a></li>`);
}

$(document).on('click', '#paginacao a[data-p]', function(e) {
    e.preventDefault();
    carregarTabela(parseInt($(this).data('p')));
});

function carregarSelects() {
    $.get(URLS.categorias, r => {
        if (!r.sucesso) return;
        const opts = r.dados.filter(c => c.tipo !== 'despesa').map(c => `<option value="${c.id}">${c.nome}</option>`).join('');
        $('#sel-categoria-receber').append(opts);
    });
    $.get(URLS.clientes, r => {
        if (!r.sucesso) return;
        const opts = r.dados.map(c => `<option value="${c.id}">${c.nome}</option>`).join('');
        $('#sel-cliente').append(opts);
    });
    $.get(URLS.contasBancarias, r => {
        if (!r.sucesso) return;
        const opts = r.dados.map(c => `<option value="${c.id}">${c.nome}</option>`).join('');
        $('#sel-conta-bancaria-receber, #sel-conta-bancaria-receber2').append(opts);
    });
}

$('#btn-novo').on('click', function() {
    $('#modal-titulo').text('Nova Conta a Receber');
    $('#conta-id').val('');
    $('#form-conta')[0].reset();
    $('#modal-conta').modal('show');
});

$(document).on('click', '.btn-editar', function() {
    const id = $(this).data('id');
    $.get(URLS.show + id, r => {
        if (!r.sucesso) return;
        const c = r.dado;
        $('#modal-titulo').text('Editar Conta a Receber');
        $('#conta-id').val(c.id);
        const form = $('#form-conta');
        form.find('[name="descricao"]').val(c.descricao);
        form.find('[name="valor"]').val(parseFloat(c.valor).toLocaleString('pt-BR',{minimumFractionDigits:2}));
        form.find('[name="data_vencimento"]').val(c.data_vencimento ? c.data_vencimento.split('-').reverse().join('/') : '');
        form.find('[name="categoria_id"]').val(c.categoria_id);
        form.find('[name="cliente_id"]').val(c.cliente_id);
        form.find('[name="conta_bancaria_id"]').val(c.conta_bancaria_id);
        form.find('[name="numero_documento"]').val(c.numero_documento);
        form.find('[name="observacoes"]').val(c.observacoes);
        $('#modal-conta').modal('show');
    });
});

$('#form-conta').on('submit', function(e) {
    e.preventDefault();
    const id = $('#conta-id').val();
    const dados = {};
    $(this).serializeArray().forEach(f => dados[f.name] = f.value);
    $.ajax({
        url: id ? URLS.update + id : URLS.store,
        type: id ? 'PUT' : 'POST',
        data: dados,
        success: r => {
            if (r.sucesso) { toast(r.mensagem, 'sucesso'); $('#modal-conta').modal('hide'); carregarTabela(paginaAtual); }
            else toast(r.mensagem || 'Erro ao salvar.', 'erro');
        },
        error: r => toast(r.responseJSON?.mensagem || 'Erro ao salvar.', 'erro'),
    });
});

$(document).on('click', '.btn-receber', function() {
    const id = $(this).data('id');
    $.get(URLS.show + id, r => {
        if (!r.sucesso) return;
        const c = r.dado;
        $('#receber-id').val(c.id);
        $('#info-conta-receber').html(`<strong>${c.descricao}</strong> &mdash; Valor: <strong>R$ ${parseFloat(c.valor).toLocaleString('pt-BR',{minimumFractionDigits:2})}</strong>`);
        $('#form-receber [name="data_recebimento"]').val(new Date().toLocaleDateString('pt-BR'));
        $('#form-receber [name="valor_recebido"]').val(parseFloat(c.valor).toLocaleString('pt-BR',{minimumFractionDigits:2}));
        $('#form-receber [name="conta_bancaria_id"]').val(c.conta_bancaria_id);
        $('#modal-receber').modal('show');
    });
});

$('#form-receber').on('submit', function(e) {
    e.preventDefault();
    const id = $('#receber-id').val();
    const dados = {};
    $(this).serializeArray().forEach(f => dados[f.name] = f.value);
    $.ajax({
        url: URLS.receber + id + '/receber',
        type: 'POST',
        data: dados,
        success: r => {
            if (r.sucesso) { toast(r.mensagem, 'sucesso'); $('#modal-receber').modal('hide'); carregarTabela(paginaAtual); }
            else toast(r.mensagem || 'Erro.', 'erro');
        },
        error: r => toast(r.responseJSON?.mensagem || 'Erro ao registrar recebimento.', 'erro'),
    });
});

$(document).on('click', '.btn-excluir', function() {
    const id = $(this).data('id');
    confirmarExclusao(URLS.destroy + id, () => {
        $.ajax({ url: URLS.destroy + id, type: 'DELETE',
            success: r => { toast(r.mensagem, 'sucesso'); carregarTabela(paginaAtual); },
            error: r => toast(r.responseJSON?.mensagem || 'Erro ao excluir.', 'erro'),
        });
    });
});

$('#btn-filtrar').on('click', () => carregarTabela(1));
$('#btn-limpar-filtros').on('click', () => { $('#filtro-status, #filtro-inicio, #filtro-fim, #filtro-search').val(''); carregarTabela(1); });
$('#filtro-search').on('keypress', e => { if (e.which === 13) carregarTabela(1); });

    carregarSelects();
    carregarTabela();
}); // fecha $(document).ready
</script>
@endpush
