@extends('layouts.admin.app')

@section('titulo', 'SaaS')
@section('titulo_pagina', 'Assinaturas')

@section('breadcrumb')
    <li class="breadcrumb-item">Administracao</li>
    <li class="breadcrumb-item">SaaS</li>
    <li class="breadcrumb-item active">Assinaturas</li>
@endsection

@section('conteudo')
<div class="card card-outline card-secondary mb-3">
    <div class="card-body py-2">
        <div class="row g-2 align-items-end">
            <div class="col-md-4">
                <label class="form-label small mb-1">Buscar</label>
                <input type="text" id="filtro-search" class="form-control form-control-sm" placeholder="Gateway ref...">
            </div>
            <div class="col-md-3">
                <label class="form-label small mb-1">Status</label>
                <select id="filtro-status" class="form-select form-select-sm">
                    <option value="">Todos</option>
                    <option value="trial">Trial</option>
                    <option value="ativa">Ativa</option>
                    <option value="suspensa">Suspensa</option>
                    <option value="cancelada">Cancelada</option>
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
        <h3 class="card-title mb-0"><i class="bi bi-receipt text-primary me-2"></i>Assinaturas</h3>
        <button class="btn btn-primary btn-sm" id="btn-novo"><i class="bi bi-plus-lg me-1"></i>Nova Assinatura</button>
    </div>
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover mb-0">
                <thead class="table-light">
                    <tr>
                        <th>#</th>
                        <th>Empresa</th>
                        <th>Plano</th>
                        <th>Status</th>
                        <th>Proxima cobranca</th>
                        <th class="text-end" style="width: 110px;">Acoes</th>
                    </tr>
                </thead>
                <tbody id="tbody-assinaturas">
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

<div class="modal fade" id="modal-assinatura" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header bg-primary text-white">
                <h5 class="modal-title"><i class="bi bi-receipt me-2"></i><span id="modal-titulo">Nova Assinatura</span></h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <form id="form-assinatura">
                <div class="modal-body">
                    <input type="hidden" id="assinatura-id">
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label fw-medium">Empresa</label>
                            <select class="form-select" name="empresa_id" id="assinatura-empresa" required></select>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-medium">Plano</label>
                            <select class="form-select" name="plano_id" id="assinatura-plano" required></select>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label fw-medium">Status</label>
                            <select class="form-select" name="status" required>
                                <option value="trial">Trial</option>
                                <option value="ativa">Ativa</option>
                                <option value="suspensa">Suspensa</option>
                                <option value="cancelada">Cancelada</option>
                            </select>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label fw-medium">Inicio</label>
                            <input type="datetime-local" class="form-control" name="inicio_em">
                        </div>
                        <div class="col-md-4">
                            <label class="form-label fw-medium">Proxima cobranca</label>
                            <input type="datetime-local" class="form-control" name="proxima_cobranca_em">
                        </div>
                        <div class="col-md-4">
                            <label class="form-label fw-medium">Gateway</label>
                            <input type="text" class="form-control" name="gateway" placeholder="mercadopago">
                        </div>
                        <div class="col-md-8">
                            <label class="form-label fw-medium">Gateway ref</label>
                            <input type="text" class="form-control" name="gateway_ref" placeholder="ID externo">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-medium">Trial ate</label>
                            <input type="datetime-local" class="form-control" name="trial_ate">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-medium">Cancelada em</label>
                            <input type="datetime-local" class="form-control" name="cancelada_em">
                        </div>
                        <div class="col-12">
                            <label class="form-label fw-medium">Motivo cancelamento</label>
                            <input type="text" class="form-control" name="cancelamento_motivo">
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
const URLS_A = {
    listar: '{{ route("admin.saas.assinaturas.listar") }}',
    store:  '{{ route("admin.saas.assinaturas.store") }}',
    show:   '{{ url("/admin/saas/assinaturas") }}/',
    update: '{{ url("/admin/saas/assinaturas") }}/',
    destroy:'{{ url("/admin/saas/assinaturas") }}/',
};
let paginaAtualA = 1;
const perPageA = 10;
let lookupEmpresas = [];
let lookupPlanos = [];

function fmtData(v) { return v ? String(v).replace('T', ' ').slice(0, 16) : '<span class="text-muted">-</span>'; }

function carregarAssinaturas(pagina = 1) {
    paginaAtualA = pagina;
    $.get(URLS_A.listar, { page: pagina, per_page: perPageA, search: $('#filtro-search').val(), status: $('#filtro-status').val() }, function (r) {
        lookupEmpresas = r.lookups?.empresas || lookupEmpresas;
        lookupPlanos = r.lookups?.planos || lookupPlanos;
        preencherLookups();

        const tbody = $('#tbody-assinaturas').empty();
        if (!r.sucesso || !r.dados.length) {
            tbody.html('<tr><td colspan="6" class="text-center py-4 text-muted"><i class="bi bi-inbox fs-3 d-block mb-2"></i>Nenhuma assinatura encontrada.</td></tr>');
            $('#info-paginacao').text('0 registros');
            $('#paginacao').empty();
            return;
        }
        const statusMap = {trial:'warning',ativa:'success',suspensa:'secondary',cancelada:'danger'};
        r.dados.forEach(a => {
            tbody.append(`<tr>
                <td class="text-muted small">${a.id}</td>
                <td>${a.empresa}</td>
                <td>${a.plano}</td>
                <td><span class="badge bg-${statusMap[a.status]||'secondary'}">${a.status}</span></td>
                <td>${fmtData(a.proxima_cobranca_em)}</td>
                <td class="text-end"><div class="btn-group btn-group-sm">
                    <button class="btn btn-outline-primary btn-editar" data-id="${a.id}"><i class="bi bi-pencil"></i></button>
                    <button class="btn btn-outline-danger btn-excluir" data-id="${a.id}"><i class="bi bi-trash"></i></button>
                </div></td>
            </tr>`);
        });
        const ini = (pagina - 1) * perPageA + 1;
        const fim = Math.min(pagina * perPageA, r.total);
        $('#info-paginacao').text(`Exibindo ${ini}-${fim} de ${r.total} registros`);
        renderPagA(r.paginas, pagina);
    }).fail(() => toast('Erro ao carregar assinaturas.', 'erro'));
}

function preencherLookups() {
    const selE = $('#assinatura-empresa').empty();
    lookupEmpresas.forEach(e => selE.append(`<option value="${e.id}">${e.nome_fantasia}</option>`));
    const selP = $('#assinatura-plano').empty();
    lookupPlanos.forEach(p => selP.append(`<option value="${p.id}">${p.nome}</option>`));
}

function renderPagA(total, atual) {
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
    carregarAssinaturas(parseInt($(this).data('p')));
});

$('#btn-novo').on('click', () => {
    $('#modal-titulo').text('Nova Assinatura');
    $('#assinatura-id').val('');
    $('#form-assinatura')[0].reset();
    preencherLookups();
    $('#modal-assinatura').modal('show');
});

$(document).on('click', '.btn-editar', function () {
    const id = $(this).data('id');
    $.get(URLS_A.show + id, function (r) {
        if (!r.sucesso) return;
        const a = r.dado;
        $('#modal-titulo').text('Editar Assinatura');
        $('#assinatura-id').val(a.id);
        const f = $('#form-assinatura');
        preencherLookups();
        Object.keys(a).forEach(k => { if (f.find(`[name=\"${k}\"]`).length) f.find(`[name=\"${k}\"]`).val((a[k] || '').toString().replace(' ', 'T').slice(0, 16)); });
        $('#modal-assinatura').modal('show');
    });
});

$('#form-assinatura').on('submit', function (ev) {
    ev.preventDefault();
    const id = $('#assinatura-id').val();
    const dados = {};
    $(this).serializeArray().forEach(f => dados[f.name] = f.value);
    $.ajax({
        url: id ? URLS_A.update + id : URLS_A.store,
        type: id ? 'PUT' : 'POST',
        data: dados,
        success: r => { toast(r.mensagem || 'Salvo.', 'sucesso'); $('#modal-assinatura').modal('hide'); carregarAssinaturas(paginaAtualA); },
        error: xhr => { const erros = xhr.responseJSON?.errors; toast(erros ? Object.values(erros).flat().join(' | ') : (xhr.responseJSON?.mensagem || 'Erro.'), 'erro'); },
    });
});

$(document).on('click', '.btn-excluir', function () {
    const id = $(this).data('id');
    confirmarExclusao(URLS_A.destroy + id, () => {
        $.ajax({
            url: URLS_A.destroy + id,
            type: 'DELETE',
            success: r => { toast(r.mensagem, 'sucesso'); carregarAssinaturas(paginaAtualA); },
            error: xhr => toast(xhr.responseJSON?.mensagem || 'Erro ao remover.', 'erro'),
        });
    });
});

$('#btn-filtrar').on('click', () => carregarAssinaturas(1));
$('#btn-limpar').on('click', () => { $('#filtro-search,#filtro-status').val(''); carregarAssinaturas(1); });
$('#filtro-search').on('keypress', e => { if (e.which === 13) carregarAssinaturas(1); });

carregarAssinaturas();
</script>
@endpush

