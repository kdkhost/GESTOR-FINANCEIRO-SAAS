@extends('layouts.admin.app')

@section('titulo', 'Contas a Pagar')
@section('titulo_pagina', 'Contas a Pagar')

@section('breadcrumb')
    <li class="breadcrumb-item">Financeiro</li>
    <li class="breadcrumb-item active">Contas a Pagar</li>
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
                    <option value="pago">Pago</option>
                    <option value="parcialmente_pago">Parcial</option>
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
<div class="card card-outline card-primary">
    <div class="card-header d-flex align-items-center justify-content-between">
        <h3 class="card-title mb-0">
            <i class="bi bi-arrow-up-circle text-danger me-2"></i>Contas a Pagar
        </h3>
        <button class="btn btn-primary btn-sm" id="btn-novo">
            <i class="bi bi-plus-lg me-1"></i>Nova Conta
        </button>
    </div>
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover mb-0" id="tabela-contas-pagar">
                <thead class="table-light">
                    <tr>
                        <th style="width:50px">#</th>
                        <th>Descricao</th>
                        <th>Fornecedor</th>
                        <th>Vencimento</th>
                        <th class="text-end">Valor</th>
                        <th class="text-center">Status</th>
                        <th class="text-end" style="width:130px">Acoes</th>
                    </tr>
                </thead>
                <tbody id="tbody-contas-pagar">
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
            <div class="modal-header bg-primary text-white">
                <h5 class="modal-title"><i class="bi bi-arrow-up-circle me-2"></i><span id="modal-titulo">Nova Conta a Pagar</span></h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <form id="form-conta">
                <div class="modal-body">
                    <input type="hidden" id="conta-id">
                    <div class="row g-3">
                        <div class="col-md-8">
                            <label class="form-label fw-medium">Descricao <span class="text-danger">*</span></label>
                            <input type="text" name="descricao" class="form-control" required placeholder="Ex: Aluguel, Energia...">
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
                            <select name="categoria_id" class="form-select" id="sel-categoria-pagar">
                                <option value="">Selecione...</option>
                            </select>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label fw-medium">Fornecedor</label>
                            <select name="fornecedor_id" class="form-select" id="sel-fornecedor">
                                <option value="">Selecione...</option>
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-medium">Conta Bancaria</label>
                            <select name="conta_bancaria_id" class="form-select" id="sel-conta-bancaria-pagar">
                                <option value="">Selecione...</option>
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-medium">Numero do Documento</label>
                            <input type="text" name="numero_documento" class="form-control" placeholder="NF, boleto...">
                        </div>
                        <div class="col-12">
                            <label class="form-label fw-medium">Observacoes</label>
                            <textarea name="observacoes" class="form-control" rows="2" placeholder="Observacoes opcionais..."></textarea>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Cancelar</button>
                    <button type="submit" class="btn btn-primary">
                        <i class="bi bi-check-lg me-1"></i>Salvar
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

{{-- Modal Pagar --}}
<div class="modal fade" id="modal-pagar" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header bg-success text-white">
                <h5 class="modal-title"><i class="bi bi-check-circle me-2"></i>Registrar Pagamento</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <form id="form-pagar">
                <div class="modal-body">
                    <input type="hidden" id="pagar-id">
                    <div class="alert alert-info small mb-3" id="info-conta-pagar"></div>
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label fw-medium">Data do Pagamento <span class="text-danger">*</span></label>
                            <input type="text" name="data_pagamento" class="form-control mask-data" required placeholder="dd/mm/aaaa">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-medium">Valor Pago <span class="text-danger">*</span></label>
                            <div class="input-group">
                                <span class="input-group-text">R$</span>
                                <input type="text" name="valor_pago" class="form-control mask-moeda" required placeholder="0,00">
                            </div>
                        </div>
                        <div class="col-12">
                            <label class="form-label fw-medium">Conta Bancaria</label>
                            <select name="conta_bancaria_id" class="form-select" id="sel-conta-bancaria-pagar2">
                                <option value="">Selecione...</option>
                            </select>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Cancelar</button>
                    <button type="submit" class="btn btn-success">
                        <i class="bi bi-check-lg me-1"></i>Confirmar Pagamento
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
    listar:  '{{ route("admin.contas-pagar.listar") }}',
    store:   '{{ route("admin.contas-pagar.store") }}',
    show:    '/admin/contas-pagar/',
    update:  '/admin/contas-pagar/',
    destroy: '/admin/contas-pagar/',
    pagar:   '/admin/contas-pagar/',
    categorias: '{{ route("admin.categorias.index") }}',
    fornecedores: '{{ route("admin.fornecedores.buscar") }}',
    contasBancarias: '{{ route("admin.contas-bancarias.listar") }}',
};

let paginaAtual = 1;
const perPage = 10;

function statusBadge(status) {
    const map = {
        pendente: ['warning','Pendente'],
        pago: ['success','Pago'],
        vencido: ['danger','Vencido'],
        cancelado: ['secondary','Cancelado'],
        parcialmente_pago: ['info','Parcial'],
    };
    const [cor, label] = map[status] || ['secondary', status];
    return `<span class="badge bg-${cor}">${label}</span>`;
}

function carregarTabela(pagina = 1) {
    paginaAtual = pagina;
    const params = {
        page: pagina,
        per_page: perPage,
        status: $('#filtro-status').val(),
        inicio: $('#filtro-inicio').val(),
        fim: $('#filtro-fim').val(),
        search: $('#filtro-search').val(),
    };

    $.get(URLS.listar, params, function(r) {
        if (!r.sucesso) { toast('Erro ao carregar dados.', 'erro'); return; }
        const tbody = $('#tbody-contas-pagar');
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
            const pago = c.status === 'pago' || c.status === 'cancelado';
            tbody.append(`
                <tr>
                    <td class="text-muted small">${c.id}</td>
                    <td>
                        <div class="fw-medium">${c.descricao}</div>
                        ${c.categoria ? '<small class="text-muted"><i class="bi bi-tag me-1"></i>'+c.categoria.nome+'</small>' : ''}
                    </td>
                    <td>${c.fornecedor ? c.fornecedor.nome : '<span class="text-muted">-</span>'}</td>
                    <td>${venc}</td>
                    <td class="text-end fw-medium">${valor}</td>
                    <td class="text-center">${statusBadge(c.status)}</td>
                    <td class="text-end">
                        <div class="btn-group btn-group-sm">
                            ${!pago ? `<button class="btn btn-outline-success btn-pagar" data-id="${c.id}" title="Pagar"><i class="bi bi-check-circle"></i></button>` : ''}
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
    }).fail(() => toast('Erro ao carregar contas a pagar.', 'erro'));
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

// Carregar selects
function carregarSelects() {
    $.get(URLS.categorias, r => {
        if (!r.sucesso) return;
        const opts = r.dados.filter(c => c.tipo !== 'receita').map(c => `<option value="${c.id}">${c.nome}</option>`).join('');
        $('#sel-categoria-pagar').append(opts);
    });
    $.get(URLS.fornecedores, r => {
        if (!r.sucesso) return;
        const opts = r.dados.map(f => `<option value="${f.id}">${f.nome}</option>`).join('');
        $('#sel-fornecedor').append(opts);
    });
    $.get(URLS.contasBancarias, r => {
        if (!r.sucesso) return;
        const opts = r.dados.map(c => `<option value="${c.id}">${c.nome}</option>`).join('');
        $('#sel-conta-bancaria-pagar, #sel-conta-bancaria-pagar2').append(opts);
    });
}

// Novo
$('#btn-novo').on('click', function() {
    $('#modal-titulo').text('Nova Conta a Pagar');
    $('#conta-id').val('');
    $('#form-conta')[0].reset();
    $('#modal-conta').modal('show');
});

// Editar
$(document).on('click', '.btn-editar', function() {
    const id = $(this).data('id');
    $.get(URLS.show + id, r => {
        if (!r.sucesso) { toast('Erro ao carregar dados.', 'erro'); return; }
        const c = r.dado;
        $('#modal-titulo').text('Editar Conta a Pagar');
        $('#conta-id').val(c.id);
        const form = $('#form-conta');
        form.find('[name="descricao"]').val(c.descricao);
        form.find('[name="valor"]').val(parseFloat(c.valor).toLocaleString('pt-BR', {minimumFractionDigits:2}));
        form.find('[name="data_vencimento"]').val(c.data_vencimento ? c.data_vencimento.split('-').reverse().join('/') : '');
        form.find('[name="categoria_id"]').val(c.categoria_id);
        form.find('[name="fornecedor_id"]').val(c.fornecedor_id);
        form.find('[name="conta_bancaria_id"]').val(c.conta_bancaria_id);
        form.find('[name="numero_documento"]').val(c.numero_documento);
        form.find('[name="observacoes"]').val(c.observacoes);
        $('#modal-conta').modal('show');
    });
});

// Salvar
$('#form-conta').on('submit', function(e) {
    e.preventDefault();
    const id = $('#conta-id').val();
    const dados = {};
    $(this).serializeArray().forEach(f => dados[f.name] = f.value);

    const url  = id ? URLS.update + id : URLS.store;
    const type = id ? 'PUT' : 'POST';

    $.ajax({ url, type, data: dados,
        success: r => {
            if (r.sucesso) {
                toast(r.mensagem, 'sucesso');
                $('#modal-conta').modal('hide');
                carregarTabela(paginaAtual);
            } else toast(r.mensagem || 'Erro ao salvar.', 'erro');
        },
        error: r => {
            const erros = r.responseJSON?.errors;
            if (erros) toast(Object.values(erros).flat().join(' | '), 'erro');
            else toast(r.responseJSON?.mensagem || 'Erro ao salvar.', 'erro');
        },
    });
});

// Pagar
$(document).on('click', '.btn-pagar', function() {
    const id = $(this).data('id');
    $.get(URLS.show + id, r => {
        if (!r.sucesso) return;
        const c = r.dado;
        $('#pagar-id').val(c.id);
        $('#info-conta-pagar').html(`<strong>${c.descricao}</strong> &mdash; Valor: <strong>R$ ${parseFloat(c.valor).toLocaleString('pt-BR',{minimumFractionDigits:2})}</strong>`);
        $('#form-pagar [name="data_pagamento"]').val(new Date().toLocaleDateString('pt-BR'));
        $('#form-pagar [name="valor_pago"]').val(parseFloat(c.valor).toLocaleString('pt-BR',{minimumFractionDigits:2}));
        $('#form-pagar [name="conta_bancaria_id"]').val(c.conta_bancaria_id);
        $('#modal-pagar').modal('show');
    });
});

$('#form-pagar').on('submit', function(e) {
    e.preventDefault();
    const id = $('#pagar-id').val();
    const dados = {};
    $(this).serializeArray().forEach(f => dados[f.name] = f.value);
    $.ajax({
        url: URLS.pagar + id + '/pagar',
        type: 'POST',
        data: dados,
        success: r => {
            if (r.sucesso) {
                toast(r.mensagem, 'sucesso');
                $('#modal-pagar').modal('hide');
                carregarTabela(paginaAtual);
            } else toast(r.mensagem || 'Erro ao registrar pagamento.', 'erro');
        },
        error: r => toast(r.responseJSON?.mensagem || 'Erro ao registrar pagamento.', 'erro'),
    });
});

// Excluir
$(document).on('click', '.btn-excluir', function() {
    const id = $(this).data('id');
    confirmarExclusao(URLS.destroy + id, () => {
        $.ajax({ url: URLS.destroy + id, type: 'DELETE',
            success: r => { toast(r.mensagem, 'sucesso'); carregarTabela(paginaAtual); },
            error: r => toast(r.responseJSON?.mensagem || 'Erro ao excluir.', 'erro'),
        });
    });
});

// Filtros
$('#btn-filtrar').on('click', () => carregarTabela(1));
$('#btn-limpar-filtros').on('click', () => {
    $('#filtro-status, #filtro-inicio, #filtro-fim, #filtro-search').val('');
    carregarTabela(1);
});
$('#filtro-search').on('keypress', e => { if (e.which === 13) carregarTabela(1); });

// Init
carregarSelects();
    carregarTabela();
}); // fecha $(document).ready
</script>
@endpush
