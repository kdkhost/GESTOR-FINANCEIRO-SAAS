@extends('layouts.admin.app')

@section('titulo', 'Manutencao')
@section('titulo_pagina', 'Modo Manutencao')

@section('breadcrumb')
    <li class="breadcrumb-item">Administracao</li>
    <li class="breadcrumb-item active">Manutencao</li>
@endsection

@section('conteudo')
<div class="row g-3">
    <div class="col-xl-8">
        <div class="card card-outline card-warning">
            <div class="card-header">
                <h3 class="card-title mb-0"><i class="bi bi-cone-striped me-2"></i>Controle de manutencao</h3>
            </div>
            <div class="card-body">
                <form id="form-manutencao">
                    <div class="form-check form-switch mb-3">
                        <input class="form-check-input" type="checkbox" role="switch" id="manutencao_ativa" name="manutencao_ativa"
                               value="1" {{ configuracao('manutencao_ativa', '0') === '1' ? 'checked' : '' }}>
                        <label class="form-check-label fw-medium" for="manutencao_ativa">Ativar modo manutencao</label>
                        <div class="form-text">Quando ativo, o sistema pode exibir uma pagina de manutencao (com liberacao por IP/dispositivo).</div>
                    </div>

                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label fw-medium">Liberacao automatica (opcional)</label>
                            <input type="text" class="form-control" name="manutencao_liberar_em"
                                   value="{{ configuracao('manutencao_liberar_em', '') }}"
                                   placeholder="Ex: 2026-05-04 23:59:59">
                            <div class="form-text">Formato recomendado: YYYY-MM-DD HH:MM:SS</div>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-medium">Mensagem</label>
                            <input type="text" class="form-control" name="manutencao_mensagem"
                                   value="{{ configuracao('manutencao_mensagem', 'Estamos realizando melhorias. Volte em breve.') }}"
                                   placeholder="Mensagem curta">
                        </div>
                        <div class="col-12">
                            <label class="form-label fw-medium">IPs liberados</label>
                            <textarea class="form-control" rows="4" name="manutencao_ips"
                                      placeholder="Um IP por linha. Ex:\n187.10.20.30\n200.150.10.5">{{ configuracao('manutencao_ips', '') }}</textarea>
                            <div class="form-text">IPs listados aqui poderao acessar o sistema mesmo com manutencao ativa.</div>
                        </div>
                        <div class="col-12">
                            <label class="form-label fw-medium">Dispositivos liberados</label>
                            <textarea class="form-control" rows="4" name="manutencao_dispositivos"
                                      placeholder="Um identificador por linha (ex: fingerprint do dispositivo).">{{ configuracao('manutencao_dispositivos', '') }}</textarea>
                            <div class="form-text">Opcional. Use quando voce tiver um identificador de dispositivo no front.</div>
                        </div>
                    </div>

                    <div class="mt-4 d-flex gap-2">
                        <button type="submit" class="btn btn-warning">
                            <i class="bi bi-check2-circle me-1"></i>Salvar manutencao
                        </button>
                        <a href="{{ url('/') }}" class="btn btn-outline-secondary">
                            <i class="bi bi-box-arrow-up-right me-1"></i>Ver site publico
                        </a>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <div class="col-xl-4">
        <div class="card card-outline card-secondary h-100">
            <div class="card-header">
                <h3 class="card-title mb-0"><i class="bi bi-info-circle me-2"></i>Recomendacoes</h3>
            </div>
            <div class="card-body">
                <div class="text-muted small">
                    <p class="mb-2">1. Use manutencao apenas quando necessario.</p>
                    <p class="mb-2">2. Cadastre seu IP para nao se bloquear.</p>
                    <p class="mb-0">3. Defina uma liberacao automatica quando houver janela prevista.</p>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
$('#form-manutencao').on('submit', function (e) {
    e.preventDefault();
    const fd = new FormData(this);
    fd.set('manutencao_ativa', $('#manutencao_ativa').is(':checked') ? '1' : '0');
    fd.append('_token', $('meta[name="csrf-token"]').attr('content'));

    $.ajax({
        url: '{{ route("admin.manutencao.salvar") }}',
        type: 'POST',
        data: fd,
        processData: false,
        contentType: false,
        success: r => toast(r.mensagem || 'Salvo.', 'sucesso'),
        error: xhr => toast(xhr.responseJSON?.mensagem || 'Erro ao salvar manutencao.', 'erro'),
    });
});
</script>
@endpush

