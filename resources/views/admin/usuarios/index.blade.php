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
    <div class="modal-dialog modal-xl">
        <div class="modal-content">
            <div class="modal-header bg-primary text-white">
                <h5 class="modal-title"><i class="bi bi-person-plus me-2"></i><span id="modal-usuario-titulo">Novo Usuario</span></h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <form id="form-usuario" enctype="multipart/form-data">
                <div class="modal-body">
                    <input type="hidden" id="usuario-id">

                    <div class="row g-3">
                        <div class="col-lg-3 text-center">
                            <div class="card bg-light">
                                <div class="card-body">
                                    <img id="preview-avatar" src="/images/avatar-padrao.png" class="rounded-circle mb-3" width="120" height="120" style="object-fit:cover;">
                                    <div class="mb-2">
                                        <span id="badge-tipo" class="badge bg-primary">Usuario</span>
                                    </div>
                                    <div class="mb-2">
                                        <span id="badge-status" class="badge bg-success">Ativo</span>
                                    </div>
                                    <div id="info-acesso" class="small text-muted d-none">
                                        <hr class="my-2">
                                        <div>Ultimo acesso:</div>
                                        <div id="ultimo-acesso" class="fw-medium">--</div>
                                        <div id="ultimo-ip" class="small">IP: --</div>
                                    </div>
                                    <hr class="my-2">
                                    <label class="btn btn-outline-primary btn-sm w-100">
                                        <i class="bi bi-camera me-1"></i>Alterar Foto
                                        <input type="file" name="avatar" id="input-avatar" class="d-none" accept="image/*">
                                    </label>
                                </div>
                            </div>
                        </div>

                        <div class="col-lg-9">
                            <ul class="nav nav-tabs mb-3" id="usuarioTabs" role="tablist">
                                <li class="nav-item" role="presentation">
                                    <button class="nav-link active" id="tab-dados" data-bs-toggle="tab" data-bs-target="#painel-dados" type="button" role="tab">Dados Basicos</button>
                                </li>
                                <li class="nav-item" role="presentation">
                                    <button class="nav-link" id="tab-contato" data-bs-toggle="tab" data-bs-target="#painel-contato" type="button" role="tab">Contato</button>
                                </li>
                                <li class="nav-item" role="presentation">
                                    <button class="nav-link" id="tab-endereco" data-bs-toggle="tab" data-bs-target="#painel-endereco" type="button" role="tab">Endereco</button>
                                </li>
                                <li class="nav-item" role="presentation">
                                    <button class="nav-link" id="tab-seguranca" data-bs-toggle="tab" data-bs-target="#painel-seguranca" type="button" role="tab">Seguranca</button>
                                </li>
                            </ul>

                            <div class="tab-content" id="usuarioTabsContent">
                                <div class="tab-pane fade show active" id="painel-dados" role="tabpanel">
                                    <div class="row g-3">
                                        <div class="col-md-6">
                                            <label class="form-label fw-medium">Nome Completo <span class="text-danger">*</span></label>
                                            <input type="text" name="name" class="form-control" required>
                                        </div>
                                        <div class="col-md-6">
                                            <label class="form-label fw-medium">E-mail <span class="text-danger">*</span></label>
                                            <input type="email" name="email" class="form-control" required>
                                        </div>
                                        <div class="col-md-6">
                                            <label class="form-label fw-medium">Tipo de Usuario</label>
                                            <select name="tipo" id="select-tipo" class="form-select">
                                                <option value="usuario">Usuario</option>
                                                <option value="admin">Administrador</option>
                                                <option value="superadmin">Super Admin</option>
                                            </select>
                                            <div class="form-text text-warning d-none" id="aviso-superadmin">
                                                <i class="bi bi-exclamation-triangle me-1"></i>Apenas superadmins podem alterar este tipo.
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <label class="form-label fw-medium">Status</label>
                                            <select name="status" class="form-select">
                                                <option value="ativo">Ativo</option>
                                                <option value="inativo">Inativo</option>
                                                <option value="bloqueado">Bloqueado</option>
                                            </select>
                                        </div>
                                        <div class="col-md-6">
                                            <label class="form-label fw-medium">CPF</label>
                                            <input type="text" name="cpf" class="form-control" placeholder="000.000.000-00">
                                        </div>
                                        <div class="col-md-6">
                                            <label class="form-label fw-medium">Telefone</label>
                                            <input type="text" name="telefone" class="form-control" placeholder="(00) 00000-0000">
                                        </div>
                                    </div>
                                </div>

                                <div class="tab-pane fade" id="painel-contato" role="tabpanel">
                                    <div class="row g-3">
                                        <div class="col-md-6">
                                            <label class="form-label fw-medium">Telefone Principal</label>
                                            <input type="text" name="telefone" class="form-control" placeholder="(00) 00000-0000">
                                        </div>
                                        <div class="col-md-6">
                                            <label class="form-label fw-medium">E-mail (confirmacao)</label>
                                            <input type="email" class="form-control" disabled placeholder="Mesmo e-mail da aba Dados Basicos">
                                        </div>
                                    </div>
                                </div>

                                <div class="tab-pane fade" id="painel-endereco" role="tabpanel">
                                    <div class="row g-3">
                                        <div class="col-md-4">
                                            <label class="form-label fw-medium">CEP</label>
                                            <input type="text" name="cep" class="form-control mask-cep viacep" placeholder="00000-000">
                                        </div>
                                        <div class="col-md-8">
                                            <label class="form-label fw-medium">Logradouro</label>
                                            <input type="text" name="logradouro" class="form-control" placeholder="Rua, Avenida, etc">
                                        </div>
                                        <div class="col-md-4">
                                            <label class="form-label fw-medium">Numero</label>
                                            <input type="text" name="numero" class="form-control" placeholder="123">
                                        </div>
                                        <div class="col-md-8">
                                            <label class="form-label fw-medium">Complemento</label>
                                            <input type="text" name="complemento" class="form-control" placeholder="Apto, Sala, etc">
                                        </div>
                                        <div class="col-md-5">
                                            <label class="form-label fw-medium">Bairro</label>
                                            <input type="text" name="bairro" class="form-control">
                                        </div>
                                        <div class="col-md-5">
                                            <label class="form-label fw-medium">Cidade</label>
                                            <input type="text" name="cidade" class="form-control">
                                        </div>
                                        <div class="col-md-2">
                                            <label class="form-label fw-medium">UF</label>
                                            <select name="estado" class="form-select">
                                                <option value="">--</option>
                                                <option value="AC">AC</option><option value="AL">AL</option><option value="AP">AP</option>
                                                <option value="AM">AM</option><option value="BA">BA</option><option value="CE">CE</option>
                                                <option value="DF">DF</option><option value="ES">ES</option><option value="GO">GO</option>
                                                <option value="MA">MA</option><option value="MT">MT</option><option value="MS">MS</option>
                                                <option value="MG">MG</option><option value="PA">PA</option><option value="PB">PB</option>
                                                <option value="PR">PR</option><option value="PE">PE</option><option value="PI">PI</option>
                                                <option value="RJ">RJ</option><option value="RN">RN</option><option value="RS">RS</option>
                                                <option value="RO">RO</option><option value="RR">RR</option><option value="SC">SC</option>
                                                <option value="SP">SP</option><option value="SE">SE</option><option value="TO">TO</option>
                                            </select>
                                        </div>
                                    </div>
                                </div>

                                <div class="tab-pane fade" id="painel-seguranca" role="tabpanel">
                                    <div class="row g-3">
                                        <div class="col-md-6">
                                            <label class="form-label fw-medium">Senha <span id="senha-obrig" class="text-danger">*</span></label>
                                            <div class="input-group">
                                                <input type="password" name="password" id="input-senha-usuario" class="form-control" placeholder="Minimo 8 caracteres">
                                                <button class="btn btn-outline-secondary" type="button" onclick="toggleSenha('input-senha-usuario')">
                                                    <i class="bi bi-eye"></i>
                                                </button>
                                            </div>
                                            <div class="form-text" id="senha-ajuda">Deixe em branco para manter a senha atual ao editar.</div>
                                        </div>
                                        <div class="col-md-6">
                                            <label class="form-label fw-medium">Confirmar Senha</label>
                                            <input type="password" name="password_confirmation" id="input-senha-confirm" class="form-control" placeholder="Repita a senha">
                                        </div>
                                        <div class="col-12">
                                            <div class="form-check form-switch">
                                                <input class="form-check-input" type="checkbox" name="dois_fatores" id="check-2fa" value="1">
                                                <label class="form-check-label" for="check-2fa">Ativar Autenticacao de 2 Fatores</label>
                                            </div>
                                        </div>
                                        <div class="col-12" id="info-seguranca" style="display:none;">
                                            <div class="alert alert-info">
                                                <h6 class="alert-heading"><i class="bi bi-shield-check me-2"></i>Informacoes de Seguranca</h6>
                                                <p class="mb-1">Tentativas de login: <span id="tentativas-login" class="fw-bold">0</span></p>
                                                <p class="mb-0">Bloqueado ate: <span id="bloqueado-ate">--</span></p>
                                            </div>
                                        </div>
                                    </div>
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
const URLS_U = { listar: '/admin/usuarios/listar', store: '/admin/usuarios', show: '/admin/usuarios/', update: '/admin/usuarios/', destroy: '/admin/usuarios/', impersonate: '/admin/usuarios/:id/impersonate' };
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
                <td>${u.ultimo_acesso_em_formatado||'<span class="text-muted">Nunca</span>'}</td>
                <td class="text-center"><span class="badge bg-${statusMap[u.status]||'secondary'}">${u.status}</span></td>
                <td class="text-end"><div class="btn-group btn-group-sm">
                    ${u.tipo !== 'superadmin' ? `<button class="btn btn-outline-success btn-impersonate-u" data-id="${u.id}" title="Acessar conta"><i class="bi bi-box-arrow-in-right"></i></button>` : ''}
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
    $('#modal-usuario-titulo').text('Novo Usuario');
    $('#usuario-id').val('');
    $('#form-usuario')[0].reset();
    $('#preview-avatar').attr('src', '/images/avatar-padrao.png');
    $('#badge-tipo').text('Usuario').attr('class', 'badge bg-primary');
    $('#badge-status').text('Ativo').attr('class', 'badge bg-success');
    $('#info-acesso').addClass('d-none');
    $('#info-seguranca').hide();
    $('#input-senha-usuario').attr('required',true);
    $('#senha-obrig').show();
    $('#senha-ajuda').text('Obrigatorio para novo usuario.');
    $('#aviso-superadmin').addClass('d-none');
    $('#select-tipo').prop('disabled', false);
    $('.nav-link').removeClass('active');
    $('#tab-dados').addClass('active');
    $('.tab-pane').removeClass('show active');
    $('#painel-dados').addClass('show active');
    $('#modal-usuario').modal('show');
});

$(document).on('click','.btn-editar-u',function(){
    const id = $(this).data('id');
    $.get(URLS_U.show+id,function(r){
        if(!r.sucesso)return;
        const u=r.dado;

        $('#modal-usuario-titulo').text('Editar Usuario');
        $('#usuario-id').val(u.id);

        // Avatar
        $('#preview-avatar').attr('src', u.avatar_url || '/images/avatar-padrao.png');

        // Badges
        const tipoMap = {usuario: ['primary', 'Usuario'], admin: ['warning', 'Administrador'], superadmin: ['danger', 'Super Admin']};
        const tipoInfo = tipoMap[u.tipo] || tipoMap.usuario;
        $('#badge-tipo').text(tipoInfo[1]).attr('class', 'badge bg-' + tipoInfo[0]);

        const statusMap = {ativo: ['success', 'Ativo'], inativo: ['secondary', 'Inativo'], bloqueado: ['danger', 'Bloqueado']};
        const statusInfo = statusMap[u.status] || statusMap.ativo;
        $('#badge-status').text(statusInfo[1]).attr('class', 'badge bg-' + statusInfo[0]);

        // Info de acesso
        if(u.ultimo_acesso_em_formatado){
            $('#info-acesso').removeClass('d-none');
            $('#ultimo-acesso').text(u.ultimo_acesso_em_formatado);
        } else {
            $('#info-acesso').addClass('d-none');
        }

        // Dados Basicos
        const f=$('#form-usuario');
        f.find('[name="name"]').val(u.name);
        f.find('[name="email"]').val(u.email);
        f.find('[name="tipo"]').val(u.tipo);
        f.find('[name="status"]').val(u.status);
        f.find('[name="cpf"]').val(u.cpf || '');
        f.find('[name="telefone"]').val(u.telefone || '');

        // Endereco
        f.find('[name="cep"]').val(u.cep || '');
        f.find('[name="logradouro"]').val(u.logradouro || '');
        f.find('[name="numero"]').val(u.numero || '');
        f.find('[name="complemento"]').val(u.complemento || '');
        f.find('[name="bairro"]').val(u.bairro || '');
        f.find('[name="cidade"]').val(u.cidade || '');
        f.find('[name="estado"]').val(u.estado || '');

        // Seguranca
        $('#input-senha-usuario').attr('required',false).val('');
        $('#input-senha-confirm').val('');
        $('#senha-obrig').hide();
        $('#senha-ajuda').text('Deixe em branco para manter a senha atual.');
        $('#check-2fa').prop('checked', u.dois_fatores == 1 || u.dois_fatores === true);

        // Info de seguranca
        if(u.tentativas_login > 0 || u.bloqueado_ate){
            $('#info-seguranca').show();
            $('#tentativas-login').text(u.tentativas_login || 0);
            $('#bloqueado-ate').text(u.bloqueado_ate ? new Date(u.bloqueado_ate).toLocaleString('pt-BR') : '--');
        } else {
            $('#info-seguranca').hide();
        }

        // Verificar se pode alterar tipo (apenas superadmin pode)
        const userAtualTipo = '{{ auth()->user()->tipo }}';
        if(userAtualTipo !== 'superadmin' && u.tipo === 'superadmin'){
            $('#select-tipo').prop('disabled', true);
            $('#aviso-superadmin').removeClass('d-none');
        } else {
            $('#select-tipo').prop('disabled', false);
            $('#aviso-superadmin').addClass('d-none');
        }

        // Reset tabs
        $('.nav-link').removeClass('active');
        $('#tab-dados').addClass('active');
        $('.tab-pane').removeClass('show active');
        $('#painel-dados').addClass('show active');

        $('#modal-usuario').modal('show');
    });
});

// Preview avatar ao selecionar arquivo
$('#input-avatar').on('change', function(){
    const file = this.files[0];
    if(file){
        const reader = new FileReader();
        reader.onload = function(e){
            $('#preview-avatar').attr('src', e.target.result);
        };
        reader.readAsDataURL(file);
    }
});

function toggleSenha(id){
    const input = document.getElementById(id);
    const icon = input.nextElementSibling.querySelector('i');
    if(input.type === 'password'){
        input.type = 'text';
        icon.classList.remove('bi-eye');
        icon.classList.add('bi-eye-slash');
    } else {
        input.type = 'password';
        icon.classList.remove('bi-eye-slash');
        icon.classList.add('bi-eye');
    }
}

$('#form-usuario').on('submit',function(e){
    e.preventDefault();
    const id=$('#usuario-id').val();
    const formData = new FormData(this);

    // Adicionar _method para PUT quando editar
    if(id){
        formData.append('_method', 'PUT');
    }

    // Remover senha vazia
    if(!formData.get('password')){
        formData.delete('password');
    }
    formData.delete('password_confirmation');

    // Dois fatores
    formData.set('dois_fatores', $('#check-2fa').is(':checked') ? '1' : '0');

    $.ajax({
        url: id ? URLS_U.update+id : URLS_U.store,
        type: 'POST',
        data: formData,
        processData: false,
        contentType: false,
        headers: {'X-CSRF-TOKEN': '{{ csrf_token() }}'},
        success: function(r){
            if(r.sucesso){
                toast(r.mensagem, 'sucesso');
                $('#modal-usuario').modal('hide');
                carregarTabela(paginaAtual);
            } else {
                toast(r.mensagem || 'Erro ao salvar.', 'erro');
            }
        },
        error: function(r){
            const erros = r.responseJSON?.errors;
            if(erros){
                toast(Object.values(erros).flat().join(' | '), 'erro');
            } else {
                toast(r.responseJSON?.mensagem || 'Erro ao salvar.', 'erro');
            }
        }
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

// Acesso supervisionado (impersonate)
$(document).on('click','.btn-impersonate-u',function(){
    const id=$(this).data('id');
    const btn = $(this);
    const nome = btn.closest('tr').find('.fw-medium').text();

    SistemaAlert.fire({
        title: 'Acessar Conta?',
        html: `Voce ira acessar a conta de <strong>${nome}</strong> sem necessidade de senha.<br><br>
               <span class="text-warning"><i class="bi bi-exclamation-triangle"></i> Todas as acoes serao registradas em auditoria.</span>`,
        icon: 'warning',
        showCancelButton: true,
        confirmButtonText: 'Acessar Conta',
        cancelButtonText: 'Cancelar',
        confirmButtonColor: '#198754'
    }).then((result) => {
        if (result.isConfirmed) {
            $.ajax({
                url: URLS_U.impersonate.replace(':id', id),
                type: 'POST',
                headers: {'X-CSRF-TOKEN': '{{ csrf_token() }}'}
            })
            .done(function(r){
                if(r.sucesso){
                    toast(r.mensagem, 'sucesso');
                    setTimeout(() => {
                        window.location.href = r.redirect || '/admin/dashboard';
                    }, 1000);
                } else {
                    toast(r.mensagem || 'Erro ao acessar conta.', 'erro');
                }
            })
            .fail(function(xhr){
                const msg = xhr.responseJSON?.mensagem || 'Erro ao acessar conta.';
                toast(msg, 'erro');
            });
        }
    });
});

$('#btn-filtrar').on('click',()=>carregarTabela(1));
$('#btn-limpar').on('click',()=>{$('#filtro-search,#filtro-status').val('');carregarTabela(1);});
$('#filtro-search').on('keypress',e=>{if(e.which===13)carregarTabela(1);});

carregarTabela();
</script>
@endpush