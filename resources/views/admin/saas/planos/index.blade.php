@extends('layouts.admin.app')

@section('titulo', 'SaaS')
@section('titulo_pagina', 'Planos do SaaS')

@section('breadcrumb')
    <li class="breadcrumb-item">Administracao</li>
    <li class="breadcrumb-item">SaaS</li>
    <li class="breadcrumb-item active">Planos</li>
@endsection

@section('conteudo')
<div class="card card-outline card-secondary mb-3">
    <div class="card-body py-2">
        <div class="row g-2 align-items-end">
            <div class="col-md-5">
                <label class="form-label small mb-1">Buscar</label>
                <input type="text" id="filtro-search" class="form-control form-control-sm" placeholder="Nome ou slug...">
            </div>
            <div class="col-md-3 d-flex gap-2">
                <button class="btn btn-primary btn-sm" id="btn-filtrar"><i class="bi bi-search me-1"></i>Filtrar</button>
                <button class="btn btn-outline-secondary btn-sm" id="btn-limpar"><i class="bi bi-x-lg"></i></button>
            </div>
        </div>
    </div>
</div>

<div class="card card-outline card-primary">
    <div class="card-header d-flex align-items-center justify-content-between">
        <h3 class="card-title mb-0"><i class="bi bi-layers text-primary me-2"></i>Planos</h3>
        <button class="btn btn-primary btn-sm" id="btn-novo"><i class="bi bi-plus-lg me-1"></i>Novo Plano</button>
    </div>
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover mb-0">
                <thead class="table-light">
                    <tr>
                        <th>#</th>
                        <th>Plano</th>
                        <th>Mensal</th>
                        <th>Anual</th>
                        <th class="text-center">Ativo</th>
                        <th class="text-end" style="width: 110px;">Acoes</th>
                    </tr>
                </thead>
                <tbody id="tbody-planos">
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

<div class="modal fade" id="modal-plano" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header bg-primary text-white">
                <h5 class="modal-title"><i class="bi bi-layers me-2"></i><span id="modal-titulo">Novo Plano</span></h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <form id="form-plano">
                <div class="modal-body">
                    <input type="hidden" id="plano-id">
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label fw-medium">Nome</label>
                            <input type="text" class="form-control" name="nome" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-medium">Slug</label>
                            <input type="text" class="form-control" name="slug" required placeholder="ex: pro, premium, empresarial">
                        </div>
                        <div class="col-12">
                            <label class="form-label fw-medium">Descricao</label>
                            <textarea class="form-control" rows="3" name="descricao" placeholder="Texto curto para pagina de venda/checkout"></textarea>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label fw-medium">Valor mensal</label>
                            <input type="text" class="form-control" name="valor_mensal" required placeholder="0,00">
                        </div>
                        <div class="col-md-4">
                            <label class="form-label fw-medium">Valor anual</label>
                            <input type="text" class="form-control" name="valor_anual" placeholder="0,00">
                        </div>
                        <div class="col-md-4">
                            <label class="form-label fw-medium">Ordem</label>
                            <input type="number" class="form-control" name="ordem" value="0" min="0" max="9999">
                        </div>
                        <div class="col-12">
                            <label class="form-label fw-medium">Limites (uma linha por item)</label>
                            <textarea class="form-control" rows="4" name="limites" placeholder="usuarios=3\nclientes=500\narmazenamento_gb=5"></textarea>
                            <div class="form-text">Formato: chave=valor</div>
                        </div>
                        <div class="col-12">
                            <div class="form-check form-switch">
                                <input class="form-check-input" type="checkbox" role="switch" id="plano-ativo" checked>
                                <label class="form-check-label" for="plano-ativo">Ativo</label>
                            </div>
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
const URLS_P = {
    listar: '{{ route("admin.saas.planos.listar") }}',
    store:  '{{ route("admin.saas.planos.store") }}',
    show:   '{{ url("/admin/saas/planos") }}/',
    update: '{{ url("/admin/saas/planos") }}/',
    destroy:'{{ url("/admin/saas/planos") }}/',
};
let paginaAtualP = 1;
const perPageP = 10;

function toNumberBr(v) {
    if (!v) return '';
    return String(v).replace('.', ',');
}
function parseMoneyBr(v) {
    if (v === null || v === undefined) return '';
    const s = String(v).trim();
    if (s === '') return '';
    const norm = s.replace(/\./g, '').replace(',', '.').replace(/[^0-9.]/g, '');
    return norm;
}

function carregarPlanos(pagina = 1) {
    paginaAtualP = pagina;
    $.get(URLS_P.listar, { page: pagina, per_page: perPageP, search: $('#filtro-search').val() }, function (r) {
        const tbody = $('#tbody-planos').empty();
        if (!r.sucesso || !r.dados.length) {
            tbody.html('<tr><td colspan="6" class="text-center py-4 text-muted"><i class="bi bi-inbox fs-3 d-block mb-2"></i>Nenhum plano encontrado.</td></tr>');
            $('#info-paginacao').text('0 registros');
            $('#paginacao').empty();
            return;
        }
        r.dados.forEach(p => {
            tbody.append(`<tr>
                <td class="text-muted small">${p.id}</td>
                <td><div class="fw-medium">${p.nome}</div><small class="text-muted">${p.slug}</small></td>
                <td>R$ ${toNumberBr(p.valor_mensal)}</td>
                <td>${p.valor_anual ? 'R$ ' + toNumberBr(p.valor_anual) : '<span class="text-muted">-</span>'}</td>
                <td class="text-center"><span class="badge bg-${p.ativo ? 'success' : 'secondary'}">${p.ativo ? 'Sim' : 'Nao'}</span></td>
                <td class="text-end"><div class="btn-group btn-group-sm">
                    <button class="btn btn-outline-primary btn-editar" data-id="${p.id}"><i class="bi bi-pencil"></i></button>
                    <button class="btn btn-outline-danger btn-excluir" data-id="${p.id}"><i class="bi bi-trash"></i></button>
                </div></td>
            </tr>`);
        });
        const ini = (pagina - 1) * perPageP + 1;
        const fim = Math.min(pagina * perPageP, r.total);
        $('#info-paginacao').text(`Exibindo ${ini}-${fim} de ${r.total} registros`);
        renderPagP(r.paginas, pagina);
    }).fail(() => toast('Erro ao carregar planos.', 'erro'));
}

function renderPagP(total, atual) {
    const ul = $('#paginacao').empty();
    if (total <= 1) return;
    ul.append(`<li class="page-item ${atual === 1 ? 'disabled' : ''}"><a class="page-link" href="#" data-p="${atual - 1}">&laquo;</a></li>`);
    for (let i = 1; i <= total; i++) {
        if (i === 1 || i === total || Math.abs(i - atual) <= 2) {
            ul.append(`<li class="page-item ${i === atual ? 'active' : ''}"><a class="page-link" href="#" data-p="${i}">${i}</a></li>`);
        } else if (Math.abs(i - atual) === 3) {
            ul.append('<li class="page-item disabled"><span class="page-link">...</span></li>');
        }
    }
    ul.append(`<li class="page-item ${atual === total ? 'disabled' : ''}"><a class="page-link" href="#" data-p="${atual + 1}">&raquo;</a></li>`);
}

$(document).on('click', '#paginacao a[data-p]', function (e) {
    e.preventDefault();
    carregarPlanos(parseInt($(this).data('p')));
});

$('#btn-novo').on('click', () => {
    $('#modal-titulo').text('Novo Plano');
    $('#plano-id').val('');
    $('#form-plano')[0].reset();
    $('#plano-ativo').prop('checked', true);
    $('#modal-plano').modal('show');
});

$(document).on('click', '.btn-editar', function () {
    const id = $(this).data('id');
    $.get(URLS_P.show + id, function (r) {
        if (!r.sucesso) return;
        const p = r.dado;
        $('#modal-titulo').text('Editar Plano');
        $('#plano-id').val(p.id);
        const f = $('#form-plano');
        f.find('[name="nome"]').val(p.nome);
        f.find('[name="slug"]').val(p.slug);
        f.find('[name="descricao"]').val(p.descricao || '');
        f.find('[name="valor_mensal"]').val(toNumberBr(p.valor_mensal));
        f.find('[name="valor_anual"]').val(p.valor_anual ? toNumberBr(p.valor_anual) : '');
        f.find('[name="ordem"]').val(p.ordem ?? 0);
        const limites = p.limites ? Object.entries(p.limites).map(([k,v]) => `${k}=${v}`).join('\\n') : '';
        f.find('[name="limites"]').val(limites);
        $('#plano-ativo').prop('checked', !!p.ativo);
        $('#modal-plano').modal('show');
    });
});

$('#form-plano').on('submit', function (e) {
    e.preventDefault();
    const id = $('#plano-id').val();
    const dados = {};
    $(this).serializeArray().forEach(f => dados[f.name] = f.value);
    dados.ativo = $('#plano-ativo').is(':checked') ? '1' : '0';
    dados.valor_mensal = parseMoneyBr(dados.valor_mensal);
    dados.valor_anual = parseMoneyBr(dados.valor_anual);

    $.ajax({
        url: id ? URLS_P.update + id : URLS_P.store,
        type: id ? 'PUT' : 'POST',
        data: dados,
        success: r => {
            toast(r.mensagem || 'Salvo.', 'sucesso');
            $('#modal-plano').modal('hide');
            carregarPlanos(paginaAtualP);
        },
        error: xhr => {
            const erros = xhr.responseJSON?.errors;
            toast(erros ? Object.values(erros).flat().join(' | ') : (xhr.responseJSON?.mensagem || 'Erro ao salvar.'), 'erro');
        },
    });
});

$(document).on('click', '.btn-excluir', function () {
    const id = $(this).data('id');
    confirmarExclusao(URLS_P.destroy + id, () => {
        $.ajax({
            url: URLS_P.destroy + id,
            type: 'DELETE',
            success: r => { toast(r.mensagem, 'sucesso'); carregarPlanos(paginaAtualP); },
            error: xhr => toast(xhr.responseJSON?.mensagem || 'Erro ao remover.', 'erro'),
        });
    });
});

$('#btn-filtrar').on('click', () => carregarPlanos(1));
$('#btn-limpar').on('click', () => { $('#filtro-search').val(''); carregarPlanos(1); });
$('#filtro-search').on('keypress', e => { if (e.which === 13) carregarPlanos(1); });

carregarPlanos();
</script>
@endpush

