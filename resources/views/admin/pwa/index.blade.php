@extends('layouts.admin.app')

@section('titulo', 'PWA')
@section('titulo_pagina', 'Progressive Web App')

@section('breadcrumb')
    <li class="breadcrumb-item active">PWA</li>
@endsection

@section('conteudo')
<div class="row g-3">
    <div class="col-xl-8">
        <div class="card card-outline card-primary">
            <div class="card-header">
                <h3 class="card-title mb-0"><i class="bi bi-app-indicator me-2"></i>Configuracoes do PWA</h3>
            </div>
            <div class="card-body">
                <form id="form-pwa">
                    <div class="form-check form-switch mb-4">
                        <input class="form-check-input" type="checkbox" role="switch" id="pwa_ativo" name="pwa_ativo"
                               value="1" {{ configuracao('pwa_ativo', '0') === '1' ? 'checked' : '' }}>
                        <label class="form-check-label fw-medium" for="pwa_ativo">Ativar PWA</label>
                        <div class="form-text">Permite que usuarios instalem o sistema como um aplicativo no celular/desktop.</div>
                    </div>

                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label fw-medium">Nome do App</label>
                            <input type="text" name="pwa_nome" class="form-control"
                                   value="{{ configuracao('pwa_nome', configuracao('sistema_nome', 'FinanceiroSaaS')) }}"
                                   placeholder="FinanceiroSaaS">
                            <div class="form-text">Nome exibido na tela inicial.</div>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-medium">Nome Curto</label>
                            <input type="text" name="pwa_nome_curto" class="form-control"
                                   value="{{ configuracao('pwa_nome_curto', 'Financeiro') }}"
                                   placeholder="Financeiro">
                            <div class="form-text">Maximo 12 caracteres.</div>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-medium">Cor do Tema</label>
                            <input type="color" name="pwa_cor_tema" class="form-control form-control-color"
                                   value="{{ configuracao('pwa_cor_tema', '#3b82f6') }}" style="min-height: 38px;">
                            <div class="form-text">Cor da barra de status no mobile.</div>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-medium">Cor de Fundo</label>
                            <input type="color" name="pwa_cor_fundo" class="form-control form-control-color"
                                   value="{{ configuracao('pwa_cor_fundo', '#ffffff') }}" style="min-height: 38px;">
                            <div class="form-text">Cor de fundo da tela de splash.</div>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-medium">Icone (192x192)</label>
                            <input type="file" name="pwa_icone_192" class="form-control" accept="image/png,image/svg+xml">
                            @if(configuracao('pwa_icone_192'))
                                <div class="mt-2">
                                    <img src="{{ asset('storage/' . configuracao('pwa_icone_192')) }}" alt="Icone 192" height="48">
                                </div>
                            @endif
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-medium">Icone (512x512)</label>
                            <input type="file" name="pwa_icone_512" class="form-control" accept="image/png,image/svg+xml">
                            @if(configuracao('pwa_icone_512'))
                                <div class="mt-2">
                                    <img src="{{ asset('storage/' . configuracao('pwa_icone_512')) }}" alt="Icone 512" height="48">
                                </div>
                            @endif
                        </div>
                        <div class="col-12">
                            <label class="form-label fw-medium">Descricao</label>
                            <textarea name="pwa_descricao" class="form-control" rows="2"
                                      placeholder="Descricao do aplicativo">{{ configuracao('pwa_descricao', configuracao('sistema_descricao', 'Sistema de Gestao Financeira')) }}</textarea>
                        </div>
                    </div>

                    <div class="mt-4 d-flex gap-2">
                        <button type="submit" class="btn btn-primary">
                            <i class="bi bi-check-lg me-1"></i>Salvar Configuracoes
                        </button>
                        <button type="button" class="btn btn-outline-success" onclick="instalarPWA()">
                            <i class="bi bi-download me-1"></i>Testar Instalacao
                        </button>
                        <button type="button" class="btn btn-outline-info" onclick="gerarManifest()">
                            <i class="bi bi-file-code me-1"></i>Ver Manifest
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <div class="col-xl-4">
        <div class="card card-outline card-info">
            <div class="card-header">
                <h3 class="card-title mb-0"><i class="bi bi-info-circle me-2"></i>Preview Mobile</h3>
            </div>
            <div class="card-body text-center">
                <div class="bg-light border rounded p-3 d-inline-block" style="max-width: 280px;">
                    <div class="bg-dark rounded-top" style="height: 20px;"></div>
                    <div class="bg-white p-3" style="min-height: 300px;">
                        <div class="text-center mb-3">
                            <div class="d-inline-block p-3 rounded mb-2" style="background: {{ configuracao('pwa_cor_tema', '#3b82f6') }};">
                                <i class="bi bi-wallet2 text-white" style="font-size: 2rem;"></i>
                            </div>
                            <h5 class="mb-0">{{ configuracao('pwa_nome_curto', 'Financeiro') }}</h5>
                            <small class="text-muted">{{ configuracao('pwa_nome', 'FinanceiroSaaS') }}</small>
                        </div>
                        <div class="text-start">
                            <div class="p-2 bg-light rounded mb-2">
                                <small class="text-muted">Dashboard</small>
                            </div>
                            <div class="p-2 bg-light rounded mb-2">
                                <small class="text-muted">Contas a Pagar</small>
                            </div>
                            <div class="p-2 bg-light rounded mb-2">
                                <small class="text-muted">Contas a Receber</small>
                            </div>
                            <div class="p-2 bg-light rounded">
                                <small class="text-muted">Relatorios</small>
                            </div>
                        </div>
                    </div>
                    <div class="bg-dark rounded-bottom" style="height: 20px;"></div>
                </div>
                <p class="text-muted small mt-3 mb-0">
                    <i class="bi bi-info-circle me-1"></i>
                    O PWA permite acesso offline e instalacao na tela inicial.
                </p>
            </div>
        </div>

        <div class="card card-outline card-secondary mt-3">
            <div class="card-header">
                <h3 class="card-title mb-0"><i class="bi bi-question-circle me-2"></i>Como Instalar</h3>
            </div>
            <div class="card-body">
                <div class="accordion accordion-flush" id="accordionInstalacao">
                    <div class="accordion-item">
                        <h2 class="accordion-header">
                            <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#android">
                                <i class="bi bi-android me-2"></i>Android
                            </button>
                        </h2>
                        <div id="android" class="accordion-collapse collapse" data-bs-parent="#accordionInstalacao">
                            <div class="accordion-body small">
                                1. Abra no Chrome<br>
                                2. Toque no menu (3 pontos)<br>
                                3. Selecione "Adicionar a tela inicial"
                            </div>
                        </div>
                    </div>
                    <div class="accordion-item">
                        <h2 class="accordion-header">
                            <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#ios">
                                <i class="bi bi-apple me-2"></i>iPhone/iPad
                            </button>
                        </h2>
                        <div id="ios" class="accordion-collapse collapse" data-bs-parent="#accordionInstalacao">
                            <div class="accordion-body small">
                                1. Abra no Safari<br>
                                2. Toque no icone de compartilhar<br>
                                3. Selecione "Adicionar a Home"
                            </div>
                        </div>
                    </div>
                    <div class="accordion-item">
                        <h2 class="accordion-header">
                            <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#desktop">
                                <i class="bi bi-laptop me-2"></i>Desktop
                            </button>
                        </h2>
                        <div id="desktop" class="accordion-collapse collapse" data-bs-parent="#accordionInstalacao">
                            <div class="accordion-body small">
                                1. Abra no Chrome/Edge<br>
                                2. Clique no icone de install na barra de endereco<br>
                                3. Ou use menu > Apps > Instalar
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="modal-manifest" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title"><i class="bi bi-file-code me-2"></i>manifest.json</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <pre class="bg-dark text-light p-3 rounded"><code id="manifest-conteudo">{
  "name": "{{ configuracao('pwa_nome', 'FinanceiroSaaS') }}",
  "short_name": "{{ configuracao('pwa_nome_curto', 'Financeiro') }}",
  "description": "{{ configuracao('pwa_descricao', 'Sistema de Gestao Financeira') }}",
  "start_url": "/",
  "display": "standalone",
  "background_color": "{{ configuracao('pwa_cor_fundo', '#ffffff') }}",
  "theme_color": "{{ configuracao('pwa_cor_tema', '#3b82f6') }}",
  "orientation": "portrait",
  "scope": "/",
  "icons": [
    {
      "src": "/icon-192x192.png",
      "sizes": "192x192",
      "type": "image/png"
    },
    {
      "src": "/icon-512x512.png",
      "sizes": "512x512",
      "type": "image/png"
    }
  ]
}</code></pre>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Fechar</button>
                <button type="button" class="btn btn-primary" onclick="copiarManifest()">
                    <i class="bi bi-clipboard me-1"></i>Copiar
                </button>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
$('#form-pwa').on('submit', function(e) {
    e.preventDefault();
    const fd = new FormData(this);
    fd.set('pwa_ativo', $('#pwa_ativo').is(':checked') ? '1' : '0');
    fd.append('_token', $('meta[name="csrf-token"]').attr('content'));

    $.ajax({
        url: '{{ route("admin.configuracoes.salvar") }}',
        type: 'POST',
        data: fd,
        processData: false,
        contentType: false,
        success: r => {
            toast('Configuracoes PWA salvas com sucesso!', 'sucesso');
        },
        error: xhr => {
            toast(xhr.responseJSON?.mensagem || 'Erro ao salvar configuracoes.', 'erro');
        }
    });
});

function instalarPWA() {
    if ('beforeinstallprompt' in window) {
        toast('Clique no icone de instalacao na barra de endereco', 'info');
    } else {
        SistemaAlert.fire({
            title: 'Instalacao Manual',
            text: 'Use o menu do navegador para adicionar a tela inicial.',
            icon: 'info',
            confirmButtonText: 'Entendi'
        });
    }
}

function gerarManifest() {
    $('#modal-manifest').modal('show');
}

function copiarManifest() {
    const conteudo = $('#manifest-conteudo').text();
    navigator.clipboard.writeText(conteudo).then(() => {
        toast('Manifest copiado para a area de transferencia!', 'sucesso');
    });
}

// Atualizar preview em tempo real
$('input[name="pwa_cor_tema"]').on('input', function() {
    $('.preview-icon').css('background', $(this).val());
});

$('input[name="pwa_nome"], input[name="pwa_nome_curto"]').on('input', function() {
    // Recarregar preview
});

// Registrar Service Worker se PWA ativo
@if(configuracao('pwa_ativo', '0') === '1')
if ('serviceWorker' in navigator) {
    navigator.serviceWorker.register('/sw.js').then(reg => {
        console.log('Service Worker registrado:', reg);
    }).catch(err => {
        console.log('Erro ao registrar SW:', err);
    });
}
@endif
</script>
@endpush
