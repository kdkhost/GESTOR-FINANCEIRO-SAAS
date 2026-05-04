@extends('layouts.admin.app')

@section('titulo', 'SaaS')
@section('titulo_pagina', 'Empresas (Tenants)')

@section('breadcrumb')
    <li class="breadcrumb-item">Administracao</li>
    <li class="breadcrumb-item">SaaS</li>
    <li class="breadcrumb-item active">Empresas</li>
@endsection

@section('conteudo')
<div class="card card-outline card-secondary mb-3">
    <div class="card-body py-2">
        <div class="row g-2 align-items-end">
            <div class="col-md-4">
                <label class="form-label small mb-1">Buscar</label>
                <input type="text" id="filtro-search" class="form-control form-control-sm" placeholder="Nome, CNPJ, e-mail...">
            </div>
            <div class="col-md-3">
                <label class="form-label small mb-1">Status</label>
                <select id="filtro-status" class="form-select form-select-sm">
                    <option value="">Todos</option>
                    <option value="ativo">Ativo</option>
                    <option value="inativo">Inativo</option>
                    <option value="bloqueado">Bloqueado</option>
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
        <h3 class="card-title mb-0"><i class="bi bi-buildings text-primary me-2"></i>Empresas</h3>
        <button class="btn btn-primary btn-sm" id="btn-novo"><i class="bi bi-plus-lg me-1"></i>Nova Empresa</button>
    </div>
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover mb-0">
                <thead class="table-light">
                    <tr>
                        <th>#</th>
                        <th>Empresa</th>
                        <th>Contato</th>
                        <th class="text-center">Status</th>
                        <th class="text-end" style="width: 110px;">Acoes</th>
                    </tr>
                </thead>
                <tbody id="tbody-empresas">
                    <tr><td colspan="5" class="text-center py-4 text-muted"><i class="bi bi-hourglass-split me-2"></i>Carregando...</td></tr>
                </tbody>
            </table>
        </div>
    </div>
    <div class="card-footer d-flex align-items-center justify-content-between">
        <span class="text-muted small" id="info-paginacao">0 registros</span>
        <nav><ul class="pagination pagination-sm mb-0" id="paginacao"></ul></nav>
    </div>
</div>

<div class="modal fade" id="modal-empresa" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header bg-primary text-white">
                <h5 class="modal-title"><i class="bi bi-building me-2"></i><span id="modal-titulo">Nova Empresa</span></h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <form id="form-empresa">
                <div class="modal-body">
                    <input type="hidden" id="empresa-id">
                    <div class="row g-3">
                        <div class="col-md-8">
                            <label class="form-label fw-medium">Nome fantasia</label>
                            <input type="text" class="form-control" name="nome_fantasia" required>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label fw-medium">Status</label>
                            <select class="form-select" name="status" required>
                                <option value="ativo">Ativo</option>
                                <option value="inativo">Inativo</option>
                                <option value="bloqueado">Bloqueado</option>
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-medium">Razao social</label>
                            <input type="text" class="form-control" name="razao_social">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-medium">CNPJ</label>
                            <input type="text" class="form-control mask-cnpj" name="cnpj" placeholder="00.000.000/0000-00">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-medium">E-mail</label>
                            <input type="email" class="form-control" name="email">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-medium">Telefone</label>
                            <input type="text" class="form-control mask-telefone" name="telefone" placeholder="(11) 99999-9999">
                        </div>
                        <div class="col-md-3">
                            <label class="form-label fw-medium">CEP</label>
                            <input type="text" class="form-control mask-cep viacep" name="cep" placeholder="00000-000">
                        </div>
                        <div class="col-md-7">
                            <label class="form-label fw-medium">Logradouro</label>
                            <input type="text" class="form-control" name="logradouro">
                        </div>
                        <div class="col-md-2">
                            <label class="form-label fw-medium">Numero</label>
                            <input type="text" class="form-control" name="numero">
                        </div>
                        <div class="col-md-4">
                            <label class="form-label fw-medium">Bairro</label>
                            <input type="text" class="form-control" name="bairro">
                        </div>
                        <div class="col-md-5">
                            <label class="form-label fw-medium">Cidade</label>
                            <input type="text" class="form-control" name="cidade">
                        </div>
                        <div class="col-md-3">
                            <label class="form-label fw-medium">Estado</label>
                            <input type="text" class="form-control" name="estado" placeholder="UF">
                        </div>
                        <div class="col-12">
                            <label class="form-label fw-medium">Observacoes</label>
                            <textarea class="form-control" rows="3" name="observacoes"></textarea>
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
const URLS_E = {
    listar: '{{ route("admin.saas.empresas.listar") }}',
    store:  '{{ route("admin.saas.empresas.store") }}',
    show:   '{{ url("/admin/saas/empresas") }}/',
    update: '{{ url("/admin/saas/empresas") }}/',
    destroy:'{{ url("/admin/saas/empresas") }}/',
};
let paginaAtualE = 1;
const perPageE = 10;

function carregarEmpresas(pagina = 1) {
    paginaAtualE = pagina;
    $.get(URLS_E.listar, { page: pagina, per_page: perPageE, search: $('#filtro-search').val(), status: $('#filtro-status').val() }, function (r) {
        const tbody = $('#tbody-empresas').empty();
        if (!r.sucesso || !r.dados.length) {
            tbody.html('<tr><td colspan="5" class="text-center py-4 text-muted"><i class="bi bi-inbox fs-3 d-block mb-2"></i>Nenhuma empresa encontrada.</td></tr>');
            $('#info-paginacao').text('0 registros');
            $('#paginacao').empty();
            return;
        }
        r.dados.forEach(e => {
            const statusMap = {ativo:'success',inativo:'secondary',bloqueado:'danger'};
            tbody.append(`<tr>
                <td class="text-muted small">${e.id}</td>
                <td><div class="fw-medium">${e.nome_fantasia}</div><small class="text-muted">${e.cnpj || '-'}</small></td>
                <td><div>${e.email || '<span class="text-muted">-</span>'}</div><small class="text-muted">${e.telefone || ''}</small></td>
                <td class="text-center"><span class="badge bg-${statusMap[e.status]||'secondary'}">${e.status}</span></td>
                <td class="text-end"><div class="btn-group btn-group-sm">
                    <button class="btn btn-outline-primary btn-editar" data-id="${e.id}"><i class="bi bi-pencil"></i></button>
                    <button class="btn btn-outline-danger btn-excluir" data-id="${e.id}"><i class="bi bi-trash"></i></button>
                </div></td>
            </tr>`);
        });
        const ini = (pagina - 1) * perPageE + 1;
        const fim = Math.min(pagina * perPageE, r.total);
        $('#info-paginacao').text(`Exibindo ${ini}-${fim} de ${r.total} registros`);
        renderPagE(r.paginas, pagina);
    }).fail(() => toast('Erro ao carregar empresas.', 'erro'));
}

function renderPagE(total, atual) {
    const ul = $('#paginacao').empty();
    if (total <= 1) return;
    ul.append(`<li class="page-item ${atual === 1 ? 'disabled' : ''}"><a class="page-link" href="#" data-p="${atual - 1}">&laquo;</a></li>`);
    for (let i = 1; i <= total; i++) {
        if (i === 1 || i === total || Math.abs(i - atual) <= 2) ul.append(`<li class="page-item ${i === atual ? 'active' : ''}"><a class="page-link" href="#" data-p="${i}">${i}</a></li>`);
        else if (Math.abs(i - atual) === 3) ul.append('<li class="page-item disabled"><span class="page-link">...</span></li>');
    }
    ul.append(`<li class="page-item ${atual === total ? 'disabled' : ''}"><a class="page-link" href="#" data-p="${atual + 1}">&raquo;</a></li>`);
}

$(document).on('click', '#paginacao a[data-p]', function (e) {
    e.preventDefault();
    carregarEmpresas(parseInt($(this).data('p')));
});

$('#btn-novo').on('click', () => {
    $('#modal-titulo').text('Nova Empresa');
    $('#empresa-id').val('');
    $('#form-empresa')[0].reset();
    $('#modal-empresa').modal('show');
});

$(document).on('click', '.btn-editar', function () {
    const id = $(this).data('id');
    $.get(URLS_E.show + id, function (r) {
        if (!r.sucesso) return;
        const e = r.dado;
        $('#modal-titulo').text('Editar Empresa');
        $('#empresa-id').val(e.id);
        const f = $('#form-empresa');
        Object.keys(e).forEach(k => { if (f.find(`[name=\"${k}\"]`).length) f.find(`[name=\"${k}\"]`).val(e[k] ?? ''); });
        $('#modal-empresa').modal('show');
    });
});

$('#form-empresa').on('submit', function (ev) {
    ev.preventDefault();
    const id = $('#empresa-id').val();
    const dados = {};
    $(this).serializeArray().forEach(f => dados[f.name] = f.value);
    $.ajax({
        url: id ? URLS_E.update + id : URLS_E.store,
        type: id ? 'PUT' : 'POST',
        data: dados,
        success: r => { toast(r.mensagem || 'Salvo.', 'sucesso'); $('#modal-empresa').modal('hide'); carregarEmpresas(paginaAtualE); },
        error: xhr => { const erros = xhr.responseJSON?.errors; toast(erros ? Object.values(erros).flat().join(' | ') : (xhr.responseJSON?.mensagem || 'Erro.'), 'erro'); },
    });
});

$(document).on('click', '.btn-excluir', function () {
    const id = $(this).data('id');
    confirmarExclusao(URLS_E.destroy + id, () => {
        $.ajax({
            url: URLS_E.destroy + id,
            type: 'DELETE',
            success: r => { toast(r.mensagem, 'sucesso'); carregarEmpresas(paginaAtualE); },
            error: xhr => toast(xhr.responseJSON?.mensagem || 'Erro ao remover.', 'erro'),
        });
    });
});

$('#btn-filtrar').on('click', () => carregarEmpresas(1));
$('#btn-limpar').on('click', () => { $('#filtro-search,#filtro-status').val(''); carregarEmpresas(1); });
$('#filtro-search').on('keypress', e => { if (e.which === 13) carregarEmpresas(1); });

carregarEmpresas();
</script>
@endpush
