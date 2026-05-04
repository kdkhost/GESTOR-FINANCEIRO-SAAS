@extends('layouts.admin.app')

@section('titulo', 'Clientes')
@section('titulo_pagina', 'Clientes')

@section('breadcrumb')
    <li class="breadcrumb-item">Cadastros</li>
    <li class="breadcrumb-item active">Clientes</li>
@endsection

@section('conteudo')
<div class="card card-outline card-primary mb-3">
    <div class="card-body py-2">
        <div class="row g-2 align-items-end">
            <div class="col-md-4">
                <label class="form-label small mb-1">Buscar</label>
                <input type="text" id="filtro-search" class="form-control form-control-sm" placeholder="Nome, CPF/CNPJ, e-mail...">
            </div>
            <div class="col-md-2">
                <label class="form-label small mb-1">Status</label>
                <select id="filtro-ativo" class="form-select form-select-sm">
                    <option value="">Todos</option>
                    <option value="1">Ativo</option>
                    <option value="0">Inativo</option>
                </select>
            </div>
            <div class="col-md-2 d-flex gap-2">
                <button class="btn btn-primary btn-sm" id="btn-filtrar"><i class="bi bi-search me-1"></i>Filtrar</button>
                <button class="btn btn-outline-secondary btn-sm" id="btn-limpar"><i class="bi bi-x-lg"></i></button>
            </div>
        </div>
    </div>
</div>

<div class="card card-outline card-primary">
    <div class="card-header d-flex align-items-center justify-content-between">
        <h3 class="card-title mb-0"><i class="bi bi-people me-2 text-primary"></i>Clientes</h3>
        <button class="btn btn-primary btn-sm" id="btn-novo"><i class="bi bi-plus-lg me-1"></i>Novo Cliente</button>
    </div>
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover mb-0">
                <thead class="table-light">
                    <tr>
                        <th>#</th>
                        <th>Nome</th>
                        <th>Tipo</th>
                        <th>CPF/CNPJ</th>
                        <th>Telefone</th>
                        <th class="text-center">Status</th>
                        <th class="text-end" style="width:100px">Acoes</th>
                    </tr>
                </thead>
                <tbody id="tbody-clientes">
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

{{-- Modal --}}
<div class="modal fade" id="modal-cliente" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header bg-primary text-white">
                <h5 class="modal-title"><i class="bi bi-person-plus me-2"></i><span id="modal-titulo">Novo Cliente</span></h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <form id="form-cliente">
                <div class="modal-body">
                    <input type="hidden" id="cliente-id">
                    <ul class="nav nav-tabs mb-3" id="tabs-cliente">
                        <li class="nav-item"><a class="nav-link active" data-bs-toggle="tab" href="#tab-dados">Dados Principais</a></li>
                        <li class="nav-item"><a class="nav-link" data-bs-toggle="tab" href="#tab-endereco">Endereco</a></li>
                    </ul>
                    <div class="tab-content">
                        <div class="tab-pane fade show active" id="tab-dados">
                            <div class="row g-3">
                                <div class="col-md-6">
                                    <label class="form-label fw-medium">Nome <span class="text-danger">*</span></label>
                                    <input type="text" name="nome" class="form-control" required placeholder="Nome completo ou razao social">
                                </div>
                                <div class="col-md-3">
                                    <label class="form-label fw-medium">Tipo Pessoa <span class="text-danger">*</span></label>
                                    <select name="tipo_pessoa" class="form-select" required id="tipo-pessoa-cliente">
                                        <option value="">Selecione...</option>
                                        <option value="fisica">Fisica</option>
                                        <option value="juridica">Juridica</option>
                                    </select>
                                </div>
                                <div class="col-md-3">
                                    <label class="form-label fw-medium" id="label-cpf-cnpj">CPF/CNPJ</label>
                                    <input type="text" name="cpf_cnpj" id="input-cpf-cnpj-cliente" class="form-control" placeholder="000.000.000-00">
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label fw-medium">E-mail</label>
                                    <input type="email" name="email" class="form-control" placeholder="email@exemplo.com">
                                </div>
                                <div class="col-md-3">
                                    <label class="form-label fw-medium">Telefone</label>
                                    <input type="text" name="telefone" class="form-control mask-telefone" placeholder="(00) 00000-0000">
                                </div>
                                <div class="col-md-3">
                                    <label class="form-label fw-medium">Celular</label>
                                    <input type="text" name="celular" class="form-control mask-telefone" placeholder="(00) 00000-0000">
                                </div>
                                <div class="col-12">
                                    <label class="form-label fw-medium">Observacoes</label>
                                    <textarea name="observacoes" class="form-control" rows="2"></textarea>
                                </div>
                            </div>
                        </div>
                        <div class="tab-pane fade" id="tab-endereco">
                            <div class="row g-3">
                                <div class="col-md-3">
                                    <label class="form-label fw-medium">CEP</label>
                                    <input type="text" name="cep" class="form-control mask-cep viacep" placeholder="00000-000">
                                </div>
                                <div class="col-md-7">
                                    <label class="form-label fw-medium">Logradouro</label>
                                    <input type="text" name="logradouro" class="form-control" placeholder="Rua, Avenida...">
                                </div>
                                <div class="col-md-2">
                                    <label class="form-label fw-medium">Numero</label>
                                    <input type="text" name="numero" class="form-control" placeholder="123">
                                </div>
                                <div class="col-md-4">
                                    <label class="form-label fw-medium">Complemento</label>
                                    <input type="text" name="complemento" class="form-control" placeholder="Apto, Sala...">
                                </div>
                                <div class="col-md-4">
                                    <label class="form-label fw-medium">Bairro</label>
                                    <input type="text" name="bairro" class="form-control">
                                </div>
                                <div class="col-md-3">
                                    <label class="form-label fw-medium">Cidade</label>
                                    <input type="text" name="cidade" class="form-control">
                                </div>
                                <div class="col-md-1">
                                    <label class="form-label fw-medium">UF</label>
                                    <input type="text" name="estado" class="form-control" maxlength="2">
                                </div>
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
const URLS = {
    listar: '{{ route("admin.clientes.listar") }}',
    store:  '{{ route("admin.clientes.store") }}',
    show:   '/admin/clientes/',
    update: '/admin/clientes/',
    destroy:'/admin/clientes/',
};
let paginaAtual = 1;
const perPage = 10;

// Mascara CPF/CNPJ dinamica
document.getElementById('tipo-pessoa-cliente').addEventListener('change', function() {
    const input = document.getElementById('input-cpf-cnpj-cliente');
    const label = document.getElementById('label-cpf-cnpj');
    if (this.value === 'fisica') {
        label.textContent = 'CPF';
        input.placeholder = '000.000.000-00';
        if (window._maskCpfCnpj) window._maskCpfCnpj.destroy();
        window._maskCpfCnpj = IMask(input, { mask: '000.000.000-00' });
    } else if (this.value === 'juridica') {
        label.textContent = 'CNPJ';
        input.placeholder = '00.000.000/0000-00';
        if (window._maskCpfCnpj) window._maskCpfCnpj.destroy();
        window._maskCpfCnpj = IMask(input, { mask: '00.000.000/0000-00' });
    }
});

function carregarTabela(pagina = 1) {
    paginaAtual = pagina;
    $.get(URLS.listar, {
        search: $('#filtro-search').val(),
        ativo: $('#filtro-ativo').val(),
        page: pagina, per_page: perPage,
    }, function(r) {
        const tbody = $('#tbody-clientes');
        tbody.empty();
        if (!r.dados || !r.dados.length) {
            tbody.html('<tr><td colspan="7" class="text-center py-4 text-muted"><i class="bi bi-inbox fs-3 d-block mb-2"></i>Nenhum cliente encontrado.</td></tr>');
            $('#info-paginacao').text('0 registros'); $('#paginacao').empty(); return;
        }
        r.dados.forEach(c => {
            tbody.append(`<tr>
                <td class="text-muted small">${c.id}</td>
                <td><div class="fw-medium">${c.nome}</div>${c.email ? '<small class="text-muted">'+c.email+'</small>' : ''}</td>
                <td><span class="badge bg-${c.tipo_pessoa==='fisica'?'info':'warning'}">${c.tipo_pessoa==='fisica'?'Fisica':'Juridica'}</span></td>
                <td>${c.cpf_cnpj || '<span class="text-muted">-</span>'}</td>
                <td>${c.telefone || '<span class="text-muted">-</span>'}</td>
                <td class="text-center"><span class="badge bg-${c.ativo?'success':'secondary'}">${c.ativo?'Ativo':'Inativo'}</span></td>
                <td class="text-end">
                    <div class="btn-group btn-group-sm">
                        <button class="btn btn-outline-primary btn-editar" data-id="${c.id}"><i class="bi bi-pencil"></i></button>
                        <button class="btn btn-outline-danger btn-excluir" data-id="${c.id}"><i class="bi bi-trash"></i></button>
                    </div>
                </td>
            </tr>`);
        });
        const ini = (pagina-1)*perPage+1, fim = Math.min(pagina*perPage, r.total||r.dados.length);
        $('#info-paginacao').text(`Exibindo ${ini}-${fim} de ${r.total||r.dados.length} registros`);
        if (r.paginas) renderPaginacao(r.paginas, pagina);
    }).fail(() => toast('Erro ao carregar clientes.', 'erro'));
}

$(document).on('click', '#paginacao a[data-p]', function(e) { e.preventDefault(); carregarTabela(parseInt($(this).data('p'))); });

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

$('#btn-novo').on('click', () => { $('#modal-titulo').text('Novo Cliente'); $('#cliente-id').val(''); $('#form-cliente')[0].reset(); $('#modal-cliente').modal('show'); });

$(document).on('click', '.btn-editar', function() {
    $.get(URLS.show + $(this).data('id'), r => {
        if (!r.sucesso) return;
        const c = r.dado;
        $('#modal-titulo').text('Editar Cliente'); $('#cliente-id').val(c.id);
        const f = $('#form-cliente');
        ['nome','tipo_pessoa','cpf_cnpj','email','telefone','celular','observacoes','cep','logradouro','numero','complemento','bairro','cidade','estado'].forEach(k => f.find(`[name="${k}"]`).val(c[k] || ''));
        $('#modal-cliente').modal('show');
    });
});

$('#form-cliente').on('submit', function(e) {
    e.preventDefault();
    const id = $('#cliente-id').val();
    const dados = {};
    $(this).serializeArray().forEach(f => dados[f.name] = f.value);
    $.ajax({
        url: id ? URLS.update + id : URLS.store,
        type: id ? 'PUT' : 'POST',
        data: dados,
        success: r => { if (r.sucesso) { toast(r.mensagem,'sucesso'); $('#modal-cliente').modal('hide'); carregarTabela(paginaAtual); } else toast(r.mensagem||'Erro.','erro'); },
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
$('#btn-limpar').on('click', () => { $('#filtro-search,#filtro-ativo').val(''); carregarTabela(1); });
$('#filtro-search').on('keypress', e => { if (e.which===13) carregarTabela(1); });

carregarTabela();
</script>
@endpush
