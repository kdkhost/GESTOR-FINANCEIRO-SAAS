@extends('layouts.admin.app')
@section('titulo', 'Modulos')
@section('titulo_pagina', 'Gestao de Modulos')
@section('breadcrumb')
    <li class="breadcrumb-item">Administracao</li>
    <li class="breadcrumb-item active">Modulos</li>
@endsection
@section('conteudo')
<div class="card card-outline card-secondary mb-3">
    <div class="card-body py-2">
        <div class="row g-2 align-items-end">
            <div class="col-md-4">
                <label class="form-label small mb-1">Buscar</label>
                <input type="text" id="filtro-search" class="form-control form-control-sm" placeholder="Nome, slug ou descricao...">
            </div>
            <div class="col-md-2">
                <label class="form-label small mb-1">Status</label>
                <select id="filtro-status" class="form-select form-select-sm">
                    <option value="">Todos</option>
                    <option value="ativo">Ativo</option>
                    <option value="inativo">Inativo</option>
                </select>
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
        <h3 class="card-title mb-0"><i class="bi bi-boxes text-primary me-2"></i>Modulos adicionais e nativos</h3>
        <button class="btn btn-primary btn-sm" id="btn-novo"><i class="bi bi-plus-lg me-1"></i>Novo Modulo</button>
    </div>
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover mb-0">
                <thead class="table-light">
                    <tr>
                        <th>#</th>
                        <th>Modulo</th>
                        <th>Diretorio</th>
                        <th>Versao</th>
                        <th class="text-center">Tipo</th>
                        <th class="text-center">Status</th>
                        <th class="text-end" style="width: 170px;">Acoes</th>
                    </tr>
                </thead>
                <tbody id="tbody-modulos">
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

<div class="modal fade" id="modal-modulo" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header bg-primary text-white">
                <h5 class="modal-title" id="modal-modulo-titulo">Novo Modulo</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <form id="form-modulo">
                <div class="modal-body">
                    <input type="hidden" id="modulo-id">
                    <div class="mb-3">
                        <label class="form-label fw-medium">Nome <span class="text-danger">*</span></label>
                        <input type="text" name="nome" class="form-control" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-medium">Slug <span class="text-danger">*</span></label>
                        <input type="text" name="slug" class="form-control" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-medium">Diretorio</label>
                        <input type="text" name="diretorio" class="form-control" placeholder="Ex: MeuModulo">
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-medium">Provider</label>
                        <input type="text" name="provider" class="form-control" placeholder="Ex: App\Modules\MeuModulo\MeuModuloServiceProvider">
                    </div>
                    <div class="row g-2">
                        <div class="col-md-6">
                            <label class="form-label fw-medium">Versao</label>
                            <input type="text" name="versao" class="form-control" placeholder="1.0.0">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-medium">Status</label>
                            <select name="ativo" class="form-select">
                                <option value="1">Ativo</option>
                                <option value="0">Inativo</option>
                            </select>
                        </div>
                    </div>
                    <div class="mt-3">
                        <label class="form-label fw-medium">Descricao</label>
                        <textarea name="descricao" class="form-control" rows="3"></textarea>
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
const URLS_M = { listar:'/admin/modulos/listar', store:'/admin/modulos', show:'/admin/modulos/', update:'/admin/modulos/', destroy:'/admin/modulos/', status:'/admin/modulos/' };
let paginaAtualM = 1; const perPageM = 10;

function carregarModulos(pagina = 1) {
    paginaAtualM = pagina;
    $.get(URLS_M.listar, {
        page: pagina,
        per_page: perPageM,
        search: $('#filtro-search').val(),
        status: $('#filtro-status').val(),
    }, function (r) {
        const tbody = $('#tbody-modulos'); tbody.empty();
        if (!r.sucesso || !r.dados.length) {
            tbody.html('<tr><td colspan="7" class="text-center py-4 text-muted"><i class="bi bi-inbox fs-3 d-block mb-2"></i>Nenhum modulo encontrado.</td></tr>');
            $('#info-paginacao').text('0 registros');
            $('#paginacao').empty();
            return;
        }
        r.dados.forEach(m => {
            tbody.append(`<tr>
                <td class="text-muted small">${m.id}</td>
                <td><div class="fw-medium">${m.nome}</div><small class="text-muted">${m.slug}</small></td>
                <td><code>${m.diretorio ?? '-'}</code></td>
                <td>${m.versao ?? '-'}</td>
                <td class="text-center"><span class="badge bg-${m.nativo ? 'dark' : 'info'}">${m.nativo ? 'Nativo' : 'Adicional'}</span></td>
                <td class="text-center"><span class="badge bg-${m.ativo ? 'success' : 'secondary'}">${m.ativo ? 'Ativo' : 'Inativo'}</span></td>
                <td class="text-end">
                    <div class="btn-group btn-group-sm">
                        <button class="btn btn-outline-${m.ativo ? 'warning' : 'success'} btn-status" data-id="${m.id}" title="${m.ativo ? 'Desativar' : 'Ativar'}"><i class="bi bi-power"></i></button>
                        <button class="btn btn-outline-primary btn-editar" data-id="${m.id}" ${m.nativo ? 'disabled' : ''}><i class="bi bi-pencil"></i></button>
                        <button class="btn btn-outline-danger btn-excluir" data-id="${m.id}" ${m.nativo ? 'disabled' : ''}><i class="bi bi-trash"></i></button>
                    </div>
                </td>
            </tr>`);
        });
        const ini = (pagina - 1) * perPageM + 1;
        const fim = Math.min(pagina * perPageM, r.total);
        $('#info-paginacao').text(`Exibindo ${ini}-${fim} de ${r.total} registros`);
        renderPagM(r.paginas, pagina);
    }).fail(() => toast('Erro ao carregar modulos.', 'erro'));
}

function renderPagM(total, atual) {
    const ul = $('#paginacao'); ul.empty(); if (total <= 1) return;
    ul.append(`<li class="page-item ${atual===1?'disabled':''}"><a class="page-link" href="#" data-p="${atual-1}">&laquo;</a></li>`);
    for (let i = 1; i <= total; i++) {
        if (i === 1 || i === total || Math.abs(i - atual) <= 2) ul.append(`<li class="page-item ${i===atual?'active':''}"><a class="page-link" href="#" data-p="${i}">${i}</a></li>`);
        else if (Math.abs(i - atual) === 3) ul.append('<li class="page-item disabled"><span class="page-link">...</span></li>');
    }
    ul.append(`<li class="page-item ${atual===total?'disabled':''}"><a class="page-link" href="#" data-p="${atual+1}">&raquo;</a></li>`);
}

$(document).on('click', '#paginacao a[data-p]', function (e) {
    e.preventDefault();
    carregarModulos(parseInt($(this).data('p')));
});

$('#btn-novo').on('click', () => {
    $('#modal-modulo-titulo').text('Novo Modulo');
    $('#modulo-id').val('');
    $('#form-modulo')[0].reset();
    $('#modal-modulo').modal('show');
});

$(document).on('click', '.btn-editar', function () {
    const id = $(this).data('id');
    $.get(URLS_M.show + id, function (r) {
        if (!r.sucesso) return;
        const m = r.dado;
        $('#modal-modulo-titulo').text('Editar Modulo');
        $('#modulo-id').val(m.id);
        const f = $('#form-modulo');
        f.find('[name="nome"]').val(m.nome);
        f.find('[name="slug"]').val(m.slug);
        f.find('[name="diretorio"]').val(m.diretorio);
        f.find('[name="provider"]').val(m.provider);
        f.find('[name="versao"]').val(m.versao);
        f.find('[name="ativo"]').val(m.ativo ? '1' : '0');
        f.find('[name="descricao"]').val(m.descricao);
        $('#modal-modulo').modal('show');
    });
});

$('#form-modulo').on('submit', function (e) {
    e.preventDefault();
    const id = $('#modulo-id').val();
    const dados = {};
    $(this).serializeArray().forEach(f => dados[f.name] = f.value);
    dados.ativo = dados.ativo === '1' ? 1 : 0;
    $.ajax({
        url: id ? URLS_M.update + id : URLS_M.store,
        type: id ? 'PUT' : 'POST',
        data: dados,
        success: r => {
            toast(r.mensagem || 'Operacao realizada.', 'sucesso');
            $('#modal-modulo').modal('hide');
            carregarModulos(paginaAtualM);
        },
        error: r => {
            const erros = r.responseJSON?.errors;
            if (erros) return toast(Object.values(erros).flat().join(' | '), 'erro');
            toast(r.responseJSON?.mensagem || 'Erro ao salvar modulo.', 'erro');
        }
    });
});

$(document).on('click', '.btn-excluir', function () {
    const id = $(this).data('id');
    confirmarExclusao(URLS_M.destroy + id, () => {
        $.ajax({
            url: URLS_M.destroy + id,
            type: 'DELETE',
            success: r => { toast(r.mensagem, 'sucesso'); carregarModulos(paginaAtualM); },
            error: r => toast(r.responseJSON?.mensagem || 'Erro ao remover modulo.', 'erro')
        });
    });
});

$(document).on('click', '.btn-status', function () {
    const id = $(this).data('id');
    $.post(URLS_M.status + id + '/status', {}, function (r) {
        toast(r.mensagem, 'sucesso');
        carregarModulos(paginaAtualM);
    }).fail(r => toast(r.responseJSON?.mensagem || 'Erro ao alterar status.', 'erro'));
});

$('#btn-filtrar').on('click', () => carregarModulos(1));
$('#btn-limpar').on('click', () => { $('#filtro-search,#filtro-status').val(''); carregarModulos(1); });
$('#filtro-search').on('keypress', e => { if (e.which === 13) carregarModulos(1); });

carregarModulos();
</script>
@endpush

