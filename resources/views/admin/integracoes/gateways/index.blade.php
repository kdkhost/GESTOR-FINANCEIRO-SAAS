@extends('layouts.admin.app')

@section('titulo', 'Gateways de Cobrança')
@section('titulo_pagina', 'Gateways de Cobrança')

@section('breadcrumb')
    <li class="breadcrumb-item active">Gateways</li>
@endsection

@section('conteudo')
<div class="row">
    <div class="col-12">
        <div class="card card-outline card-primary">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h3 class="card-title mb-0"><i class="bi bi-credit-card-2-front me-2"></i>Gateways de Cobrança</h3>
                <span class="badge bg-secondary">Configuração de conexões de pagamento</span>
            </div>
            <div class="card-body">
                <p class="text-muted">Gerencie os provedores de cobrança que estarão habilitados no sistema. Configure credenciais, ative o ambiente de produção e habilite o gateway.</p>
                <div class="table-responsive">
                    <table class="table table-hover table-bordered align-middle">
                        <thead>
                            <tr>
                                <th>Gateway</th>
                                <th>Identificador</th>
                                <th>Status</th>
                                <th>Ambiente</th>
                                <th>Ações</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($gateways as $gateway)
                                <tr id="gateway-row-{{ $gateway->id }}">
                                    <td>{{ $gateway->nome }}</td>
                                    <td>{{ $gateway->identificador }}</td>
                                    <td>
                                        <span class="badge gateway-status bg-{{ $gateway->ativo ? 'success' : 'secondary' }}">
                                            {{ $gateway->ativo ? 'Ativo' : 'Inativo' }}
                                        </span>
                                    </td>
                                    <td>
                                        <span class="badge gateway-ambiente bg-{{ $gateway->sandbox ? 'warning' : 'info' }}">
                                            {{ $gateway->sandbox ? 'Sandbox' : 'Produção' }}
                                        </span>
                                    </td>
                                    <td>
                                        <button class="btn btn-sm btn-outline-primary btn-editar-gateway"
                                                data-id="{{ $gateway->id }}"
                                                data-nome="{{ $gateway->nome }}"
                                                data-identificador="{{ $gateway->identificador }}"
                                                data-ativo="{{ $gateway->ativo ? '1' : '0' }}"
                                                data-sandbox="{{ $gateway->sandbox ? '1' : '0' }}"
                                                data-credenciais='@json($gateway->credenciais)'
                                                data-configuracoes='@json($gateway->configuracoes)'>
                                            <i class="bi bi-pencil-square me-1"></i>Editar
                                        </button>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

{{-- Modal de edição --}}
<div class="modal fade" id="modal-gateway" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-scrollable">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="modal-gateway-title">Editar Gateway</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Fechar"></button>
            </div>
            <form id="form-gateway">
                @csrf
                @method('PUT')
                <div class="modal-body">
                    <input type="hidden" id="gateway-id" name="gateway_id">

                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label fw-medium">Nome do gateway</label>
                            <input type="text" id="gateway-nome" class="form-control" readonly>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-medium">Identificador</label>
                            <input type="text" id="gateway-identificador" class="form-control" readonly>
                        </div>
                    </div>

                    <div id="gateway-credenciais" class="row g-3 mt-3"></div>
                    <div id="gateway-configuracoes" class="row g-3 mt-1"></div>

                    <div class="row g-3 mt-3">
                        <div class="col-md-6">
                            <input type="hidden" name="ativo" value="0">
                            <div class="form-check form-switch">
                                <input class="form-check-input" type="checkbox" id="gateway-ativo" name="ativo" value="1">
                                <label class="form-check-label" for="gateway-ativo">Ativar gateway</label>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <input type="hidden" name="sandbox" value="0">
                            <div class="form-check form-switch">
                                <input class="form-check-input" type="checkbox" id="gateway-sandbox" name="sandbox" value="1">
                                <label class="form-check-label" for="gateway-sandbox">Usar ambiente de sandbox</label>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-outline-info me-auto" id="btn-testar-gateway">
                        <i class="bi bi-plug me-1"></i>Testar conexÃ£o
                    </button>
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                    <button type="submit" class="btn btn-primary">Salvar alterações</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@php
    $gatewaysJson = $gateways->map(function ($gateway) {
        return [
            'id' => $gateway->id,
            'nome' => $gateway->nome,
            'identificador' => $gateway->identificador,
            'ativo' => $gateway->ativo,
            'sandbox' => $gateway->sandbox,
            'credenciais' => $gateway->credenciais ?? [],
            'configuracoes' => $gateway->configuracoes ?? [],
        ];
    })->values();
@endphp

@push('scripts')
<script>
    const gatewayCamposPorTipo = {
        mercadopago: [
            {name: 'access_token', label: 'Access Token', type: 'password'},
            {name: 'public_key', label: 'Public Key'},
            {name: 'webhook_secret', label: 'Webhook Secret', type: 'password'},
        ],
        stripe: [
            {name: 'secret_key', label: 'Secret Key', type: 'password'},
            {name: 'publishable_key', label: 'Publishable Key'},
        ],
        asaas: [
            {name: 'token', label: 'Token', type: 'password'},
            {name: 'account_id', label: 'Account ID'},
        ],
    };

    const gatewayConfiguracoesPorTipo = {
        mercadopago: [
            {name: 'processing_mode', label: 'Processamento', type: 'select', options: {automatic: 'Automatico', manual: 'Manual'}},
            {name: 'pix_expiration_time', label: 'Validade Pix (ISO 8601)', placeholder: 'P1D'},
            {name: 'boleto_expiration_time', label: 'Validade boleto (ISO 8601)', placeholder: 'P3D'},
            {name: 'pix_ativo', label: 'Pix ativo', type: 'checkbox'},
            {name: 'boleto_ativo', label: 'Boleto ativo', type: 'checkbox'},
            {name: 'cartao_credito_ativo', label: 'Cartao de credito ativo', type: 'checkbox'},
            {name: 'cartao_debito_ativo', label: 'Cartao de debito ativo', type: 'checkbox'},
            {name: 'card_brick_enabled', label: 'Card Payment Brick habilitado', type: 'checkbox'},
        ],
        stripe: [
            {name: 'cartao_credito_ativo', label: 'Cartao de credito ativo', type: 'checkbox'},
        ],
        asaas: [
            {name: 'pix_ativo', label: 'Pix ativo', type: 'checkbox'},
            {name: 'boleto_ativo', label: 'Boleto ativo', type: 'checkbox'},
        ],
    };

    let gateways = @json($gatewaysJson);

    function escapeHtml(valor) {
        return String(valor ?? '').replace(/[&<>"']/g, c => ({'&':'&amp;','<':'&lt;','>':'&gt;','"':'&quot;',"'":'&#039;'}[c]));
    }

    function construirCamposCredenciais(identificador, valores = {}) {
        const campos = gatewayCamposPorTipo[identificador] || [];
        return campos.map(campo => {
            const valor = valores[campo.name] || '';
            return `
                <div class="col-md-6">
                    <label class="form-label fw-medium">${campo.label}</label>
                    <input type="${campo.type || 'text'}" name="credenciais[${campo.name}]" class="form-control" value="${escapeHtml(valor)}">
                </div>
            `;
        }).join('');
    }

    function construirCamposConfiguracoes(identificador, valores = {}) {
        const campos = gatewayConfiguracoesPorTipo[identificador] || [];
        if (!campos.length) return '';

        return `
            <div class="col-12">
                <hr class="my-2">
                <h6 class="fw-bold mb-0"><i class="bi bi-sliders me-1"></i>Configuracoes de checkout</h6>
            </div>
            ${campos.map(campo => {
                const valor = valores[campo.name] ?? '';
                if (campo.type === 'checkbox') {
                    const checked = valor === true || valor === '1' || valor === 1 ? 'checked' : '';
                    return `
                        <div class="col-md-6">
                            <input type="hidden" name="configuracoes[${campo.name}]" value="0">
                            <div class="form-check form-switch mt-2">
                                <input class="form-check-input" type="checkbox" name="configuracoes[${campo.name}]" value="1" id="cfg-${campo.name}" ${checked}>
                                <label class="form-check-label" for="cfg-${campo.name}">${campo.label}</label>
                            </div>
                        </div>
                    `;
                }
                if (campo.type === 'select') {
                    const options = Object.entries(campo.options || {}).map(([key, label]) => `<option value="${key}" ${String(valor) === key ? 'selected' : ''}>${label}</option>`).join('');
                    return `
                        <div class="col-md-6">
                            <label class="form-label fw-medium">${campo.label}</label>
                            <select class="form-select" name="configuracoes[${campo.name}]">${options}</select>
                        </div>
                    `;
                }
                return `
                    <div class="col-md-6">
                        <label class="form-label fw-medium">${campo.label}</label>
                        <input type="text" name="configuracoes[${campo.name}]" class="form-control" value="${escapeHtml(valor)}" placeholder="${escapeHtml(campo.placeholder || '')}">
                    </div>
                `;
            }).join('')}
        `;
    }

    function abrirModalGateway(gatewayId) {
        const gateway = gateways.find(g => g.id === gatewayId);
        if (!gateway) return;

        $('#gateway-id').val(gateway.id);
        $('#gateway-nome').val(gateway.nome);
        $('#gateway-identificador').val(gateway.identificador);
        $('#gateway-ativo').prop('checked', gateway.ativo);
        $('#gateway-sandbox').prop('checked', gateway.sandbox);
        $('#gateway-credenciais').html(construirCamposCredenciais(gateway.identificador, gateway.credenciais));
        $('#gateway-configuracoes').html(construirCamposConfiguracoes(gateway.identificador, gateway.configuracoes));
        $('#modal-gateway-title').text(`Editar ${gateway.nome}`);
        $('#btn-testar-gateway').toggle(gateway.identificador === 'mercadopago');
        new bootstrap.Modal(document.getElementById('modal-gateway')).show();
    }

    function atualizarLinhaGateway(gateway) {
        const row = $(`#gateway-row-${gateway.id}`);
        row.find('.gateway-status')
            .removeClass('bg-success bg-secondary')
            .addClass(gateway.ativo ? 'bg-success' : 'bg-secondary')
            .text(gateway.ativo ? 'Ativo' : 'Inativo');
        row.find('.gateway-ambiente')
            .removeClass('bg-warning bg-info')
            .addClass(gateway.sandbox ? 'bg-warning' : 'bg-info')
            .text(gateway.sandbox ? 'Sandbox' : 'Producao');
        gateways = gateways.map(item => item.id === gateway.id ? gateway : item);
    }

    $(document).on('click', '.btn-editar-gateway', function () {
        abrirModalGateway(parseInt($(this).data('id'), 10));
    });

    $('#form-gateway').on('submit', function (e) {
        e.preventDefault();
        const gatewayId = $('#gateway-id').val();
        const method = 'PUT';
        const url = `{{ url('admin/gateways') }}/${gatewayId}`;
        const data = $(this).serializeArray();
        data.push({name: '_method', value: method});

        $.ajax({
            url,
            type: 'POST',
            data,
            success: response => {
                toast(response.mensagem, 'sucesso');
                atualizarLinhaGateway(response.gateway);
                bootstrap.Modal.getInstance(document.getElementById('modal-gateway'))?.hide();
            },
            error: response => {
                const errors = response.responseJSON?.errors;
                if (errors) {
                    const mensagem = Object.values(errors).flat().join('<br>');
                    toast(mensagem, 'erro');
                    return;
                }
                toast(response.responseJSON?.mensagem || 'Erro ao salvar gateway.', 'erro');
            }
        });
    });

    $('#btn-testar-gateway').on('click', function () {
        const gatewayId = $('#gateway-id').val();
        if (!gatewayId) return;

        $.post(`{{ url('admin/gateways') }}/${gatewayId}/testar`, {}, function (response) {
            toast(response.mensagem || 'Conexao validada.', 'sucesso');
        }).fail(function (response) {
            const errors = response.responseJSON?.errors;
            const mensagem = errors ? Object.values(errors).flat().join('<br>') : (response.responseJSON?.message || 'Erro ao testar conexao.');
            toast(mensagem, 'erro');
        });
    });
</script>
@endpush
