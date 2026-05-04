@extends('layouts.admin.app')

@section('titulo', 'Permissões')
@section('titulo_pagina', 'Permissões e Acessos')

@section('breadcrumb')
    <li class="breadcrumb-item">Administração</li>
    <li class="breadcrumb-item active">Permissões</li>
@endsection

@section('conteudo')
@if (! $tabelasOk)
    <div class="alert alert-warning d-flex align-items-center gap-2">
        <i class="bi bi-exclamation-triangle fs-4"></i>
        <div>
            <strong>Tabelas de permissões ausentes.</strong>
            Execute as migrations pelo instalador antes de gerenciar roles e permissões.
        </div>
    </div>
@endif

<div class="row g-3">
    <div class="col-xl-4">
        <div class="card card-outline card-primary h-100">
            <div class="card-header d-flex align-items-center justify-content-between">
                <h3 class="card-title mb-0"><i class="bi bi-shield-lock text-primary me-2"></i>Papéis</h3>
                <button class="btn btn-primary btn-sm" id="btn-novo-papel" @disabled(! $tabelasOk)>
                    <i class="bi bi-plus-lg me-1"></i>Novo papel
                </button>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0">
                        <thead class="table-light">
                            <tr>
                                <th>Papel</th>
                                <th class="text-center">Permissões</th>
                                <th class="text-end" style="width: 98px;">Ações</th>
                            </tr>
                        </thead>
                        <tbody id="tbody-roles">
                            <tr><td colspan="3" class="text-center py-4 text-muted">Carregando...</td></tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <div class="col-xl-8">
        <div class="card card-outline card-secondary">
            <div class="card-header d-flex align-items-center justify-content-between">
                <div>
                    <h3 class="card-title mb-0"><i class="bi bi-ui-checks-grid text-secondary me-2"></i>Matriz granular</h3>
                    <small class="text-muted" id="papel-selecionado-label">Selecione um papel para editar.</small>
                </div>
                <button class="btn btn-success btn-sm" id="btn-salvar-matriz" disabled>
                    <i class="bi bi-check2 me-1"></i>Salvar matriz
                </button>
            </div>
            <div class="card-body">
                <div id="matriz-permissoes" class="row g-3"></div>
            </div>
        </div>

        <div class="card card-outline card-info mt-3">
            <div class="card-header d-flex align-items-center justify-content-between">
                <div>
                    <h3 class="card-title mb-0"><i class="bi bi-people text-info me-2"></i>Usuários por papel</h3>
                    <small class="text-muted">Atribuição direta de roles aos usuários cadastrados.</small>
                </div>
                <div class="input-group input-group-sm" style="max-width: 280px;">
                    <input type="text" id="busca-usuario" class="form-control" placeholder="Nome ou e-mail">
                    <button class="btn btn-outline-secondary" id="btn-buscar-usuario"><i class="bi bi-search"></i></button>
                </div>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0">
                        <thead class="table-light">
                            <tr>
                                <th>Usuário</th>
                                <th>Status</th>
                                <th>Roles</th>
                                <th class="text-end" style="width: 90px;">Ação</th>
                            </tr>
                        </thead>
                        <tbody id="tbody-usuarios-roles">
                            <tr><td colspan="4" class="text-center py-4 text-muted">Carregando...</td></tr>
                        </tbody>
                    </table>
                </div>
            </div>
            <div class="card-footer d-flex align-items-center justify-content-between">
                <span class="text-muted small" id="info-usuarios">0 registros</span>
                <nav><ul class="pagination pagination-sm mb-0" id="paginacao-usuarios"></ul></nav>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="modal-papel" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header bg-primary text-white">
                <h5 class="modal-title"><i class="bi bi-shield-plus me-2"></i>Novo papel</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <form id="form-papel">
                <div class="modal-body">
                    <label class="form-label">Identificador</label>
                    <input type="text" name="name" class="form-control" required placeholder="ex: financeiro, atendimento, gerente">
                    <small class="text-muted">Use letras, números, hífen ou underline. O nome será salvo em minúsculas.</small>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Cancelar</button>
                    <button type="submit" class="btn btn-primary"><i class="bi bi-check-lg me-1"></i>Criar</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
const permissoesGrupos = @json($gruposPermissoes);
const urlsPerm = {
    listar: '{{ route('admin.permissoes.listar') }}',
    store: '{{ route('admin.permissoes.roles.store') }}',
    show: '/admin/permissoes/roles/',
    update: '/admin/permissoes/roles/',
    destroy: '/admin/permissoes/roles/',
    usuarios: '{{ route('admin.permissoes.usuarios') }}',
    usuarioRoles: '/admin/permissoes/usuarios/',
};
let rolesPerm = [];
let roleSelecionado = null;
let paginaUsuarios = 1;
let rolesNomes = [];

function nomeHumanoRole(nome) {
    const mapa = { superadmin: 'Superadmin', administrador: 'Administrador', financeiro: 'Financeiro', operador: 'Operador' };
    return mapa[nome] || nome.replace(/[-_]/g, ' ').replace(/\b\w/g, letra => letra.toUpperCase());
}

function carregarPermissoes() {
    $.get(urlsPerm.listar, function (r) {
        rolesPerm = r.roles || [];
        rolesNomes = rolesPerm.map(role => role.name);
        renderRoles();
        if (!roleSelecionado && rolesPerm.length) selecionarRole(rolesPerm[0].id);
        carregarUsuariosRoles(paginaUsuarios);
    }).fail(xhr => toast(xhr.responseJSON?.mensagem || 'Erro ao carregar permissões.', 'erro'));
}

function renderRoles() {
    const tbody = $('#tbody-roles').empty();
    if (!rolesPerm.length) {
        tbody.html('<tr><td colspan="3" class="text-center py-4 text-muted">Nenhum papel cadastrado.</td></tr>');
        return;
    }
    rolesPerm.forEach(role => {
        const ativo = roleSelecionado && roleSelecionado.id === role.id ? 'table-primary' : '';
        tbody.append(`<tr class="${ativo}">
            <td>
                <button class="btn btn-link p-0 fw-bold text-decoration-none btn-selecionar-role" data-id="${role.id}">${nomeHumanoRole(role.name)}</button>
                <small class="text-muted d-block">${role.name}</small>
            </td>
            <td class="text-center"><span class="badge text-bg-light border">${role.total_permissions}</span></td>
            <td class="text-end">
                <div class="btn-group btn-group-sm">
                    <button class="btn btn-outline-primary btn-selecionar-role" data-id="${role.id}"><i class="bi bi-pencil"></i></button>
                    <button class="btn btn-outline-danger btn-excluir-role" data-id="${role.id}" ${role.bloqueado ? 'disabled' : ''}><i class="bi bi-trash"></i></button>
                </div>
            </td>
        </tr>`);
    });
}

function selecionarRole(id) {
    roleSelecionado = rolesPerm.find(role => Number(role.id) === Number(id));
    renderRoles();
    renderMatriz();
}

function renderMatriz() {
    const alvo = $('#matriz-permissoes').empty();
    if (!roleSelecionado) {
        $('#papel-selecionado-label').text('Selecione um papel para editar.');
        $('#btn-salvar-matriz').prop('disabled', true);
        return;
    }
    $('#papel-selecionado-label').text(`Editando: ${nomeHumanoRole(roleSelecionado.name)}`);
    $('#btn-salvar-matriz').prop('disabled', false);
    const marcadas = new Set(roleSelecionado.permissions || []);

    Object.entries(permissoesGrupos).forEach(([grupo, permissoes]) => {
        let checks = '';
        Object.entries(permissoes).forEach(([nome, label]) => {
            const checked = marcadas.has(nome) ? 'checked' : '';
            const disabled = roleSelecionado.name === 'superadmin' ? 'disabled' : '';
            checks += `<div class="form-check">
                <input class="form-check-input chk-permissao" type="checkbox" value="${nome}" id="perm-${nome}" ${checked} ${disabled}>
                <label class="form-check-label" for="perm-${nome}">${label}<small class="text-muted d-block">${nome}</small></label>
            </div>`;
        });
        alvo.append(`<div class="col-md-6">
            <div class="border rounded p-3 h-100">
                <div class="fw-bold mb-2"><i class="bi bi-folder2-open text-primary me-1"></i>${grupo}</div>
                <div class="d-grid gap-2">${checks}</div>
            </div>
        </div>`);
    });
}

$(document).on('click', '.btn-selecionar-role', function () {
    selecionarRole($(this).data('id'));
});

$('#btn-novo-papel').on('click', function () {
    $('#form-papel')[0].reset();
    $('#modal-papel').modal('show');
});

$('#form-papel').on('submit', function (e) {
    e.preventDefault();
    $.post(urlsPerm.store, $(this).serialize())
        .done(r => {
            toast(r.mensagem, 'sucesso');
            $('#modal-papel').modal('hide');
            roleSelecionado = null;
            carregarPermissoes();
        })
        .fail(xhr => {
            const erros = xhr.responseJSON?.errors;
            toast(erros ? Object.values(erros).flat().join(' | ') : (xhr.responseJSON?.mensagem || 'Erro ao criar papel.'), 'erro');
        });
});

$('#btn-salvar-matriz').on('click', function () {
    if (!roleSelecionado) return;
    const permissoes = roleSelecionado.name === 'superadmin'
        ? Object.values(permissoesGrupos).flatMap(grupo => Object.keys(grupo))
        : $('.chk-permissao:checked').map((_, el) => el.value).get();
    $.ajax({
        url: urlsPerm.update + roleSelecionado.id,
        type: 'PUT',
        data: { name: roleSelecionado.name, permissions: permissoes },
        success: r => {
            toast(r.mensagem, 'sucesso');
            carregarPermissoes();
        },
        error: xhr => toast(xhr.responseJSON?.mensagem || 'Erro ao salvar permissões.', 'erro'),
    });
});

$(document).on('click', '.btn-excluir-role', function () {
    const id = $(this).data('id');
    confirmarExclusao(urlsPerm.destroy + id, () => {
        $.ajax({
            url: urlsPerm.destroy + id,
            type: 'DELETE',
            success: r => {
                toast(r.mensagem, 'sucesso');
                roleSelecionado = null;
                carregarPermissoes();
            },
            error: xhr => toast(xhr.responseJSON?.mensagem || 'Erro ao excluir papel.', 'erro'),
        });
    });
});

function carregarUsuariosRoles(pagina = 1) {
    paginaUsuarios = pagina;
    $.get(urlsPerm.usuarios, { page: pagina, per_page: 10, search: $('#busca-usuario').val() }, function (r) {
        const tbody = $('#tbody-usuarios-roles').empty();
        if (!r.dados.length) {
            tbody.html('<tr><td colspan="4" class="text-center py-4 text-muted">Nenhum usuário encontrado.</td></tr>');
            $('#info-usuarios').text('0 registros');
            return;
        }

        r.dados.forEach(usuario => {
            const options = rolesNomes.map(role => `<option value="${role}" ${usuario.roles.includes(role) ? 'selected' : ''}>${nomeHumanoRole(role)}</option>`).join('');
            tbody.append(`<tr>
                <td><div class="fw-bold">${usuario.name}</div><small class="text-muted">${usuario.email}</small></td>
                <td><span class="badge text-bg-${usuario.status === 'ativo' ? 'success' : 'secondary'}">${usuario.status}</span></td>
                <td><select class="form-select form-select-sm select-roles-usuario" data-id="${usuario.id}" multiple>${options}</select></td>
                <td class="text-end"><button class="btn btn-sm btn-outline-primary btn-salvar-usuario-role" data-id="${usuario.id}"><i class="bi bi-check2"></i></button></td>
            </tr>`);
        });

        const ini = (pagina - 1) * 10 + 1;
        const fim = Math.min(pagina * 10, r.total);
        $('#info-usuarios').text(`Exibindo ${ini}-${fim} de ${r.total} registros`);
        renderPaginacaoUsuarios(r.paginas, pagina);
    }).fail(xhr => toast(xhr.responseJSON?.mensagem || 'Erro ao carregar usuários.', 'erro'));
}

function renderPaginacaoUsuarios(total, atual) {
    const ul = $('#paginacao-usuarios').empty();
    if (total <= 1) return;
    ul.append(`<li class="page-item ${atual === 1 ? 'disabled' : ''}"><a class="page-link" href="#" data-p="${atual - 1}">&laquo;</a></li>`);
    for (let i = 1; i <= total; i++) {
        if (i === 1 || i === total || Math.abs(i - atual) <= 2) {
            ul.append(`<li class="page-item ${i === atual ? 'active' : ''}"><a class="page-link" href="#" data-p="${i}">${i}</a></li>`);
        }
    }
    ul.append(`<li class="page-item ${atual === total ? 'disabled' : ''}"><a class="page-link" href="#" data-p="${atual + 1}">&raquo;</a></li>`);
}

$(document).on('click', '#paginacao-usuarios a[data-p]', function (e) {
    e.preventDefault();
    carregarUsuariosRoles(Number($(this).data('p')));
});

$('#btn-buscar-usuario').on('click', () => carregarUsuariosRoles(1));
$('#busca-usuario').on('keypress', e => { if (e.which === 13) carregarUsuariosRoles(1); });

$(document).on('click', '.btn-salvar-usuario-role', function () {
    const id = $(this).data('id');
    const roles = $(`.select-roles-usuario[data-id="${id}"]`).val() || [];
    $.post(`${urlsPerm.usuarioRoles}${id}/roles`, { roles })
        .done(r => toast(r.mensagem, 'sucesso'))
        .fail(xhr => toast(xhr.responseJSON?.mensagem || 'Erro ao atualizar usuário.', 'erro'));
});

carregarPermissoes();
</script>
@endpush
