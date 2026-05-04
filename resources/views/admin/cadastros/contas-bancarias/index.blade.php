@extends('layouts.admin.app')

@section('titulo', 'Contas Bancarias')
@section('titulo_pagina', 'Contas Bancarias')

@section('breadcrumb')
    <li class="breadcrumb-item">Cadastros</li>
    <li class="breadcrumb-item active">Contas Bancarias</li>
@endsection

@section('conteudo')
<div class="card card-outline card-primary">
    <div class="card-header d-flex align-items-center justify-content-between">
        <h3 class="card-title mb-0"><i class="bi bi-bank me-2 text-primary"></i>Contas Bancarias</h3>
        <button class="btn btn-primary btn-sm" id="btn-novo"><i class="bi bi-plus-lg me-1"></i>Nova Conta</button>
    </div>
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover mb-0">
                <thead class="table-light">
                    <tr>
                        <th>#</th>
                        <th>Nome</th>
                        <th>Tipo</th>
                        <th>Agencia / Conta</th>
                        <th class="text-end">Saldo Atual</th>
                        <th class="text-center">Status</th>
                        <th class="text-end" style="width:130px">Acoes</th>
                    </tr>
                </thead>
                <tbody id="tbody-contas-bancarias">
                    <tr><td colspan="7" class="text-center py-4 text-muted"><i class="bi bi-hourglass-split me-2"></i>Carregando...</td></tr>
                </tbody>
            </table>
        </div>
    </div>
</div>

{{-- Modal Criar/Editar --}}
<div class="modal fade" id="modal-conta" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header bg-primary text-white">
                <h5 class="modal-title"><i class="bi bi-bank me-2"></i><span id="modal-titulo">Nova Conta Bancaria</span></h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <form id="form-conta">
                <div class="modal-body">
                    <input type="hidden" id="conta-id">
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label fw-medium">Nome da Conta <span class="text-danger">*</span></label>
                            <input type="text" name="nome" class="form-control" required placeholder="Ex: Conta Corrente Bradesco">
                        </div>
                        <div class="col-md-3">
                            <label class="form-label fw-medium">Tipo <span class="text-danger">*</span></label>
                            <select name="tipo" class="form-select" required>
                                <option value="">Selecione...</option>
                                <option value="corrente">Corrente</option>
                                <option value="poupanca">Poupanca</option>
                                <option value="salario">Salario</option>
                                <option value="investimento">Investimento</option>
                                <option value="carteira">Carteira</option>
                                <option value="outro">Outro</option>
                            </select>
                        </div>
                        <div class="col-md-3">
                            <label class="form-label fw-medium">Saldo Inicial <span class="text-danger">*</span></label>
                            <div class="input-group">
                                <span class="input-group-text">R$</span>
                                <input type="text" name="saldo_inicial" class="form-control mask-moeda" required placeholder="0,00">
                            </div>
                        </div>
                        <div class="col-md-3">
                            <label class="form-label fw-medium">Agencia</label>
                            <input type="text" name="agencia" class="form-control" placeholder="0000">
                        </div>
                        <div class="col-md-3">
                            <label class="form-label fw-medium">Numero da Conta</label>
                            <input type="text" name="numero_conta" class="form-control" placeholder="00000">
                        </div>
                        <div class="col-md-2">
                            <label class="form-label fw-medium">Digito</label>
                            <input type="text" name="digito" class="form-control" placeholder="0" maxlength="2">
                        </div>
                        <div class="col-md-4">
                            <label class="form-label fw-medium">Cor</label>
                            <div class="input-group">
                                <input type="color" name="cor" id="input-cor-cb" class="form-control form-control-color" value="#0d6efd">
                                <input type="text" id="cor-hex-cb" class="form-control" value="#0d6efd">
                            </div>
                        </div>
                        <div class="col-12">
                            <div class="form-check form-switch">
                                <input class="form-check-input" type="checkbox" name="incluir_no_total" id="incluir-total" value="1" checked>
                                <label class="form-check-label" for="incluir-total">Incluir no saldo total</label>
                            </div>
                        </div>
                        <div class="col-12">
                            <label class="form-label fw-medium">Observacoes</label>
                            <textarea name="observacoes" class="form-control" rows="2"></textarea>
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

{{-- Modal Ajustar Saldo --}}
<div class="modal fade" id="modal-ajustar" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header bg-warning text-dark">
                <h5 class="modal-title"><i class="bi bi-currency-dollar me-2"></i>Ajustar Saldo</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form id="form-ajustar">
                <div class="modal-body">
                    <input type="hidden" id="ajustar-id">
                    <div class="alert alert-info small" id="info-saldo-atual"></div>
                    <div class="mb-3">
                        <label class="form-label fw-medium">Novo Saldo <span class="text-danger">*</span></label>
                        <div class="input-group">
                            <span class="input-group-text">R$</span>
                            <input type="text" name="saldo_novo" class="form-control mask-moeda" required placeholder="0,00">
                        </div>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-medium">Motivo</label>
                        <input type="text" name="motivo" class="form-control" placeholder="Ex: Correcao de saldo...">
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Cancelar</button>
                    <button type="submit" class="btn btn-warning"><i class="bi bi-check-lg me-1"></i>Ajustar</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
const URLS = {
    listar: '{{ route("admin.contas-bancarias.listar") }}',
    store:  '{{ route("admin.contas-bancarias.store") }}',
    show:   '/admin/contas-bancarias/',
    update: '/admin/contas-bancarias/',
    destroy:'/admin/contas-bancarias/',
    ajustar:'/admin/contas-bancarias/',
};

document.getElementById('input-cor-cb').addEventListener('input', function() { document.getElementById('cor-hex-cb').value = this.value; });
document.getElementById('cor-hex-cb').addEventListener('input', function() { if (/^#[0-9A-Fa-f]{6}$/.test(this.value)) document.getElementById('input-cor-cb').value = this.value; });

const tipoLabels = { corrente:'Corrente', poupanca:'Poupanca', salario:'Salario', investimento:'Investimento', carteira:'Carteira', outro:'Outro' };

function carregarTabela() {
    $.get(URLS.listar, function(r) {
        const tbody = $('#tbody-contas-bancarias');
        tbody.empty();
        if (!r.dados.length) {
            tbody.html('<tr><td colspan="7" class="text-center py-4 text-muted"><i class="bi bi-inbox fs-3 d-block mb-2"></i>Nenhuma conta cadastrada.</td></tr>');
            return;
        }
        r.dados.forEach(c => {
            const saldo = parseFloat(c.saldo_atual || 0);
            const saldoFmt = 'R$ ' + saldo.toLocaleString('pt-BR',{minimumFractionDigits:2});
            const saldoCor = saldo >= 0 ? 'text-success' : 'text-danger';
            tbody.append(`<tr>
                <td class="text-muted small">${c.id}</td>
                <td>
                    <div class="d-flex align-items-center gap-2">
                        <span class="rounded-circle d-inline-block" style="width:12px;height:12px;background:${c.cor||'#0d6efd'};"></span>
                        <span class="fw-medium">${c.nome}</span>
                    </div>
                </td>
                <td><span class="badge bg-secondary">${tipoLabels[c.tipo]||c.tipo}</span></td>
                <td>${c.agencia ? c.agencia + ' / ' + (c.numero_conta||'') : '<span class="text-muted">-</span>'}</td>
                <td class="text-end fw-medium ${saldoCor}">${saldoFmt}</td>
                <td class="text-center"><span class="badge bg-${c.ativo?'success':'secondary'}">${c.ativo?'Ativa':'Inativa'}</span></td>
                <td class="text-end">
                    <div class="btn-group btn-group-sm">
                        <button class="btn btn-outline-warning btn-ajustar" data-id="${c.id}" data-saldo="${saldo}" data-nome="${c.nome}" title="Ajustar Saldo"><i class="bi bi-currency-dollar"></i></button>
                        <button class="btn btn-outline-primary btn-editar" data-id="${c.id}" title="Editar"><i class="bi bi-pencil"></i></button>
                        <button class="btn btn-outline-danger btn-excluir" data-id="${c.id}" title="Excluir"><i class="bi bi-trash"></i></button>
                    </div>
                </td>
            </tr>`);
        });
    }).fail(() => toast('Erro ao carregar contas bancarias.', 'erro'));
}

$('#btn-novo').on('click', () => {
    $('#modal-titulo').text('Nova Conta Bancaria'); $('#conta-id').val(''); $('#form-conta')[0].reset();
    $('#input-cor-cb').val('#0d6efd'); $('#cor-hex-cb').val('#0d6efd'); $('#incluir-total').prop('checked', true);
    $('#modal-conta').modal('show');
});

$(document).on('click', '.btn-editar', function() {
    $.get(URLS.show + $(this).data('id'), r => {
        if (!r.sucesso) return;
        const c = r.dado;
        $('#modal-titulo').text('Editar Conta Bancaria'); $('#conta-id').val(c.id);
        const f = $('#form-conta');
        ['nome','tipo','agencia','numero_conta','digito','observacoes'].forEach(k => f.find(`[name="${k}"]`).val(c[k]||''));
        f.find('[name="saldo_inicial"]').val(parseFloat(c.saldo_inicial||0).toLocaleString('pt-BR',{minimumFractionDigits:2}));
        f.find('[name="cor"]').val(c.cor||'#0d6efd'); $('#cor-hex-cb').val(c.cor||'#0d6efd');
        $('#incluir-total').prop('checked', !!c.incluir_no_total);
        $('#modal-conta').modal('show');
    });
});

$('#form-conta').on('submit', function(e) {
    e.preventDefault();
    const id = $('#conta-id').val();
    const dados = {};
    $(this).serializeArray().forEach(f => dados[f.name] = f.value);
    dados.incluir_no_total = $('#incluir-total').is(':checked') ? 1 : 0;
    $.ajax({
        url: id ? URLS.update + id : URLS.store,
        type: id ? 'PUT' : 'POST',
        data: dados,
        success: r => { if (r.sucesso) { toast(r.mensagem,'sucesso'); $('#modal-conta').modal('hide'); carregarTabela(); } else toast(r.mensagem||'Erro.','erro'); },
        error: r => toast(r.responseJSON?.mensagem||'Erro ao salvar.','erro'),
    });
});

$(document).on('click', '.btn-ajustar', function() {
    const id = $(this).data('id');
    const saldo = $(this).data('saldo');
    const nome = $(this).data('nome');
    $('#ajustar-id').val(id);
    $('#info-saldo-atual').html(`Conta: <strong>${nome}</strong><br>Saldo atual: <strong>R$ ${parseFloat(saldo).toLocaleString('pt-BR',{minimumFractionDigits:2})}</strong>`);
    $('#form-ajustar [name="saldo_novo"]').val(parseFloat(saldo).toLocaleString('pt-BR',{minimumFractionDigits:2}));
    $('#form-ajustar [name="motivo"]').val('');
    $('#modal-ajustar').modal('show');
});

$('#form-ajustar').on('submit', function(e) {
    e.preventDefault();
    const id = $('#ajustar-id').val();
    const dados = {};
    $(this).serializeArray().forEach(f => dados[f.name] = f.value);
    $.ajax({
        url: URLS.ajustar + id + '/ajustar-saldo',
        type: 'POST',
        data: dados,
        success: r => { if (r.sucesso) { toast(r.mensagem,'sucesso'); $('#modal-ajustar').modal('hide'); carregarTabela(); } else toast(r.mensagem||'Erro.','erro'); },
        error: r => toast(r.responseJSON?.mensagem||'Erro ao ajustar saldo.','erro'),
    });
});

$(document).on('click', '.btn-excluir', function() {
    const id = $(this).data('id');
    confirmarExclusao(URLS.destroy+id, () => {
        $.ajax({ url: URLS.destroy+id, type: 'DELETE',
            success: r => { toast(r.mensagem,'sucesso'); carregarTabela(); },
            error: r => toast(r.responseJSON?.mensagem||'Erro.','erro'),
        });
    });
});

carregarTabela();
</script>
@endpush
