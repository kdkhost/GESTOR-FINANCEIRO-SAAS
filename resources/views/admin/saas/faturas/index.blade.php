@extends('layouts.admin.app')

@section('titulo', 'SaaS')
@section('titulo_pagina', 'Faturas / Cobrancas')

@section('breadcrumb')
    <li class="breadcrumb-item">Administracao</li>
    <li class="breadcrumb-item">SaaS</li>
    <li class="breadcrumb-item active">Faturas</li>
@endsection

@section('conteudo')
<div class="card card-outline card-secondary mb-3">
    <div class="card-body py-2">
        <div class="row g-2 align-items-end">
            <div class="col-md-4">
                <label class="form-label small mb-1">Buscar</label>
                <input type="text" id="filtro-search" class="form-control form-control-sm" placeholder="Competencia, gateway ref...">
            </div>
            <div class="col-md-3">
                <label class="form-label small mb-1">Status</label>
                <select id="filtro-status" class="form-select form-select-sm">
                    <option value="">Todos</option>
                    <option value="aberta">Aberta</option>
                    <option value="paga">Paga</option>
                    <option value="vencida">Vencida</option>
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
        <h3 class="card-title mb-0"><i class="bi bi-cash-coin text-primary me-2"></i>Faturas</h3>
        <button class="btn btn-primary btn-sm" id="btn-novo"><i class="bi bi-plus-lg me-1"></i>Nova Fatura</button>
    </div>
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover mb-0">
                <thead class="table-light">
                    <tr>
                        <th>#</th>
                        <th>Empresa</th>
                        <th>Competencia</th>
                        <th>Valor</th>
                        <th>Vencimento</th>
                        <th>Status</th>
                        <th class="text-end" style="width: 110px;">Acoes</th>
                    </tr>
                </thead>
                <tbody id="tbody-faturas">
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

<div class="modal fade" id="modal-fatura" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header bg-primary text-white">
                <h5 class="modal-title"><i class="bi bi-cash-coin me-2"></i><span id="modal-titulo">Nova Fatura</span></h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <form id="form-fatura">
                <div class="modal-body">
                    <input type="hidden" id="fatura-id">
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label fw-medium">Empresa</label>
                            <select class="form-select" name="empresa_id" id="fatura-empresa" required></select>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-medium">Assinatura (opcional)</label>
                            <select class="form-select" name="assinatura_id" id="fatura-assinatura"></select>
                        </div>
                        <div class="col-md-3">
                            <label class="form-label fw-medium">Status</label>
                            <select class="form-select" name="status" required>
                                <option value="aberta">Aberta</option>
                                <option value="paga">Paga</option>
                                <option value="vencida">Vencida</option>
                                <option value="cancelada">Cancelada</option>
                            </select>
                        </div>
                        <div class="col-md-3">
                            <label class="form-label fw-medium">Competencia</label>
                            <input type="text" class="form-control" name="competencia" required placeholder="2026-05">
                        </div>
                        <div class="col-md-3">
                            <label class="form-label fw-medium">Valor</label>
                            <input type="text" class="form-control" name="valor" required placeholder="0,00">
                        </div>
                        <div class="col-md-3">
                            <label class="form-label fw-medium">Vencimento</label>
                            <input type="datetime-local" class="form-control" name="vencimento_em" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-medium">Gateway</label>
                            <input type="text" class="form-control" name="gateway" placeholder="mercadopago">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-medium">Gateway ref</label>
                            <input type="text" class="form-control" name="gateway_ref">
                        </div>
                        <div class="col-12">
                            <label class="form-label fw-medium">Link pagamento</label>
                            <input type="text" class="form-control" name="link_pagamento">
                        </div>
                        <div class="col-12">
                            <label class="form-label fw-medium">PIX copia e cola</label>
                            <textarea class="form-control" rows="2" name="pix_copia_e_cola"></textarea>
                        </div>
                        <div class="col-12">
                            <label class="form-label fw-medium">Boleto linha digitavel</label>
                            <textarea class="form-control" rows="2" name="boleto_linha_digitavel"></textarea>
                        </div>
                        <div class="col-12">
                            <label class="form-label fw-medium">Observacoes</label>
                            <textarea class="form-control" rows="2" name="observacoes"></textarea>
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
const URLS_F = {
    listar: '{{ route("admin.saas.faturas.listar") }}',
    store:  '{{ route("admin.saas.faturas.store") }}',
    show:   '{{ url("/admin/saas/faturas") }}/',
    update: '{{ url("/admin/saas/faturas") }}/',
    destroy:'{{ url("/admin/saas/faturas") }}/',
};
let paginaAtualF = 1;
const perPageF = 10;
let lookupEmpresasF = [];
let lookupAssinaturasF = [];

function parseMoneyBr(v) {
    const s = String(v || '').trim();
    if (s === '') return '';
    return s.replace(/\./g, '').replace(',', '.').replace(/[^0-9.]/g, '');
}
function toBr(v) { return v ? String(v).replace('.', ',') : '0,00'; }
function fmtData(v) { return v ? String(v).replace('T',' ').slice(0,16) : '<span class="text-muted">-</span>'; }

function preencherLookupsF() {
    const selE = $('#fatura-empresa').empty();
    lookupEmpresasF.forEach(e => selE.append(`<option value="${e.id}">${e.nome_fantasia}</option>`));
    const selA = $('#fatura-assinatura').empty();
    selA.append('<option value=\"\">-</option>');
    lookupAssinaturasF.forEach(a => selA.append(`<option value="${a.id}">#${a.id} (empresa ${a.empresa_id})</option>`));
}

function carregarFaturas(pagina = 1) {
    paginaAtualF = pagina;
    $.get(URLS_F.listar, { page: pagina, per_page: perPageF, search: $('#filtro-search').val(), status: $('#filtro-status').val() }, function (r) {
        lookupEmpresasF = r.lookups?.empresas || lookupEmpresasF;
        lookupAssinaturasF = r.lookups?.assinaturas || lookupAssinaturasF;
        preencherLookupsF();

        const tbody = $('#tbody-faturas').empty();
        if (!r.sucesso || !r.dados.length) {
            tbody.html('<tr><td colspan=\"7\" class=\"text-center py-4 text-muted\"><i class=\"bi bi-inbox fs-3 d-block mb-2\"></i>Nenhuma fatura encontrada.</td></tr>');
            $('#info-paginacao').text('0 registros');
            $('#paginacao').empty();
            return;
        }
        const statusMap = {aberta:'warning',paga:'success',vencida:'danger',cancelada:'secondary'};
        r.dados.forEach(f => {
            tbody.append(`<tr>
                <td class="text-muted small">${f.id}</td>
                <td>${f.empresa}</td>
                <td>${f.competencia}</td>
                <td>R$ ${toBr(f.valor)}</td>
                <td>${fmtData(f.vencimento_em)}</td>
                <td><span class="badge bg-${statusMap[f.status]||'secondary'}">${f.status}</span></td>
                <td class="text-end"><div class="btn-group btn-group-sm">
                    <button class="btn btn-outline-primary btn-editar" data-id="${f.id}"><i class="bi bi-pencil"></i></button>
                    <button class="btn btn-outline-danger btn-excluir" data-id="${f.id}"><i class="bi bi-trash"></i></button>
                </div></td>
            </tr>`);
        });
        const ini = (pagina - 1) * perPageF + 1;
        const fim = Math.min(pagina * perPageF, r.total);
        $('#info-paginacao').text(`Exibindo ${ini}-${fim} de ${r.total} registros`);
        renderPagF(r.paginas, pagina);
    }).fail(() => toast('Erro ao carregar faturas.', 'erro'));
}

function renderPagF(total, atual) {
    const ul = $('#paginacao').empty();
    if (total <= 1) return;
    ul.append(`<li class="page-item ${atual===1?'disabled':''}"><a class="page-link" href="#" data-p="${atual-1}">&laquo;</a></li>`);
    for (let i=1;i<=total;i++){
        if (i===1||i===total||Math.abs(i-atual)<=2) ul.append(`<li class="page-item ${i===atual?'active':''}"><a class="page-link" href="#" data-p="${i}">${i}</a></li>`);
        else if (Math.abs(i-atual)===3) ul.append('<li class="page-item disabled"><span class="page-link">...</span></li>');
    }
    ul.append(`<li class="page-item ${atual===total?'disabled':''}"><a class="page-link" href="#" data-p="${atual+1}">&raquo;</a></li>`);
}

$(document).on('click', '#paginacao a[data-p]', function(e){ e.preventDefault(); carregarFaturas(parseInt($(this).data('p'))); });

$('#btn-novo').on('click', () => {
    $('#modal-titulo').text('Nova Fatura');
    $('#fatura-id').val('');
    $('#form-fatura')[0].reset();
    preencherLookupsF();
    $('#modal-fatura').modal('show');
});

$(document).on('click', '.btn-editar', function () {
    const id = $(this).data('id');
    $.get(URLS_F.show + id, function (r) {
        if (!r.sucesso) return;
        const fatura = r.dado;
        $('#modal-titulo').text('Editar Fatura');
        $('#fatura-id').val(fatura.id);
        const f = $('#form-fatura');
        preencherLookupsF();
        f.find('[name="empresa_id"]').val(fatura.empresa_id);
        f.find('[name="assinatura_id"]').val(fatura.assinatura_id || '');
        f.find('[name="status"]').val(fatura.status);
        f.find('[name="competencia"]').val(fatura.competencia);
        f.find('[name="valor"]').val(toBr(fatura.valor));
        f.find('[name="vencimento_em"]').val(String(fatura.vencimento_em || '').replace(' ', 'T').slice(0,16));
        f.find('[name="gateway"]').val(fatura.gateway || '');
        f.find('[name="gateway_ref"]').val(fatura.gateway_ref || '');
        f.find('[name="link_pagamento"]').val(fatura.link_pagamento || '');
        f.find('[name="pix_copia_e_cola"]').val(fatura.pix_copia_e_cola || '');
        f.find('[name="boleto_linha_digitavel"]').val(fatura.boleto_linha_digitavel || '');
        f.find('[name="observacoes"]').val(fatura.observacoes || '');
        $('#modal-fatura').modal('show');
    });
});

$('#form-fatura').on('submit', function(ev){
    ev.preventDefault();
    const id = $('#fatura-id').val();
    const dados = {};
    $(this).serializeArray().forEach(f => dados[f.name] = f.value);
    dados.valor = parseMoneyBr(dados.valor);
    $.ajax({
        url: id ? URLS_F.update + id : URLS_F.store,
        type: id ? 'PUT' : 'POST',
        data: dados,
        success: r => { toast(r.mensagem || 'Salvo.', 'sucesso'); $('#modal-fatura').modal('hide'); carregarFaturas(paginaAtualF); },
        error: xhr => { const erros = xhr.responseJSON?.errors; toast(erros ? Object.values(erros).flat().join(' | ') : (xhr.responseJSON?.mensagem || 'Erro.'), 'erro'); },
    });
});

$(document).on('click', '.btn-excluir', function(){
    const id = $(this).data('id');
    confirmarExclusao(URLS_F.destroy + id, () => {
        $.ajax({
            url: URLS_F.destroy + id,
            type: 'DELETE',
            success: r => { toast(r.mensagem, 'sucesso'); carregarFaturas(paginaAtualF); },
            error: xhr => toast(xhr.responseJSON?.mensagem || 'Erro ao remover.', 'erro'),
        });
    });
});

$('#btn-filtrar').on('click', () => carregarFaturas(1));
$('#btn-limpar').on('click', () => { $('#filtro-search,#filtro-status').val(''); carregarFaturas(1); });
$('#filtro-search').on('keypress', e => { if (e.which === 13) carregarFaturas(1); });

carregarFaturas();
</script>
@endpush

