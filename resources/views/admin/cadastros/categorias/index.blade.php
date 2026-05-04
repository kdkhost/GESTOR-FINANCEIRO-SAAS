@extends('layouts.admin.app')

@section('titulo', 'Categorias')
@section('titulo_pagina', 'Categorias')

@section('breadcrumb')
    <li class="breadcrumb-item">Cadastros</li>
    <li class="breadcrumb-item active">Categorias</li>
@endsection

@section('conteudo')
<div class="card card-outline card-primary">
    <div class="card-header d-flex align-items-center justify-content-between">
        <h3 class="card-title mb-0"><i class="bi bi-tags me-2 text-primary"></i>Categorias</h3>
        <button class="btn btn-primary btn-sm" id="btn-novo"><i class="bi bi-plus-lg me-1"></i>Nova Categoria</button>
    </div>
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover mb-0">
                <thead class="table-light">
                    <tr>
                        <th>#</th>
                        <th>Nome</th>
                        <th>Tipo</th>
                        <th>Icone / Cor</th>
                        <th class="text-center">Status</th>
                        <th class="text-end" style="width:100px">Acoes</th>
                    </tr>
                </thead>
                <tbody id="tbody-categorias">
                    <tr><td colspan="6" class="text-center py-4 text-muted"><i class="bi bi-hourglass-split me-2"></i>Carregando...</td></tr>
                </tbody>
            </table>
        </div>
    </div>
</div>

{{-- Modal --}}
<div class="modal fade" id="modal-categoria" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header bg-primary text-white">
                <h5 class="modal-title"><i class="bi bi-tags me-2"></i><span id="modal-titulo">Nova Categoria</span></h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <form id="form-categoria">
                <div class="modal-body">
                    <input type="hidden" id="categoria-id">
                    <div class="row g-3">
                        <div class="col-md-8">
                            <label class="form-label fw-medium">Nome <span class="text-danger">*</span></label>
                            <input type="text" name="nome" class="form-control" required placeholder="Ex: Alimentacao, Salario...">
                        </div>
                        <div class="col-md-4">
                            <label class="form-label fw-medium">Tipo <span class="text-danger">*</span></label>
                            <select name="tipo" class="form-select" required>
                                <option value="">Selecione...</option>
                                <option value="receita">Receita</option>
                                <option value="despesa">Despesa</option>
                                <option value="ambos">Ambos</option>
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-medium">Icone Bootstrap</label>
                            <div class="input-group">
                                <span class="input-group-text" id="preview-icone"><i class="bi bi-tag"></i></span>
                                <input type="text" name="icone" id="input-icone" class="form-control" value="bi-tag" placeholder="bi-tag">
                            </div>
                            <small class="text-muted">Ex: bi-tag, bi-house, bi-car-front</small>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-medium">Cor</label>
                            <div class="input-group">
                                <input type="color" name="cor" id="input-cor" class="form-control form-control-color" value="#6c757d">
                                <input type="text" id="cor-hex" class="form-control" value="#6c757d" placeholder="#6c757d">
                            </div>
                        </div>
                        <div class="col-12">
                            <div class="form-check form-switch">
                                <input class="form-check-input" type="checkbox" name="ativo" id="ativo-check" value="1" checked>
                                <label class="form-check-label" for="ativo-check">Categoria ativa</label>
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
    index:  '{{ route("admin.categorias.index") }}',
    store:  '{{ route("admin.categorias.store") }}',
    show:   '/admin/categorias/',
    update: '/admin/categorias/',
    destroy:'/admin/categorias/',
};

// Sincronizar cor
document.getElementById('input-cor').addEventListener('input', function() {
    document.getElementById('cor-hex').value = this.value;
});
document.getElementById('cor-hex').addEventListener('input', function() {
    if (/^#[0-9A-Fa-f]{6}$/.test(this.value)) document.getElementById('input-cor').value = this.value;
});
document.getElementById('input-icone').addEventListener('input', function() {
    document.getElementById('preview-icone').innerHTML = `<i class="bi ${this.value}"></i>`;
});

function tipoBadge(tipo) {
    const map = { receita: ['success','Receita'], despesa: ['danger','Despesa'], ambos: ['info','Ambos'] };
    const [cor, label] = map[tipo] || ['secondary', tipo];
    return `<span class="badge bg-${cor}">${label}</span>`;
}

function carregarTabela() {
    $.get(URLS.index, function(r) {
        const tbody = $('#tbody-categorias');
        tbody.empty();
        if (!r.dados.length) {
            tbody.html('<tr><td colspan="6" class="text-center py-4 text-muted"><i class="bi bi-inbox fs-3 d-block mb-2"></i>Nenhuma categoria cadastrada.</td></tr>');
            return;
        }
        r.dados.forEach(c => {
            tbody.append(`<tr>
                <td class="text-muted small">${c.id}</td>
                <td><span class="fw-medium">${c.nome}</span></td>
                <td>${tipoBadge(c.tipo)}</td>
                <td>
                    <i class="bi ${c.icone || 'bi-tag'} me-2" style="color:${c.cor || '#6c757d'};font-size:1.2rem;"></i>
                    <span class="badge" style="background:${c.cor || '#6c757d'}">${c.cor || '#6c757d'}</span>
                </td>
                <td class="text-center">
                    <span class="badge bg-${c.ativo ? 'success' : 'secondary'}">${c.ativo ? 'Ativa' : 'Inativa'}</span>
                </td>
                <td class="text-end">
                    <div class="btn-group btn-group-sm">
                        <button class="btn btn-outline-primary btn-editar" data-id="${c.id}"><i class="bi bi-pencil"></i></button>
                        <button class="btn btn-outline-danger btn-excluir" data-id="${c.id}"><i class="bi bi-trash"></i></button>
                    </div>
                </td>
            </tr>`);
        });
    }).fail(() => toast('Erro ao carregar categorias.', 'erro'));
}

$('#btn-novo').on('click', () => {
    $('#modal-titulo').text('Nova Categoria');
    $('#categoria-id').val('');
    $('#form-categoria')[0].reset();
    $('#input-cor').val('#6c757d');
    $('#cor-hex').val('#6c757d');
    $('#input-icone').val('bi-tag');
    $('#preview-icone').html('<i class="bi bi-tag"></i>');
    $('#ativo-check').prop('checked', true);
    $('#modal-categoria').modal('show');
});

$(document).on('click', '.btn-editar', function() {
    $.get(URLS.show + $(this).data('id'), r => {
        if (!r.sucesso) return;
        const c = r.dado;
        $('#modal-titulo').text('Editar Categoria');
        $('#categoria-id').val(c.id);
        const f = $('#form-categoria');
        f.find('[name="nome"]').val(c.nome);
        f.find('[name="tipo"]').val(c.tipo);
        f.find('[name="icone"]').val(c.icone || 'bi-tag');
        f.find('[name="cor"]').val(c.cor || '#6c757d');
        $('#cor-hex').val(c.cor || '#6c757d');
        $('#preview-icone').html(`<i class="bi ${c.icone || 'bi-tag'}"></i>`);
        $('#ativo-check').prop('checked', !!c.ativo);
        $('#modal-categoria').modal('show');
    });
});

$('#form-categoria').on('submit', function(e) {
    e.preventDefault();
    const id = $('#categoria-id').val();
    const dados = {};
    $(this).serializeArray().forEach(f => dados[f.name] = f.value);
    dados.ativo = $('#ativo-check').is(':checked') ? 1 : 0;
    $.ajax({
        url: id ? URLS.update + id : URLS.store,
        type: id ? 'PUT' : 'POST',
        data: dados,
        success: r => { if (r.sucesso) { toast(r.mensagem,'sucesso'); $('#modal-categoria').modal('hide'); carregarTabela(); } else toast(r.mensagem||'Erro.','erro'); },
        error: r => toast(r.responseJSON?.mensagem||'Erro ao salvar.','erro'),
    });
});

$(document).on('click', '.btn-excluir', function() {
    const id = $(this).data('id');
    confirmarExclusao(URLS.destroy+id, () => {
        $.ajax({ url: URLS.destroy+id, type: 'DELETE',
            success: r => { toast(r.mensagem,'sucesso'); carregarTabela(); },
            error: r => toast(r.responseJSON?.mensagem||'Erro.','erro'),
        });
    });
});

carregarTabela();
</script>
@endpush
