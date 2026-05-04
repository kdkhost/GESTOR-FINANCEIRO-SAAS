@extends('layouts.admin.app')

@section('titulo', 'Meu Perfil')
@section('titulo_pagina', 'Meu Perfil')

@section('breadcrumb')
    <li class="breadcrumb-item active">Perfil</li>
@endsection

@section('conteudo')
<div class="row">
    {{-- Card Avatar --}}
    <div class="col-lg-4 col-md-5 mb-4">
        <div class="card card-outline card-primary text-center">
            <div class="card-body pt-4">
                <div class="position-relative d-inline-block mb-3">
                    <img id="avatar-preview" src="{{ $user->avatar_url }}" alt="Avatar"
                         class="rounded-circle shadow" width="120" height="120" style="object-fit:cover;cursor:pointer;"
                         onclick="document.getElementById('input-avatar').click()">
                    <span class="position-absolute bottom-0 end-0 bg-primary rounded-circle p-1" style="cursor:pointer;"
                          onclick="document.getElementById('input-avatar').click()">
                        <i class="bi bi-camera-fill text-white small"></i>
                    </span>
                </div>
                <input type="file" id="input-avatar" accept="image/*" class="d-none">
                <h5 class="fw-bold mb-0">{{ $user->name }}</h5>
                <p class="text-muted small mb-2">{{ $user->email }}</p>
                <span class="badge bg-{{ $user->is_admin ? 'danger' : 'primary' }}">
                    {{ $user->is_admin ? 'Administrador' : 'Usuário' }}
                </span>
                <hr>
                <div class="text-start small text-muted">
                    <div class="mb-1"><i class="bi bi-clock me-2"></i>Último acesso:
                        {{ $user->ultimo_acesso_em ? data_hora_br($user->ultimo_acesso_em) : 'Nunca' }}
                    </div>
                    <div><i class="bi bi-geo-alt me-2"></i>IP: {{ $user->ultimo_ip ?? 'N/A' }}</div>
                </div>
            </div>
        </div>
    </div>

    {{-- Card Dados --}}
    <div class="col-lg-8 col-md-7">
        <div class="card card-outline card-primary">
            <div class="card-header">
                <h3 class="card-title mb-0"><i class="bi bi-person-gear me-2"></i>Dados Pessoais</h3>
            </div>
            <div class="card-body">
                <form id="form-perfil">
                    @csrf
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label fw-medium">Nome completo <span class="text-danger">*</span></label>
                            <input type="text" name="name" class="form-control" value="{{ $user->name }}" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-medium">E-mail <span class="text-danger">*</span></label>
                            <input type="email" name="email" class="form-control" value="{{ $user->email }}" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-medium">CPF</label>
                            <input type="text" name="cpf" class="form-control mask-cpf" value="{{ $user->cpf }}">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-medium">Telefone</label>
                            <input type="text" name="telefone" class="form-control mask-telefone" value="{{ $user->telefone }}">
                        </div>
                    </div>

                    <hr class="my-4">
                    <h6 class="fw-semibold mb-3"><i class="bi bi-lock me-2"></i>Alterar Senha</h6>
                    <div class="row g-3">
                        <div class="col-md-4">
                            <label class="form-label fw-medium">Senha atual</label>
                            <div class="input-group">
                                <input type="password" name="current_password" id="current_password" class="form-control" placeholder="Senha atual">
                                <button class="btn btn-outline-secondary" type="button" onclick="toggleSenha('current_password')">
                                    <i class="bi bi-eye"></i>
                                </button>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label fw-medium">Nova senha</label>
                            <div class="input-group">
                                <input type="password" name="password" id="nova_senha" class="form-control" placeholder="Nova senha">
                                <button class="btn btn-outline-secondary" type="button" onclick="toggleSenha('nova_senha')">
                                    <i class="bi bi-eye"></i>
                                </button>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label fw-medium">Confirmar senha</label>
                            <div class="input-group">
                                <input type="password" name="password_confirmation" id="conf_senha" class="form-control" placeholder="Confirmar senha">
                                <button class="btn btn-outline-secondary" type="button" onclick="toggleSenha('conf_senha')">
                                    <i class="bi bi-eye"></i>
                                </button>
                            </div>
                        </div>
                    </div>

                    <div class="mt-4 d-flex gap-2">
                        <button type="submit" class="btn btn-primary">
                            <i class="bi bi-check-lg me-1"></i>Salvar Alterações
                        </button>
                        <button type="reset" class="btn btn-outline-secondary">
                            <i class="bi bi-x-lg me-1"></i>Cancelar
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
function toggleSenha(id) {
    const el = document.getElementById(id);
    el.type = el.type === 'password' ? 'text' : 'password';
}

// Upload de avatar
document.getElementById('input-avatar').addEventListener('change', function() {
    const file = this.files[0];
    if (!file) return;

    // Preview imediato
    const reader = new FileReader();
    reader.onload = e => document.getElementById('avatar-preview').src = e.target.result;
    reader.readAsDataURL(file);

    // Upload AJAX
    const fd = new FormData();
    fd.append('avatar', file);
    fd.append('_token', $('meta[name="csrf-token"]').attr('content'));

    $.ajax({
        url: '{{ route("admin.perfil.avatar") }}',
        type: 'POST',
        data: fd,
        processData: false,
        contentType: false,
        success: r => {
            if (r.sucesso) {
                toast('Avatar atualizado com sucesso!', 'sucesso');
                document.getElementById('avatar-preview').src = r.url + '?t=' + Date.now();
            } else {
                toast(r.mensagem || 'Erro ao enviar avatar.', 'erro');
            }
        },
        error: r => toast(r.responseJSON?.mensagem || 'Erro ao enviar avatar.', 'erro'),
    });
});

// Salvar perfil
$('#form-perfil').on('submit', function(e) {
    e.preventDefault();
    const dados = {};
    $(this).serializeArray().forEach(f => dados[f.name] = f.value);

    $.ajax({
        url: '{{ route("admin.perfil.update") }}',
        type: 'PUT',
        data: dados,
        success: r => {
            if (r.sucesso) {
                toast(r.mensagem, 'sucesso');
                // Limpa campos de senha
                $('#form-perfil [name="current_password"], #form-perfil [name="password"], #form-perfil [name="password_confirmation"]').val('');
            } else {
                toast(r.mensagem || 'Erro ao salvar.', 'erro');
            }
        },
        error: r => {
            const erros = r.responseJSON?.errors;
            if (erros) {
                const msgs = Object.values(erros).flat().join('<br>');
                toast(msgs, 'erro');
            } else {
                toast(r.responseJSON?.mensagem || 'Erro ao salvar perfil.', 'erro');
            }
        },
    });
});
</script>
@endpush
