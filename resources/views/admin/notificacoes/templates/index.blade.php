@extends('layouts.admin.app')

@section('titulo', 'Notificacoes')
@section('titulo_pagina', 'Templates de Notificacoes')

@section('breadcrumb')
    <li class="breadcrumb-item">Administracao</li>
    <li class="breadcrumb-item active">Notificacoes</li>
@endsection

@push('styles')
<style>
.var-sistema { cursor: pointer; font-size: .8rem; transition: all .15s ease; }
.var-sistema:hover { transform: scale(1.05); box-shadow: 0 2px 6px rgba(0,0,0,.15); }
#preview-conteudo img { max-width: 100%; height: auto; }
#preview-conteudo table { width: 100%; }
</style>
@endpush

@section('conteudo')
<div class="card card-outline card-secondary mb-3">
    <div class="card-body py-2">
        <div class="row g-2 align-items-end">
            <div class="col-md-5">
                <label class="form-label small mb-1">Buscar</label>
                <input type="text" id="filtro-search" class="form-control form-control-sm" placeholder="Nome, chave ou canal...">
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
        <h3 class="card-title mb-0"><i class="bi bi-bell-fill text-primary me-2"></i>Templates</h3>
        <button class="btn btn-primary btn-sm" id="btn-novo"><i class="bi bi-plus-lg me-1"></i>Novo Template</button>
    </div>
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover mb-0">
                <thead class="table-light">
                    <tr>
                        <th>#</th>
                        <th>Template</th>
                        <th>Canal</th>
                        <th class="text-center">Ativo</th>
                        <th class="text-end" style="width: 110px;">Acoes</th>
                    </tr>
                </thead>
                <tbody id="tbody-templates">
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

<div class="modal fade" id="modal-template" tabindex="-1">
    <div class="modal-dialog modal-xl">
        <div class="modal-content">
            <div class="modal-header bg-primary text-white">
                <h5 class="modal-title"><i class="bi bi-bell me-2"></i><span id="modal-titulo">Novo Template</span></h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <form id="form-template">
                <div class="modal-body p-0">
                    <div class="row g-0">
                        {{-- Coluna Esquerda: Formulario --}}
                        <div class="col-md-7 border-end p-3">
                            <input type="hidden" id="template-id">
                            <div class="row g-3">
                                <div class="col-md-4">
                                    <label class="form-label fw-medium">Canal</label>
                                    <select class="form-select" name="canal" required>
                                        <option value="whatsapp">WhatsApp</option>
                                        <option value="email">E-mail</option>
                                        <option value="push">Push</option>
                                    </select>
                                </div>
                                <div class="col-md-8">
                                    <label class="form-label fw-medium">Nome</label>
                                    <input type="text" class="form-control" name="nome" required>
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label fw-medium">Chave</label>
                                    <input type="text" class="form-control" name="chave" required placeholder="ex: cadastro_boas_vindas">
                                    <div class="form-text">Somente letras, numeros, hifen e underline.</div>
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label fw-medium">Assunto (para e-mail)</label>
                                    <input type="text" class="form-control" name="assunto" placeholder="Assunto do e-mail">
                                </div>
                                <div class="col-12">
                                    <label class="form-label fw-medium d-flex align-items-center justify-content-between">
                                        <span>Conteúdo</span>
                                        <small class="text-muted">Clique nas variáveis abaixo para inserir</small>
                                    </label>
                                    <textarea class="form-control summernote-editor" name="conteudo" required></textarea>
                                </div>
                                <div class="col-12">
                                    <label class="form-label fw-medium small mb-2">Variáveis do Sistema <small class="text-muted fw-normal">(clique para inserir no editor)</small></label>
                                    <div class="d-flex flex-wrap gap-2" id="variaveis-sistema">
                                        <span class="badge bg-primary cursor-pointer var-sistema" data-var="&#123;&#123;nome&#125;&#125;">&#123;&#123;nome&#125;&#125;</span>
                                        <span class="badge bg-primary cursor-pointer var-sistema" data-var="&#123;&#123;email&#125;&#125;">&#123;&#123;email&#125;&#125;</span>
                                        <span class="badge bg-primary cursor-pointer var-sistema" data-var="&#123;&#123;telefone&#125;&#125;">&#123;&#123;telefone&#125;&#125;</span>
                                        <span class="badge bg-primary cursor-pointer var-sistema" data-var="&#123;&#123;empresa&#125;&#125;">&#123;&#123;empresa&#125;&#125;</span>
                                        <span class="badge bg-primary cursor-pointer var-sistema" data-var="&#123;&#123;valor&#125;&#125;">&#123;&#123;valor&#125;&#125;</span>
                                        <span class="badge bg-primary cursor-pointer var-sistema" data-var="&#123;&#123;data&#125;&#125;">&#123;&#123;data&#125;&#125;</span>
                                        <span class="badge bg-primary cursor-pointer var-sistema" data-var="&#123;&#123;numero_fatura&#125;&#125;">&#123;&#123;numero_fatura&#125;&#125;</span>
                                        <span class="badge bg-primary cursor-pointer var-sistema" data-var="&#123;&#123;vencimento&#125;&#125;">&#123;&#123;vencimento&#125;&#125;</span>
                                        <span class="badge bg-primary cursor-pointer var-sistema" data-var="&#123;&#123;link_pagamento&#125;&#125;">&#123;&#123;link_pagamento&#125;&#125;</span>
                                        <span class="badge bg-primary cursor-pointer var-sistema" data-var="&#123;&#123;dias_restantes&#125;&#125;">&#123;&#123;dias_restantes&#125;&#125;</span>
                                        <span class="badge bg-primary cursor-pointer var-sistema" data-var="&#123;&#123;competencia&#125;&#125;">&#123;&#123;competencia&#125;&#125;</span>
                                    </div>
                                </div>
                                <div class="col-12">
                                    <label class="form-label fw-medium">Variáveis Customizadas <small class="text-muted fw-normal">(uma por linha)</small></label>
                                    <textarea class="form-control" rows="2" name="variaveis" placeholder="&#123;&#123;var_custom&#125;&#125;"></textarea>
                                </div>
                                <div class="col-12">
                                    <div class="form-check form-switch">
                                        <input class="form-check-input" type="checkbox" role="switch" id="template-ativo" checked>
                                        <label class="form-check-label" for="template-ativo">Ativo</label>
                                    </div>
                                </div>
                            </div>
                        </div>

                        {{-- Coluna Direita: Preview --}}
                        <div class="col-md-5 bg-light p-3 d-flex flex-column" style="min-height: 500px;">
                            <h6 class="mb-3"><i class="bi bi-eye me-2 text-info"></i>Preview em Tempo Real</h6>
                            <div class="mb-3">
                                <label class="small text-muted fw-medium">Assunto:</label>
                                <div id="preview-assunto" class="p-2 bg-white rounded border small">-</div>
                            </div>
                            <div class="flex-grow-1">
                                <label class="small text-muted fw-medium">Conteúdo:</label>
                                <div id="preview-conteudo" class="p-3 bg-white rounded border small" style="min-height: 300px; overflow-y: auto;">-</div>
                            </div>
                            <div class="mt-3 p-2 bg-info-subtle rounded border border-info">
                                <small class="text-muted"><i class="bi bi-info-circle me-1"></i>As variáveis serão substituídas pelos dados reais no envio.</small>
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
$(document).ready(function() {
const URLS_T = {
    listar: '{{ route("admin.notificacoes.templates.listar") }}',
    store:  '{{ route("admin.notificacoes.templates.store") }}',
    show:   '{{ url("/admin/notificacoes/templates") }}/',
    update: '{{ url("/admin/notificacoes/templates") }}/',
    destroy:'{{ url("/admin/notificacoes/templates") }}/',
    preview: '{{ route("admin.notificacoes.templates.preview") }}',
};
let paginaAtualT = 1;
const perPageT = 10;

function carregarTemplates(pagina = 1) {
    console.log('carregarTemplates chamado, pagina:', pagina, 'URL:', URLS_T.listar);
    paginaAtualT = pagina;
    $.get(URLS_T.listar, { page: pagina, per_page: perPageT, search: $('#filtro-search').val() }, function (r) {
        console.log('Resposta do servidor:', r);
        const tbody = $('#tbody-templates').empty();
        if (!r.sucesso || !r.dados || !r.dados.length) {
            tbody.html('<tr><td colspan="5" class="text-center py-4 text-muted"><i class="bi bi-inbox fs-3 d-block mb-2"></i>Nenhum template encontrado.</td></tr>');
            $('#info-paginacao').text('0 registros');
            $('#paginacao').empty();
            return;
        }
        r.dados.forEach(t => {
            tbody.append(`<tr>
                <td class="text-muted small">${t.id}</td>
                <td><div class="fw-medium">${t.nome}</div><small class="text-muted">${t.chave}</small></td>
                <td><span class="badge bg-secondary">${t.canal}</span></td>
                <td class="text-center"><span class="badge bg-${t.ativo ? 'success' : 'secondary'}">${t.ativo ? 'Sim' : 'Nao'}</span></td>
                <td class="text-end"><div class="btn-group btn-group-sm">
                    <button class="btn btn-outline-primary btn-editar" data-id="${t.id}"><i class="bi bi-pencil"></i></button>
                    <button class="btn btn-outline-danger btn-excluir" data-id="${t.id}"><i class="bi bi-trash"></i></button>
                </div></td>
            </tr>`);
        });
        const ini = (pagina - 1) * perPageT + 1;
        const fim = Math.min(pagina * perPageT, r.total);
        $('#info-paginacao').text(`Exibindo ${ini}-${fim} de ${r.total} registros`);
        renderPagT(r.paginas, pagina);
    }).fail(function(xhr) {
        console.error('Erro ao carregar templates:', xhr);
        toast('Erro ao carregar templates. Verifique o console.', 'erro');
    });
}

function renderPagT(total, atual) {
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
    carregarTemplates(parseInt($(this).data('p')));
});

$('#btn-novo').on('click', () => {
    $('#modal-titulo').text('Novo Template');
    $('#template-id').val('');
    $('#form-template')[0].reset();
    $('.summernote-editor').summernote('code', '');
    $('#template-ativo').prop('checked', true);
    $('#preview-assunto').text('-');
    $('#preview-conteudo').html('-');
    $('#modal-template').modal('show');
});

$(document).on('click', '.btn-editar', function () {
    const id = $(this).data('id');
    $.get(URLS_T.show + id, function (r) {
        if (!r.sucesso) return;
        const t = r.dado;
        $('#modal-titulo').text('Editar Template');
        $('#template-id').val(t.id);
        const f = $('#form-template');
        f.find('[name="canal"]').val(t.canal);
        f.find('[name="nome"]').val(t.nome);
        f.find('[name="chave"]').val(t.chave);
        f.find('[name="assunto"]').val(t.assunto || '');
        $('.summernote-editor').summernote('code', t.conteudo || '');
        f.find('[name="variaveis"]').val((t.variaveis || []).join('\n'));
        $('#template-ativo').prop('checked', !!t.ativo);
        $('#modal-template').modal('show');
        // Dispara preview após abrir modal
        setTimeout(atualizarPreview, 300);
    });
});

$('#form-template').on('submit', function (e) {
    e.preventDefault();
    const id = $('#template-id').val();
    const dados = {};
    $(this).serializeArray().forEach(f => dados[f.name] = f.value);
    dados.ativo = $('#template-ativo').is(':checked') ? '1' : '0';
    $.ajax({
        url: id ? URLS_T.update + id : URLS_T.store,
        type: id ? 'PUT' : 'POST',
        data: dados,
        success: r => {
            toast(r.mensagem || 'Salvo.', 'sucesso');
            $('#modal-template').modal('hide');
            carregarTemplates(paginaAtualT);
        },
        error: xhr => {
            const erros = xhr.responseJSON?.errors;
            toast(erros ? Object.values(erros).flat().join(' | ') : (xhr.responseJSON?.mensagem || 'Erro ao salvar.'), 'erro');
        },
    });
});

$(document).on('click', '.btn-excluir', function () {
    const id = $(this).data('id');
    confirmarExclusao(URLS_T.destroy + id, () => {
        $.ajax({
            url: URLS_T.destroy + id,
            type: 'DELETE',
            success: r => { toast(r.mensagem, 'sucesso'); carregarTemplates(paginaAtualT); },
            error: xhr => toast(xhr.responseJSON?.mensagem || 'Erro ao remover.', 'erro'),
        });
    });
});

$('#btn-filtrar').on('click', () => carregarTemplates(1));
$('#btn-limpar').on('click', () => { $('#filtro-search').val(''); carregarTemplates(1); });
$('#filtro-search').on('keypress', e => { if (e.which === 13) carregarTemplates(1); });

// Inserir variável no Summernote ao clicar
$(document).on('click', '.var-sistema', function() {
    const variavel = $(this).data('var');
    $('.summernote-editor').summernote('editor.saveRange');
    $('.summernote-editor').summernote('editor.restoreRange');
    $('.summernote-editor').summernote('editor.focus');
    $('.summernote-editor').summernote('editor.insertText', variavel);
    toast('Variável inserida: ' + variavel, 'sucesso');
});

// Preview em tempo real durante digitação
let debounceTimer;
function atualizarPreview() {
    clearTimeout(debounceTimer);
    debounceTimer = setTimeout(() => {
        const chave = $('#form-template [name="chave"]').val();
        const conteudo = $('.summernote-editor').summernote('code');
        const assunto = $('#form-template [name="assunto"]').val();

        if (!chave) return;

        $.post(URLS_T.preview, { chave, conteudo, assunto }, function(r) {
            if (r.sucesso) {
                $('#preview-assunto').text(r.preview.assunto || '-');
                $('#preview-conteudo').html(r.preview.conteudo || '-');
            }
        });
    }, 500);
}

$('#form-template [name="chave"], #form-template [name="assunto"]').on('input change', atualizarPreview);

carregarTemplates();
}); // fecha $(document).ready
</script>
@endpush
