@extends('layouts.admin.app')
@section('titulo', 'Metas Financeiras')
@section('titulo_pagina', 'Metas Financeiras')
@section('breadcrumb')
    <li class="breadcrumb-item">Planejamento</li>
    <li class="breadcrumb-item active">Metas</li>
@endsection
@section('conteudo')
<div class="row" id="lista-metas">
    <div class="col-12 text-center py-5 text-muted" id="loading-metas">
        <div class="spinner-border text-primary"></div><p class="mt-2">Carregando...</p>
    </div>
</div>
<div class="d-flex justify-content-end mb-3">
    <button class="btn btn-primary" id="btn-nova-meta">
        <i class="bi bi-plus-lg me-1"></i>Nova Meta
    </button>
</div>

{{-- Modal --}}
<div class="modal fade" id="modal-meta" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header bg-primary text-white">
                <h5 class="modal-title"><i class="bi bi-trophy me-2"></i><span id="modal-meta-titulo">Nova Meta</span></h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <form id="form-meta">
                <div class="modal-body">
                    <input type="hidden" id="meta-id">
                    <div class="row g-3">
                        <div class="col-md-8">
                            <label class="form-label fw-medium">Titulo <span class="text-danger">*</span></label>
                            <input type="text" name="titulo" class="form-control" required placeholder="Ex: Reserva de emergencia">
                        </div>
                        <div class="col-md-4">
                            <label class="form-label fw-medium">Valor Alvo <span class="text-danger">*</span></label>
                            <div class="input-group">
                                <span class="input-group-text">R$</span>
                                <input type="text" name="valor_alvo" class="form-control mask-moeda" required placeholder="0,00">
                            </div>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label fw-medium">Valor Atual</label>
                            <div class="input-group">
                                <span class="input-group-text">R$</span>
                                <input type="text" name="valor_atual" class="form-control mask-moeda" placeholder="0,00">
                            </div>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label fw-medium">Data Inicio <span class="text-danger">*</span></label>
                            <input type="text" name="data_inicio" class="form-control mask-data" required placeholder="dd/mm/aaaa">
                        </div>
                        <div class="col-md-4">
                            <label class="form-label fw-medium">Prazo <span class="text-danger">*</span></label>
                            <input type="text" name="data_prazo" class="form-control mask-data" required placeholder="dd/mm/aaaa">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-medium">Icone</label>
                            <div class="input-group">
                                <span class="input-group-text" id="prev-icone-meta"><i class="bi bi-trophy"></i></span>
                                <input type="text" name="icone" id="input-icone-meta" class="form-control" value="bi-trophy" placeholder="bi-trophy">
                            </div>
                        </div>
                        <div class="col-md-3">
                            <label class="form-label fw-medium">Cor</label>
                            <input type="color" name="cor" class="form-control form-control-color" value="#f59e0b">
                        </div>
                        <div class="col-md-3">
                            <label class="form-label fw-medium">Status</label>
                            <select name="status" class="form-select">
                                <option value="ativa">Ativa</option>
                                <option value="concluida">Concluida</option>
                                <option value="pausada">Pausada</option>
                                <option value="cancelada">Cancelada</option>
                            </select>
                        </div>
                        <div class="col-12">
                            <label class="form-label fw-medium">Descricao</label>
                            <textarea name="descricao" class="form-control" rows="2"></textarea>
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
$(document).ready(function() {
const URLS_META = {
    index:  '{{ route("admin.metas.index") }}',
    store:  '{{ route("admin.metas.store") }}',
    show:   '/admin/metas/',
    update: '/admin/metas/',
    destroy:'/admin/metas/',
};

function carregarMetas() {
    $.get(URLS_META.index, function(r) {
        const container = $('#lista-metas');
        $('#loading-metas').remove();
        container.empty();
        if (!r.sucesso || !r.dados.length) {
            container.html('<div class="col-12 text-center py-5 text-muted"><i class="bi bi-trophy fs-1 d-block mb-3 opacity-25"></i><h5>Nenhuma meta cadastrada</h5><p>Crie sua primeira meta financeira!</p></div>');
            return;
        }
        r.dados.forEach(m => {
            const perc = m.percentual || 0;
            const corBarra = perc >= 100 ? 'success' : perc >= 50 ? 'primary' : perc >= 25 ? 'warning' : 'danger';
            const statusMap = {ativa:'primary',concluida:'success',pausada:'warning',cancelada:'secondary'};
            container.append(`
                <div class="col-lg-4 col-md-6 mb-4">
                    <div class="card h-100 border-0 shadow-sm">
                        <div class="card-body">
                            <div class="d-flex align-items-center justify-content-between mb-3">
                                <div class="d-flex align-items-center gap-2">
                                    <span class="rounded-circle d-flex align-items-center justify-content-center" style="width:40px;height:40px;background:${m.cor||'#f59e0b'}20;">
                                        <i class="bi ${m.icone||'bi-trophy'}" style="color:${m.cor||'#f59e0b'};font-size:1.2rem;"></i>
                                    </span>
                                    <div>
                                        <h6 class="mb-0 fw-bold">${m.titulo}</h6>
                                        <span class="badge bg-${statusMap[m.status]||'secondary'} small">${m.status}</span>
                                    </div>
                                </div>
                                <div class="dropdown">
                                    <button class="btn btn-sm btn-outline-secondary" data-bs-toggle="dropdown"><i class="bi bi-three-dots-vertical"></i></button>
                                    <ul class="dropdown-menu dropdown-menu-end">
                                        <li><a class="dropdown-item btn-editar-meta" href="#" data-id="${m.id}"><i class="bi bi-pencil me-2"></i>Editar</a></li>
                                        <li><a class="dropdown-item text-danger btn-excluir-meta" href="#" data-id="${m.id}"><i class="bi bi-trash me-2"></i>Excluir</a></li>
                                    </ul>
                                </div>
                            </div>
                            <div class="d-flex justify-content-between small text-muted mb-1">
                                <span>R$ ${parseFloat(m.valor_atual||0).toLocaleString('pt-BR',{minimumFractionDigits:2})}</span>
                                <span>R$ ${parseFloat(m.valor_alvo||0).toLocaleString('pt-BR',{minimumFractionDigits:2})}</span>
                            </div>
                            <div class="progress mb-2" style="height:8px;">
                                <div class="progress-bar bg-${corBarra}" style="width:${Math.min(100,perc)}%"></div>
                            </div>
                            <div class="d-flex justify-content-between small">
                                <span class="text-muted">Progresso</span>
                                <span class="fw-bold text-${corBarra}">${perc.toFixed(1)}%</span>
                            </div>
                            ${m.data_prazo ? '<div class="mt-2 small text-muted"><i class="bi bi-calendar3 me-1"></i>Prazo: '+m.data_prazo.split("-").reverse().join("/")+'</div>' : ''}
                        </div>
                    </div>
                </div>
            `);
        });
    }).fail(() => toast('Erro ao carregar metas.', 'erro'));
}

$('#btn-nova-meta').on('click', () => {
    $('#modal-meta-titulo').text('Nova Meta');
    $('#meta-id').val('');
    $('#form-meta')[0].reset();
    $('#form-meta [name="data_inicio"]').val(new Date().toLocaleDateString('pt-BR'));
    $('#modal-meta').modal('show');
});

document.getElementById('input-icone-meta').addEventListener('input', function() {
    document.getElementById('prev-icone-meta').innerHTML = '<i class="bi '+this.value+'"></i>';
});

$(document).on('click', '.btn-editar-meta', function(e) {
    e.preventDefault();
    $.get(URLS_META.show + $(this).data('id'), r => {
        if (!r.sucesso) return;
        const m = r.dado;
        $('#modal-meta-titulo').text('Editar Meta');
        $('#meta-id').val(m.id);
        const f = $('#form-meta');
        f.find('[name="titulo"]').val(m.titulo);
        f.find('[name="valor_alvo"]').val(parseFloat(m.valor_alvo||0).toLocaleString('pt-BR',{minimumFractionDigits:2}));
        f.find('[name="valor_atual"]').val(parseFloat(m.valor_atual||0).toLocaleString('pt-BR',{minimumFractionDigits:2}));
        f.find('[name="data_inicio"]').val(m.data_inicio ? m.data_inicio.split('-').reverse().join('/') : '');
        f.find('[name="data_prazo"]').val(m.data_prazo ? m.data_prazo.split('-').reverse().join('/') : '');
        f.find('[name="icone"]').val(m.icone||'bi-trophy');
        f.find('[name="cor"]').val(m.cor||'#f59e0b');
        f.find('[name="status"]').val(m.status||'ativa');
        f.find('[name="descricao"]').val(m.descricao||'');
        document.getElementById('prev-icone-meta').innerHTML = '<i class="bi '+(m.icone||'bi-trophy')+'"></i>';
        $('#modal-meta').modal('show');
    });
});

$('#form-meta').on('submit', function(e) {
    e.preventDefault();
    const id = $('#meta-id').val();
    const dados = {};
    $(this).serializeArray().forEach(f => dados[f.name] = f.value);
    $.ajax({
        url: id ? URLS_META.update + id : URLS_META.store,
        type: id ? 'PUT' : 'POST',
        data: dados,
        success: r => { if (r.sucesso) { toast(r.mensagem,'sucesso'); $('#modal-meta').modal('hide'); carregarMetas(); } else toast(r.mensagem||'Erro.','erro'); },
        error: r => toast(r.responseJSON?.mensagem||'Erro ao salvar.','erro'),
    });
});

$(document).on('click', '.btn-excluir-meta', function(e) {
    e.preventDefault();
    const id = $(this).data('id');
    confirmarExclusao(URLS_META.destroy+id, () => {
        $.ajax({ url: URLS_META.destroy+id, type: 'DELETE',
            success: r => { toast(r.mensagem,'sucesso'); carregarMetas(); },
            error: r => toast(r.responseJSON?.mensagem||'Erro.','erro'),
        });
    });
});

    carregarMetas();
}); // fecha $(document).ready
</script>
@endpush