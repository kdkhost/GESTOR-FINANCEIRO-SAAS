@extends('layouts.admin.app')

@section('titulo', 'Auditoria')
@section('titulo_pagina', 'Auditoria do Sistema')

@section('breadcrumb')
    <li class="breadcrumb-item active">Auditoria</li>
@endsection

@section('conteudo')
<div class="row g-3 mb-4">
    <div class="col-xl-3 col-md-6">
        <div class="card bg-primary text-white h-100">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <h6 class="text-white-50 mb-1">Total Hoje</h6>
                        <h3 class="mb-0" id="stat-total-hoje">0</h3>
                    </div>
                    <i class="bi bi-calendar-check fs-2 text-white-50"></i>
                </div>
            </div>
        </div>
    </div>
    <div class="col-xl-3 col-md-6">
        <div class="card bg-success text-white h-100">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <h6 class="text-white-50 mb-1">Este Mes</h6>
                        <h3 class="mb-0" id="stat-total-mes">0</h3>
                    </div>
                    <i class="bi bi-calendar-month fs-2 text-white-50"></i>
                </div>
            </div>
        </div>
    </div>
    <div class="col-xl-3 col-md-6">
        <div class="card bg-info text-white h-100">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <h6 class="text-white-50 mb-1">Usuarios Ativos Hoje</h6>
                        <h3 class="mb-0" id="stat-usuarios">0</h3>
                    </div>
                    <i class="bi bi-people fs-2 text-white-50"></i>
                </div>
            </div>
        </div>
    </div>
    <div class="col-xl-3 col-md-6">
        <div class="card bg-secondary text-white h-100">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <h6 class="text-white-50 mb-1">Total Geral</h6>
                        <h3 class="mb-0" id="stat-total-geral">0</h3>
                    </div>
                    <i class="bi bi-journal-text fs-2 text-white-50"></i>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="card card-outline card-primary">
    <div class="card-header d-flex justify-content-between align-items-center flex-wrap gap-2">
        <h3 class="card-title mb-0"><i class="bi bi-journal-text me-2"></i>Registros de Auditoria</h3>
        <div class="d-flex gap-2">
            <button class="btn btn-sm btn-outline-secondary" onclick="limparAuditoria()">
                <i class="bi bi-trash me-1"></i>Limpar Antigos
            </button>
            <button class="btn btn-sm btn-outline-primary" onclick="exportarAuditoria()">
                <i class="bi bi-download me-1"></i>Exportar
            </button>
        </div>
    </div>
    <div class="card-body">
        <div class="row g-2 mb-3">
            <div class="col-md-2">
                <label class="form-label small mb-1">Acao</label>
                <select id="filtro-acao" class="form-select form-select-sm">
                    <option value="">Todas</option>
                    <option value="criar">Criar</option>
                    <option value="atualizar">Atualizar</option>
                    <option value="excluir">Excluir</option>
                    <option value="login">Login</option>
                    <option value="logout">Logout</option>
                    <option value="erro">Erro</option>
                </select>
            </div>
            <div class="col-md-2">
                <label class="form-label small mb-1">Entidade</label>
                <select id="filtro-entidade" class="form-select form-select-sm">
                    <option value="">Todas</option>
                    <option value="cliente">Cliente</option>
                    <option value="fornecedor">Fornecedor</option>
                    <option value="conta_pagar">Conta a Pagar</option>
                    <option value="conta_receber">Conta a Receber</option>
                    <option value="usuario">Usuario</option>
                    <option value="configuracao">Configuracao</option>
                    <option value="empresa">Empresa</option>
                    <option value="fatura">Fatura</option>
                    <option value="plano">Plano</option>
                </select>
            </div>
            <div class="col-md-2">
                <label class="form-label small mb-1">De</label>
                <input type="date" id="filtro-data-inicio" class="form-control form-control-sm">
            </div>
            <div class="col-md-2">
                <label class="form-label small mb-1">Ate</label>
                <input type="date" id="filtro-data-fim" class="form-control form-control-sm">
            </div>
            <div class="col-md-2">
                <label class="form-label small mb-1">Busca</label>
                <input type="text" id="filtro-busca" class="form-control form-control-sm" placeholder="Buscar...">
            </div>
            <div class="col-md-2 d-flex align-items-end">
                <button class="btn btn-primary btn-sm w-100" onclick="carregarAuditoria()">
                    <i class="bi bi-search me-1"></i>Filtrar
                </button>
            </div>
        </div>

        <div class="table-responsive">
            <table class="table table-sm table-striped table-hover" id="tabela-auditoria">
                <thead>
                    <tr>
                        <th>Data/Hora</th>
                        <th>Usuario</th>
                        <th>Acao</th>
                        <th>Entidade</th>
                        <th>IP</th>
                        <th>URL</th>
                        <th class="text-end">Acoes</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td colspan="7" class="text-center text-muted py-4">
                            <i class="bi bi-info-circle me-2"></i>Clique em "Filtrar" para carregar os registros
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>

        <div id="paginacao-auditoria" class="d-flex justify-content-center mt-3"></div>
    </div>
</div>

<div class="modal fade" id="modal-detalhes" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title"><i class="bi bi-eye me-2"></i>Detalhes do Registro</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body" id="modal-detalhes-conteudo">
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
let paginaAtual = 1;

$(document).ready(function() {
    carregarEstatisticas();
    carregarAuditoria();
});

function carregarEstatisticas() {
    $.get('{{ route("admin.auditoria.estatisticas") }}', function(r) {
        $('#stat-total-hoje').text(r.total_hoje.toLocaleString());
        $('#stat-total-mes').text(r.total_mes.toLocaleString());
        $('#stat-total-geral').text(r.total_geral.toLocaleString());
        $('#stat-usuarios').text(r.usuarios_ativos_hoje.toLocaleString());
    });
}

function carregarAuditoria(pagina = 1) {
    paginaAtual = pagina;
    
    const params = {
        page: pagina,
        acao: $('#filtro-acao').val(),
        entidade: $('#filtro-entidade').val(),
        data_inicio: $('#filtro-data-inicio').val(),
        data_fim: $('#filtro-data-fim').val(),
        busca: $('#filtro-busca').val()
    };

    $.get('{{ route("admin.auditoria.listar") }}', params, function(r) {
        renderizarTabela(r.data);
        renderizarPaginacao(r);
    }).fail(() => {
        toast('Erro ao carregar auditoria', 'erro');
    });
}

function renderizarTabela(dados) {
    const tbody = $('#tabela-auditoria tbody');
    tbody.empty();
    
    if (!dados || dados.length === 0) {
        tbody.append('<tr><td colspan="7" class="text-center text-muted py-4">Nenhum registro encontrado</td></tr>');
        return;
    }
    
    dados.forEach(function(item) {
        const coresAcao = {
            criar: 'success',
            atualizar: 'primary',
            excluir: 'danger',
            login: 'info',
            logout: 'secondary',
            erro: 'warning',
            visualizar: 'light'
        };
        
        const corAcao = coresAcao[item.acao] || 'secondary';
        
        const tr = $('<tr>').html(`
            <td class="text-nowrap">${new Date(item.created_at).toLocaleString('pt-BR')}</td>
            <td><span class="badge bg-dark">${item.user_name || 'Sistema'}</span></td>
            <td><span class="badge bg-${corAcao}">${item.acao.toUpperCase()}</span></td>
            <td>${item.entidade_formatted || item.entidade}</td>
            <td><small class="text-muted">${item.ip || '-'}</small></td>
            <td><small class="text-truncate d-inline-block" style="max-width:200px;" title="${item.url || '-'}">${item.url || '-'}</small></td>
            <td class="text-end">
                <button class="btn btn-sm btn-outline-primary" onclick="verDetalhes(${item.id})">
                    <i class="bi bi-eye"></i>
                </button>
            </td>
        `);
        
        tbody.append(tr);
    });
}

function renderizarPaginacao(r) {
    const container = $('#paginacao-auditoria');
    container.empty();
    
    if (r.last_page <= 1) return;
    
    let html = '<nav><ul class="pagination pagination-sm">';
    
    for (let i = 1; i <= r.last_page; i++) {
        const active = i === r.current_page ? 'active' : '';
        html += `<li class="page-item ${active}"><button class="page-link" onclick="carregarAuditoria(${i})">${i}</button></li>`;
    }
    
    html += '</ul></nav>';
    container.html(html);
}

function verDetalhes(id) {
    $.get('{{ route("admin.auditoria.detalhes", "") }}/' + id, function(r) {
        if (!r.sucesso) {
            toast('Erro ao carregar detalhes', 'erro');
            return;
        }
        
        const item = r.auditoria;
        
        let html = `
            <div class="row g-3">
                <div class="col-md-6">
                    <label class="form-label fw-bold">Usuario</label>
                    <p>${item.user_name} (${item.user_email})</p>
                </div>
                <div class="col-md-6">
                    <label class="form-label fw-bold">Data/Hora</label>
                    <p>${new Date(item.created_at).toLocaleString('pt-BR')}</p>
                </div>
                <div class="col-md-6">
                    <label class="form-label fw-bold">Acao</label>
                    <p><span class="badge bg-primary">${item.acao.toUpperCase()}</span></p>
                </div>
                <div class="col-md-6">
                    <label class="form-label fw-bold">Entidade</label>
                    <p>${item.entidade_formatted || item.entidade} #${item.entidade_id || 'N/A'}</p>
                </div>
                <div class="col-12">
                    <label class="form-label fw-bold">URL</label>
                    <p><code>${item.url || '-'}</code></p>
                </div>
                <div class="col-md-6">
                    <label class="form-label fw-bold">IP</label>
                    <p>${item.ip || '-'}</p>
                </div>
                <div class="col-md-6">
                    <label class="form-label fw-bold">Metodo</label>
                    <p><span class="badge bg-secondary">${item.metodo || '-'}</span></p>
                </div>
        `;
        
        if (item.dados_anteriores) {
            html += `
                <div class="col-12">
                    <label class="form-label fw-bold">Dados Anteriores</label>
                    <pre class="bg-light p-2 rounded"><code>${JSON.stringify(item.dados_anteriores, null, 2)}</code></pre>
                </div>
            `;
        }
        
        if (item.dados_novos) {
            html += `
                <div class="col-12">
                    <label class="form-label fw-bold">Dados Novos</label>
                    <pre class="bg-light p-2 rounded"><code>${JSON.stringify(item.dados_novos, null, 2)}</code></pre>
                </div>
            `;
        }
        
        if (item.observacao) {
            html += `
                <div class="col-12">
                    <label class="form-label fw-bold">Observacao</label>
                    <p>${item.observacao}</p>
                </div>
            `;
        }
        
        html += '</div>';
        
        $('#modal-detalhes-conteudo').html(html);
        $('#modal-detalhes').modal('show');
    }).fail(() => {
        toast('Erro ao carregar detalhes', 'erro');
    });
}

function limparAuditoria() {
    SistemaAlert.fire({
        title: 'Limpar Registros Antigos',
        text: 'Quantos dias manter? (Registros mais antigos serao excluidos)',
        input: 'number',
        inputValue: 30,
        inputAttributes: { min: 7, max: 365 },
        showCancelButton: true,
        confirmButtonText: 'Limpar',
        cancelButtonText: 'Cancelar'
    }).then((result) => {
        if (result.isConfirmed) {
            $.post('{{ route("admin.auditoria.limpar") }}', {
                _token: $('meta[name="csrf-token"]').attr('content'),
                dias: result.value
            }, function(r) {
                toast(r.mensagem, 'sucesso');
                carregarEstatisticas();
                carregarAuditoria();
            }).fail(xhr => {
                toast(xhr.responseJSON?.mensagem || 'Erro ao limpar registros', 'erro');
            });
        }
    });
}

function exportarAuditoria() {
    const params = new URLSearchParams({
        acao: $('#filtro-acao').val(),
        entidade: $('#filtro-entidade').val(),
        data_inicio: $('#filtro-data-inicio').val(),
        data_fim: $('#filtro-data-fim').val(),
        busca: $('#filtro-busca').val()
    });
    
    window.open('{{ route("admin.auditoria.listar") }}?' + params.toString() + '&format=csv', '_blank');
    toast('Exportacao iniciada', 'info');
}
</script>
@endpush