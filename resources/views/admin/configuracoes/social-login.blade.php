@extends('layouts.admin.app')
@section('titulo', 'Configuracoes de Login Social')
@section('breadcrumb')
<li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Admin</a></li>
<li class="breadcrumb-item"><a href="{{ route('admin.configuracoes.index') }}">Configuracoes</a></li>
<li class="breadcrumb-item active">Login Social</li>
@endsection
@section('conteudo')
<div class="container-fluid py-4">
    <div class="row g-4">
        {{-- Google --}}
        <div class="col-md-6">
            <div class="card shadow-sm border-0 h-100">
                <div class="card-header bg-white py-3 d-flex align-items-center justify-content-between">
                    <div class="d-flex align-items-center gap-2">
                        <i class="bi bi-google fs-4 text-danger"></i>
                        <h5 class="mb-0">Google Login</h5>
                    </div>
                    <div class="form-check form-switch">
                        <input class="form-check-input" type="checkbox" id="google-ativo" onchange="toggleProvider('google')">
                        <label class="form-check-label" for="google-ativo">Ativado</label>
                    </div>
                </div>
                <div class="card-body">
                    <div class="alert alert-info small">
                        <i class="bi bi-info-circle me-2"></i>
                        Configure no <a href="https://console.cloud.google.com/apis/credentials" target="_blank" class="alert-link">Google Cloud Console</a>
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-medium">Client ID</label>
                        <input type="text" class="form-control" id="google-client-id" placeholder="Ex: 123456789-abc.apps.googleusercontent.com">
                        <small class="text-muted">ID do cliente OAuth 2.0</small>
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-medium">Client Secret</label>
                        <div class="input-group">
                            <input type="password" class="form-control" id="google-client-secret" placeholder="Chave secreta do cliente">
                            <button class="btn btn-outline-secondary" type="button" onclick="togglePassword('google-client-secret')">
                                <i class="bi bi-eye"></i>
                            </button>
                        </div>
                        <small class="text-muted">Chave secreta (sera criptografada)</small>
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-medium">Redirect URL</label>
                        <input type="text" class="form-control" id="google-redirect-url" readonly>
                        <small class="text-muted">Copie esta URL no Google Console</small>
                    </div>

                    <div class="d-flex gap-2">
                        <button type="button" class="btn btn-primary" onclick="salvarConfig('google')">
                            <i class="bi bi-save me-1"></i> Salvar Configuracoes
                        </button>
                        <button type="button" class="btn btn-outline-secondary" onclick="testarConfig('google')">
                            <i class="bi bi-check-circle me-1"></i> Testar
                        </button>
                    </div>
                </div>
            </div>
        </div>

        {{-- Facebook --}}
        <div class="col-md-6">
            <div class="card shadow-sm border-0 h-100">
                <div class="card-header bg-white py-3 d-flex align-items-center justify-content-between">
                    <div class="d-flex align-items-center gap-2">
                        <i class="bi bi-facebook fs-4 text-primary"></i>
                        <h5 class="mb-0">Facebook Login</h5>
                    </div>
                    <div class="form-check form-switch">
                        <input class="form-check-input" type="checkbox" id="facebook-ativo" onchange="toggleProvider('facebook')">
                        <label class="form-check-label" for="facebook-ativo">Ativado</label>
                    </div>
                </div>
                <div class="card-body">
                    <div class="alert alert-info small">
                        <i class="bi bi-info-circle me-2"></i>
                        Configure no <a href="https://developers.facebook.com/apps" target="_blank" class="alert-link">Facebook Developers</a>
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-medium">App ID</label>
                        <input type="text" class="form-control" id="facebook-client-id" placeholder="Ex: 123456789">
                        <small class="text-muted">ID do aplicativo Facebook</small>
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-medium">App Secret</label>
                        <div class="input-group">
                            <input type="password" class="form-control" id="facebook-client-secret" placeholder="Chave secreta do aplicativo">
                            <button class="btn btn-outline-secondary" type="button" onclick="togglePassword('facebook-client-secret')">
                                <i class="bi bi-eye"></i>
                            </button>
                        </div>
                        <small class="text-muted">Chave secreta (sera criptografada)</small>
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-medium">Redirect URL</label>
                        <input type="text" class="form-control" id="facebook-redirect-url" readonly>
                        <small class="text-muted">Copie esta URL no Facebook Developers</small>
                    </div>

                    <div class="d-flex gap-2">
                        <button type="button" class="btn btn-primary" onclick="salvarConfig('facebook')">
                            <i class="bi bi-save me-1"></i> Salvar Configuracoes
                        </button>
                        <button type="button" class="btn btn-outline-secondary" onclick="testarConfig('facebook')">
                            <i class="bi bi-check-circle me-1"></i> Testar
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Instrucoes --}}
    <div class="row mt-4">
        <div class="col-12">
            <div class="card shadow-sm border-0">
                <div class="card-header bg-white py-3">
                    <h5 class="mb-0"><i class="bi bi-info-circle me-2"></i>Como Configurar</h5>
                </div>
                <div class="card-body">
                    <div class="accordion" id="accordionInstrucoes">
                        <div class="accordion-item">
                            <h2 class="accordion-header">
                                <button class="accordion-button" type="button" data-bs-toggle="collapse" data-bs-target="#collapseGoogle">
                                    Configuracao Google
                                </button>
                            </h2>
                            <div id="collapseGoogle" class="accordion-collapse collapse show" data-bs-parent="#accordionInstrucoes">
                                <div class="accordion-body">
                                    <ol class="small">
                                        <li>Acesse o <a href="https://console.cloud.google.com/apis/credentials" target="_blank">Google Cloud Console</a></li>
                                        <li>Crie um projeto ou selecione um existente</li>
                                        <li>Vá em "APIs e Servicos" > "Credenciais"</li>
                                        <li>Clique em "Criar Credenciais" > "ID do cliente OAuth"</li>
                                        <li>Configure a tela de consentimento (se necessario)</li>
                                        <li>Tipo de aplicativo: "Aplicativo da Web"</li>
                                        <li>Adicione a Redirect URL copiada do campo acima</li>
                                        <li>Copie o Client ID e Client Secret para os campos acima</li>
                                        <li>Ative o login social e salve</li>
                                    </ol>
                                </div>
                            </div>
                        </div>
                        <div class="accordion-item">
                            <h2 class="accordion-header">
                                <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#collapseFacebook">
                                    Configuracao Facebook
                                </button>
                            </h2>
                            <div id="collapseFacebook" class="accordion-collapse collapse" data-bs-parent="#accordionInstrucoes">
                                <div class="accordion-body">
                                    <ol class="small">
                                        <li>Acesse o <a href="https://developers.facebook.com/apps" target="_blank">Facebook Developers</a></li>
                                        <li>Crie um novo aplicativo</li>
                                        <li>Tipo: "Consumer" ou "None"</li>
                                        <li>Adicione o produto "Facebook Login"</li>
                                        <li>Em "Configuracoes" > "Basico", copie o App ID e App Secret</li>
                                        <li>Em "Facebook Login" > "Configuracoes", adicione a Redirect URL</li>
                                        <li>Copie as credenciais para os campos acima</li>
                                        <li>Ative o login social e salve</li>
                                    </ol>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
@push('scripts')
<script>
const API_URL = '{{ url('api/admin/configuracoes/social-login') }}';
const BASE_URL = '{{ url('/') }}';

$(document).ready(function() {
    carregarConfigs();

    // Define as URLs de redirect
    $('#google-redirect-url').val(BASE_URL + '/auth/google/callback');
    $('#facebook-redirect-url').val(BASE_URL + '/auth/facebook/callback');
});

function carregarConfigs() {
    $.get(API_URL)
        .done(function(res) {
            if (res.sucesso) {
                const configs = res.dados;

                // Google
                if (configs.google) {
                    $('#google-ativo').prop('checked', configs.google.ativado);
                    $('#google-client-id').val(configs.google.client_id || '');
                    $('#google-client-secret').val(configs.google.client_secret || '');
                    if (configs.google.redirect_url) {
                        $('#google-redirect-url').val(configs.google.redirect_url);
                    }
                }

                // Facebook
                if (configs.facebook) {
                    $('#facebook-ativo').prop('checked', configs.facebook.ativado);
                    $('#facebook-client-id').val(configs.facebook.client_id || '');
                    $('#facebook-client-secret').val(configs.facebook.client_secret || '');
                    if (configs.facebook.redirect_url) {
                        $('#facebook-redirect-url').val(configs.facebook.redirect_url);
                    }
                }
            }
        })
        .fail(function() {
            toast('Erro ao carregar configuracoes', 'erro');
        });
}

function salvarConfig(provider) {
    const dados = {
        provider: provider,
        ativado: $('#' + provider + '-ativo').is(':checked'),
        client_id: $('#' + provider + '-client-id').val(),
        client_secret: $('#' + provider + '-client-secret').val(),
        redirect_url: $('#' + provider + '-redirect-url').val()
    };

    // Validações
    if (dados.ativado && (!dados.client_id || !dados.client_secret)) {
        toast('Client ID e Client Secret sao obrigatorios para ativar', 'erro');
        return;
    }

    $.ajax({
        url: API_URL,
        method: 'POST',
        data: dados,
        headers: { 'X-CSRF-TOKEN': '{{ csrf_token() }}' }
    })
    .done(function(res) {
        if (res.sucesso) {
            toast(res.mensagem, 'sucesso');
        } else {
            toast(res.mensagem || 'Erro ao salvar', 'erro');
        }
    })
    .fail(function(xhr) {
        const msg = xhr.responseJSON?.mensagem || 'Erro ao salvar configuracao';
        toast(msg, 'erro');
    });
}

function testarConfig(provider) {
    $.ajax({
        url: API_URL + '/testar',
        method: 'POST',
        data: { provider: provider },
        headers: { 'X-CSRF-TOKEN': '{{ csrf_token() }}' }
    })
    .done(function(res) {
        if (res.sucesso) {
            toast(res.mensagem, 'sucesso');
        } else {
            toast(res.mensagem || 'Erro no teste', 'erro');
        }
    })
    .fail(function(xhr) {
        const msg = xhr.responseJSON?.mensagem || 'Erro ao testar configuracao';
        toast(msg, 'erro');
    });
}

function toggleProvider(provider) {
    const ativo = $('#' + provider + '-ativo').is(':checked');
    const card = $('#' + provider + '-client-id').closest('.card');

    if (ativo) {
        card.addClass('border-success');
    } else {
        card.removeClass('border-success');
    }
}

function togglePassword(inputId) {
    const input = document.getElementById(inputId);
    const button = input.nextElementSibling;
    const icon = button.querySelector('i');

    if (input.type === 'password') {
        input.type = 'text';
        icon.classList.remove('bi-eye');
        icon.classList.add('bi-eye-slash');
    } else {
        input.type = 'password';
        icon.classList.remove('bi-eye-slash');
        icon.classList.add('bi-eye');
    }
}
</script>
@endpush
