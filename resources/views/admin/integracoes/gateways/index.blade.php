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
                                <tr>
                                    <td>{{ $gateway->nome }}</td>
                                    <td>{{ $gateway->identificador }}</td>
                                    <td>
                                        <span class="badge bg-{{ $gateway->ativo ? 'success' : 'secondary' }}">
                                            {{ $gateway->ativo ? 'Ativo' : 'Inativo' }}
                                        </span>
                                    </td>
                                    <td>
                                        <span class="badge bg-{{ $gateway->sandbox ? 'warning' : 'info' }}">
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
                                                data-credenciais='@json($gateway->credenciais)'>
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

                    <div class="row g-3 mt-3">
                        <div class="col-md-6">
                            <div class="form-check form-switch">
                                <input class="form-check-input" type="checkbox" id="gateway-ativo" name="ativo" value="1">
                                <label class="form-check-label" for="gateway-ativo">Ativar gateway</label>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-check form-switch">
                                <input class="form-check-input" type="checkbox" id="gateway-sandbox" name="sandbox" value="1">
                                <label class="form-check-label" for="gateway-sandbox">Usar ambiente de sandbox</label>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                    <button type="submit" class="btn btn-primary">Salvar alterações</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    const gatewayCamposPorTipo = {
        mercadopago: [
            {name: 'access_token', label: 'Access Token'},
            {name: 'public_key', label: 'Public Key'},
        ],
        stripe: [
            {name: 'secret_key', label: 'Secret Key'},
            {name: 'publishable_key', label: 'Publishable Key'},
        ],
        asaas: [
            {name: 'token', label: 'Token'},
            {name: 'account_id', label: 'Account ID'},
        ],
    };

    const gateways = @json($gateways->map(function ($gateway) {
        return [
            'id' => $gateway->id,
            'nome' => $gateway->nome,
            'identificador' => $gateway->identificador,
            'ativo' => $gateway->ativo,
            'sandbox' => $gateway->sandbox,
            'credenciais' => $gateway->credenciais ?? [],
        ];
    }));

    function construirCamposCredenciais(identificador, valores = {}) {
        const campos = gatewayCamposPorTipo[identificador] || [];
        return campos.map(campo => {
            const valor = valores[campo.name] || '';
            return `
                <div class="col-md-6">
                    <label class="form-label fw-medium">${campo.label}</label>
                    <input type="text" name="credenciais[${campo.name}]" class="form-control" value="${valor}">
                </div>
            `;
        }).join('');
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
        $('#modal-gateway-title').text(`Editar ${gateway.nome}`);
        new bootstrap.Modal(document.getElementById('modal-gateway')).show();
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
                window.location.reload();
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
</script>
@endpush
