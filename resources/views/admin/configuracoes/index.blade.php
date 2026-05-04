@extends('layouts.admin.app')

@section('titulo', 'Configuracoes')
@section('titulo_pagina', 'Configuracoes do Sistema')

@section('breadcrumb')
    <li class="breadcrumb-item active">Configuracoes</li>
@endsection

@section('conteudo')
<div class="row">
    <div class="col-12">
        {{-- Tabs --}}
        <ul class="nav nav-tabs mb-3" id="tabs-config">
            <li class="nav-item">
                <a class="nav-link active" data-bs-toggle="tab" href="#tab-geral">
                    <i class="bi bi-gear me-1"></i>Geral
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link" data-bs-toggle="tab" href="#tab-smtp">
                    <i class="bi bi-envelope me-1"></i>E-mail / SMTP
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link" data-bs-toggle="tab" href="#tab-aparencia">
                    <i class="bi bi-palette me-1"></i>Aparencia
                </a>
            </li>
        </ul>

        <div class="tab-content">
            {{-- Aba Geral --}}
            <div class="tab-pane fade show active" id="tab-geral">
                <div class="card card-outline card-primary">
                    <div class="card-header">
                        <h3 class="card-title mb-0"><i class="bi bi-info-circle me-2"></i>Informacoes do Sistema</h3>
                    </div>
                    <div class="card-body">
                        <form id="form-geral">
                            <div class="row g-3">
                                <div class="col-md-6">
                                    <label class="form-label fw-medium">Nome do Sistema</label>
                                    <input type="text" name="sistema_nome" class="form-control"
                                           value="{{ configuracao('sistema_nome', config('app.name')) }}" placeholder="FinanceiroSaaS">
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label fw-medium">Descricao</label>
                                    <input type="text" name="sistema_descricao" class="form-control"
                                           value="{{ configuracao('sistema_descricao', '') }}" placeholder="Sistema financeiro modular">
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label fw-medium">Proprietario / Empresa</label>
                                    <input type="text" name="sistema_proprietario" class="form-control"
                                           value="{{ configuracao('sistema_proprietario', '') }}" placeholder="Nome da empresa">
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label fw-medium">Moeda padrao</label>
                                    <select name="sistema_moeda" class="form-select">
                                        <option value="BRL" {{ configuracao('sistema_moeda','BRL') === 'BRL' ? 'selected' : '' }}>BRL - Real Brasileiro</option>
                                        <option value="USD" {{ configuracao('sistema_moeda','BRL') === 'USD' ? 'selected' : '' }}>USD - Dolar Americano</option>
                                        <option value="EUR" {{ configuracao('sistema_moeda','BRL') === 'EUR' ? 'selected' : '' }}>EUR - Euro</option>
                                    </select>
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label fw-medium">Logo do Sistema</label>
                                    <input type="file" name="sistema_logo" id="input-logo" class="form-control" accept="image/*">
                                    @if(configuracao('sistema_logo'))
                                        <div class="mt-2">
                                            <img src="{{ asset('storage/'.configuracao('sistema_logo')) }}" alt="Logo" height="40">
                                        </div>
                                    @endif
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label fw-medium">Favicon</label>
                                    <input type="file" name="sistema_favicon" id="input-favicon" class="form-control" accept="image/*,.ico">
                                </div>
                            </div>
                            <div class="mt-4">
                                <button type="submit" class="btn btn-primary">
                                    <i class="bi bi-check-lg me-1"></i>Salvar Configuracoes Gerais
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>

            {{-- Aba SMTP --}}
            <div class="tab-pane fade" id="tab-smtp">
                <div class="card card-outline card-primary">
                    <div class="card-header">
                        <h3 class="card-title mb-0"><i class="bi bi-envelope-at me-2"></i>Configuracoes de E-mail</h3>
                    </div>
                    <div class="card-body">
                        <form id="form-smtp">
                            <div class="row g-3">
                                <div class="col-md-6">
                                    <label class="form-label fw-medium">Driver de E-mail</label>
                                    <select name="mail_driver" class="form-select">
                                        <option value="smtp" {{ configuracao('mail_driver','smtp') === 'smtp' ? 'selected' : '' }}>SMTP</option>
                                        <option value="sendmail" {{ configuracao('mail_driver','smtp') === 'sendmail' ? 'selected' : '' }}>Sendmail</option>
                                        <option value="log" {{ configuracao('mail_driver','smtp') === 'log' ? 'selected' : '' }}>Log (teste)</option>
                                    </select>
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label fw-medium">Host SMTP</label>
                                    <input type="text" name="mail_host" class="form-control"
                                           value="{{ configuracao('mail_host', '') }}" placeholder="smtp.gmail.com">
                                </div>
                                <div class="col-md-3">
                                    <label class="form-label fw-medium">Porta</label>
                                    <input type="number" name="mail_port" class="form-control"
                                           value="{{ configuracao('mail_port', '587') }}" placeholder="587">
                                </div>
                                <div class="col-md-3">
                                    <label class="form-label fw-medium">Criptografia</label>
                                    <select name="mail_encryption" class="form-select">
                                        <option value="tls" {{ configuracao('mail_encryption','tls') === 'tls' ? 'selected' : '' }}>TLS</option>
                                        <option value="ssl" {{ configuracao('mail_encryption','tls') === 'ssl' ? 'selected' : '' }}>SSL</option>
                                        <option value="" {{ configuracao('mail_encryption','tls') === '' ? 'selected' : '' }}>Nenhuma</option>
                                    </select>
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label fw-medium">Usuario SMTP</label>
                                    <input type="text" name="mail_username" class="form-control"
                                           value="{{ configuracao('mail_username', '') }}" placeholder="seu@email.com">
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label fw-medium">Senha SMTP</label>
                                    <div class="input-group">
                                        <input type="password" name="mail_password" id="mail_password" class="form-control"
                                               value="{{ configuracao('mail_password', '') }}" placeholder="Senha do e-mail">
                                        <button class="btn btn-outline-secondary" type="button"
                                                onclick="const el=document.getElementById('mail_password');el.type=el.type==='password'?'text':'password'">
                                            <i class="bi bi-eye"></i>
                                        </button>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label fw-medium">Nome do Remetente</label>
                                    <input type="text" name="mail_from_name" class="form-control"
                                           value="{{ configuracao('mail_from_name', config('app.name')) }}" placeholder="FinanceiroSaaS">
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label fw-medium">E-mail do Remetente</label>
                                    <input type="email" name="mail_from_address" class="form-control"
                                           value="{{ configuracao('mail_from_address', '') }}" placeholder="noreply@seudominio.com">
                                </div>
                            </div>
                            <div class="mt-4 d-flex gap-2">
                                <button type="submit" class="btn btn-primary">
                                    <i class="bi bi-check-lg me-1"></i>Salvar SMTP
                                </button>
                                <button type="button" class="btn btn-outline-info" id="btn-testar-smtp">
                                    <i class="bi bi-send me-1"></i>Testar Envio
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>

            {{-- Aba Aparencia --}}
            <div class="tab-pane fade" id="tab-aparencia">
                <div class="card card-outline card-primary">
                    <div class="card-header">
                        <h3 class="card-title mb-0"><i class="bi bi-palette me-2"></i>Aparencia e Tema</h3>
                    </div>
                    <div class="card-body">
                        <form id="form-aparencia">
                            <div class="row g-3">
                                <div class="col-md-4">
                                    <label class="form-label fw-medium">Cor Primaria</label>
                                    <div class="input-group">
                                        <input type="color" name="cor_primaria" class="form-control form-control-color"
                                               value="{{ configuracao('cor_primaria', '#3b82f6') }}">
                                        <input type="text" class="form-control" id="cor_primaria_hex"
                                               value="{{ configuracao('cor_primaria', '#3b82f6') }}" placeholder="#3b82f6">
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <label class="form-label fw-medium">Cor Secundaria</label>
                                    <div class="input-group">
                                        <input type="color" name="cor_secundaria" class="form-control form-control-color"
                                               value="{{ configuracao('cor_secundaria', '#6c757d') }}">
                                        <input type="text" class="form-control" id="cor_secundaria_hex"
                                               value="{{ configuracao('cor_secundaria', '#6c757d') }}" placeholder="#6c757d">
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <label class="form-label fw-medium">Tema Padrao</label>
                                    <select name="tema_padrao" class="form-select">
                                        <option value="light" {{ configuracao('tema_padrao','light') === 'light' ? 'selected' : '' }}>Claro</option>
                                        <option value="dark" {{ configuracao('tema_padrao','light') === 'dark' ? 'selected' : '' }}>Escuro</option>
                                    </select>
                                </div>
                            </div>
                            <div class="mt-4">
                                <button type="submit" class="btn btn-primary">
                                    <i class="bi bi-check-lg me-1"></i>Salvar Aparencia
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
// Sincronizar color picker com input texto
document.querySelectorAll('input[type="color"]').forEach(function(picker) {
    const hexInput = document.getElementById(picker.name + '_hex');
    if (!hexInput) return;
    picker.addEventListener('input', () => hexInput.value = picker.value);
    hexInput.addEventListener('input', () => { if (/^#[0-9A-Fa-f]{6}$/.test(hexInput.value)) picker.value = hexInput.value; });
});

function salvarConfiguracoes(formId, campos) {
    const form = document.getElementById(formId);
    const fd = new FormData(form);
    const dados = { _token: $('meta[name="csrf-token"]').attr('content') };
    campos.forEach(c => { if (fd.get(c) !== null) dados[c] = fd.get(c); });

    $.ajax({
        url: '{{ route("admin.configuracoes.index") }}',
        type: 'POST',
        data: dados,
        success: r => {
            if (r.sucesso) toast(r.mensagem || 'Configuracoes salvas!', 'sucesso');
            else toast(r.mensagem || 'Erro ao salvar.', 'erro');
        },
        error: r => toast(r.responseJSON?.mensagem || 'Erro ao salvar configuracoes.', 'erro'),
    });
}

$('#form-geral').on('submit', function(e) {
    e.preventDefault();
    const fd = new FormData(this);
    fd.append('_token', $('meta[name="csrf-token"]').attr('content'));
    $.ajax({
        url: '{{ route("admin.configuracoes.index") }}',
        type: 'POST',
        data: fd,
        processData: false,
        contentType: false,
        success: r => {
            if (r.sucesso) toast(r.mensagem || 'Configuracoes salvas!', 'sucesso');
            else toast(r.mensagem || 'Erro ao salvar.', 'erro');
        },
        error: r => toast(r.responseJSON?.mensagem || 'Erro ao salvar.', 'erro'),
    });
});

$('#form-smtp').on('submit', function(e) {
    e.preventDefault();
    salvarConfiguracoes('form-smtp', ['mail_driver','mail_host','mail_port','mail_encryption','mail_username','mail_password','mail_from_name','mail_from_address']);
});

$('#form-aparencia').on('submit', function(e) {
    e.preventDefault();
    salvarConfiguracoes('form-aparencia', ['cor_primaria','cor_secundaria','tema_padrao']);
});

$('#btn-testar-smtp').on('click', function() {
    const email = prompt('Digite o e-mail para teste:');
    if (!email) return;
    $.post('{{ route("admin.configuracoes.index") }}', {
        _token: $('meta[name="csrf-token"]').attr('content'),
        acao: 'testar_smtp',
        email_teste: email,
    }, r => {
        if (r.sucesso) toast('E-mail de teste enviado para ' + email, 'sucesso');
        else toast(r.mensagem || 'Falha no envio.', 'erro');
    }).fail(r => toast(r.responseJSON?.mensagem || 'Erro ao testar SMTP.', 'erro'));
});
</script>
@endpush
