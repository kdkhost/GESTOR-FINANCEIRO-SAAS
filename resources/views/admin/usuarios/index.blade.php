@extends('layouts.admin.app')
@section('titulo', 'Usuarios')
@section('titulo_pagina', 'Gerenciamento de Usuarios')
@section('breadcrumb')
    <li class="breadcrumb-item">Administracao</li>
    <li class="breadcrumb-item active">Usuarios</li>
@endsection
@section('conteudo')
<div class="card card-outline card-secondary mb-3">
    <div class="card-body py-2">
        <div class="row g-2 align-items-end">
            <div class="col-md-4"><label class="form-label small mb-1">Buscar</label>
                <input type="text" id="filtro-search" class="form-control form-control-sm" placeholder="Nome, e-mail..."></div>
            <div class="col-md-2"><label class="form-label small mb-1">Status</label>
                <select id="filtro-status" class="form-select form-select-sm">
                    <option value="">Todos</option>
                    <option value="ativo">Ativo</option>
                    <option value="inativo">Inativo</option>
                    <option value="bloqueado">Bloqueado</option>
                </select></div>
            <div class="col-md-2 d-flex gap-2">
                <button class="btn btn-primary btn-sm" id="btn-filtrar"><i class="bi bi-search me-1"></i>Filtrar</button>
                <button class="btn btn-outline-secondary btn-sm" id="btn-limpar"><i class="bi bi-x-lg"></i></button>
            </div>
        </div>
    </div>
</div>
<div class="card card-outline card-primary">
    <div class="card-header d-flex align-items-center justify-content-between">
        <h3 class="card-title mb-0"><i class="bi bi-people-fill text-primary me-2"></i>Usuarios</h3>
        <button class="btn btn-primary btn-sm" id="btn-novo"><i class="bi bi-plus-lg me-1"></i>Novo Usuario</button>
    </div>
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover mb-0">
                <thead class="table-light">
                    <tr><th>#</th><th>Usuario</th><th>Tipo</th><th>Ultimo Acesso</th><th class="text-center">Status</th><th class="text-end" style="width:100px">Acoes</th></tr>
                </thead>
                <tbody id="tbody-usuarios">
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
<div class="modal fade" id="modal-usuario" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header bg-primary text-white">
                <h5 class="modal-title"><i class="bi bi-person-plus me-2"></i><span id="modal-usuario-titulo">Novo Usuario</span></h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <form id="form-usuario">
                <div class="modal-body">
                    <input type="hidden" id="usuario-id">
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label fw-medium">Nome <span class="text-danger">*</span></label>
                            <input type="text" name="name" class="form-control" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-medium">E-mail <span class="text-danger">*</span></label>
                            <input type="email" name="email" class="form-control" required>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label fw-medium">Tipo</label>
                            <select name="tipo" class="form-select">
                                <option value="usuario">Usuario</option>
                                <option value="admin">Administrador</option>
                            </select>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label fw-medium">Status</label>
                            <select name="status" class="form-select">
                                <option value="ativo">Ativo</option>
                                <option value="inativo">Inativo</option>
                            </select>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label fw-medium">Senha <span id="senha-obrig" class="text-danger">*</span></label>
                            <input type="password" name="password" id="input-senha-usuario" class="form-control" placeholder="Minimo 8 caracteres">
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
const URLS_U = { listar: '/admin/usuarios/listar', store: '/admin/usuarios', show: '/admin/usuarios/', update: '/admin/usuarios/', destroy: '/admin/usuarios/' };
let paginaAtual = 1; const perPage = 10;

function carregarTabela(pagina=1) {
    paginaAtual = pagina;
    $.get(URLS_U.listar, {page:pagina,per_page:perPage,search:$('#filtro-search').val(),status:$('#filtro-status').val()}, function(r) {
        const tbody = $('#tbody-usuarios'); tbody.empty();
        if (!r.sucesso||!r.dados.length) { tbody.html('<tr><td colspan="6" class="text-center py-4 text-muted"><i class="bi bi-inbox fs-3 d-block mb-2"></i>Nenhum usuario encontrado.</td></tr>'); $('#info-paginacao').text('0 registros'); return; }
        r.dados.forEach(u => {
            const statusMap = {ativo:'success',inativo:'secondary',bloqueado:'danger'};
            const tipoMap = {superadmin:'danger',admin:'warning',usuario:'primary'};
            tbody.append(`<tr>
                <td class="text-muted small">${u.id}</td>
                <td><div class="d-flex align-items-center gap-2">
                    <img src="${u.avatar_url||'/images/avatar-padrao.png'}" class="rounded-circle" width="32" height="32" style="object-fit:cover;">
                    <div><div class="fw-medium">${u.name}</div><small class="text-muted">${u.email}</small></div>
                </div></td>
                <td><span class="badge bg-${tipoMap[u.tipo]||'primary'}">${u.tipo}</span></td>
                <td>${u.ultimo_acesso_em?u.ultimo_acesso_em.substring(0,16).replace('T',' '):'<span class="text-muted">Nunca</span>'}</td>
                <td class="text-center"><span class="badge bg-${statusMap[u.status]||'secondary'}">${u.status}</span></td>
                <td class="text-end"><div class="btn-group btn-group-sm">
                    <button class="btn btn-outline-primary btn-editar-u" data-id="${u.id}"><i class="bi bi-pencil"></i></button>
                    <button class="btn btn-outline-danger btn-excluir-u" data-id="${u.id}" ${u.tipo==='superadmin'?'disabled':''}><i class="bi bi-trash"></i></button>
                </div></td>
            </tr>`);
        });
        const ini=(pagina-1)*perPage+1,fim=Math.min(pagina*perPage,r.total);
        $('#info-paginacao').text(`Exibindo ${ini}-${fim} de ${r.total} registros`);
        renderPag(r.paginas,pagina);
    }).fail(()=>toast('Erro ao carregar usuarios.','erro'));
}

function renderPag(total,atual) {
    const ul=$('#paginacao');ul.empty();if(total<=1)return;
    ul.append(`<li class="page-item ${atual===1?'disabled':''}"><a class="page-link" href="#" data-p="${atual-1}">&laquo;</a></li>`);
    for(let i=1;i<=total;i++){if(i===1||i===total||Math.abs(i-atual)<=2)ul.append(`<li class="page-item ${i===atual?'active':''}"><a class="page-link" href="#" data-p="${i}">${i}</a></li>`);else if(Math.abs(i-atual)===3)ul.append('<li class="page-item disabled"><span class="page-link">...</span></li>');}
    ul.append(`<li class="page-item ${atual===total?'disabled':''}"><a class="page-link" href="#" data-p="${atual+1}">&raquo;</a></li>`);
}
$(document).on('click','#paginacao a[data-p]',function(e){e.preventDefault();carregarTabela(parseInt($(this).data('p')));});

$('#btn-novo').on('click',()=>{
    $('#modal-usuario-titulo').text('Novo Usuario');$('#usuario-id').val('');$('#form-usuario')[0].reset();
    $('#input-senha-usuario').attr('required',true);$('#senha-obrig').show();
    $('#modal-usuario').modal('show');
});

$(document).on('click','.btn-editar-u',function(){
    $.get(URLS_U.show+$(this).data('id'),r=>{
        if(!r.sucesso)return;
        const u=r.dado;
        $('#modal-usuario-titulo').text('Editar Usuario');$('#usuario-id').val(u.id);
        const f=$('#form-usuario');
        f.find('[name="name"]').val(u.name);f.find('[name="email"]').val(u.email);
        f.find('[name="tipo"]').val(u.tipo);f.find('[name="status"]').val(u.status);
        $('#input-senha-usuario').attr('required',false).val('');$('#senha-obrig').hide();
        $('#modal-usuario').modal('show');
    });
});

$('#form-usuario').on('submit',function(e){
    e.preventDefault();
    const id=$('#usuario-id').val();
    const dados={}; $(this).serializeArray().forEach(f=>dados[f.name]=f.value);
    if(!dados.password) delete dados.password;
    $.ajax({url:id?URLS_U.update+id:URLS_U.store,type:id?'PUT':'POST',data:dados,
        success:r=>{if(r.sucesso){toast(r.mensagem,'sucesso');$('#modal-usuario').modal('hide');carregarTabela(paginaAtual);}else toast(r.mensagem||'Erro.','erro');},
        error:r=>{const erros=r.responseJSON?.errors;if(erros)toast(Object.values(erros).flat().join(' | '),'erro');else toast(r.responseJSON?.mensagem||'Erro ao salvar.','erro');},
    });
});

$(document).on('click','.btn-excluir-u',function(){
    const id=$(this).data('id');
    confirmarExclusao(URLS_U.destroy+id,()=>{
        $.ajax({url:URLS_U.destroy+id,type:'DELETE',
            success:r=>{toast(r.mensagem,'sucesso');carregarTabela(paginaAtual);},
            error:r=>toast(r.responseJSON?.mensagem||'Erro.','erro'),
        });
    });
});

$('#btn-filtrar').on('click',()=>carregarTabela(1));
$('#btn-limpar').on('click',()=>{$('#filtro-search,#filtro-status').val('');carregarTabela(1);});
$('#filtro-search').on('keypress',e=>{if(e.which===13)carregarTabela(1);});

carregarTabela();
</script>
@endpush