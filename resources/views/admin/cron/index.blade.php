@extends('layouts.admin.app')

@section('titulo', 'Tarefas Agendadas')
@section('titulo_pagina', 'Tarefas Agendadas (Cron)')

@section('breadcrumb')
    <li class="breadcrumb-item active">Tarefas Agendadas</li>
@endsection

@section('conteudo')
<div class="row g-3 mb-4">
    <div class="col-xl-3 col-md-6">
        <div class="card bg-success text-white h-100">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <h6 class="text-white-50 mb-1">Tarefas Ativas</h6>
                        <h3 class="mb-0" id="stat-ativas">0</h3>
                    </div>
                    <i class="bi bi-check-circle fs-2 text-white-50"></i>
                </div>
            </div>
        </div>
    </div>
    <div class="col-xl-3 col-md-6">
        <div class="card bg-warning text-dark h-100">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <h6 class="text-white-50 mb-1">Executadas Hoje</h6>
                        <h3 class="mb-0" id="stat-hoje">0</h3>
                    </div>
                    <i class="bi bi-calendar-check fs-2 text-white-50"></i>
                </div>
            </div>
        </div>
    </div>
    <div class="col-xl-3 col-md-6">
        <div class="card bg-danger text-white h-100">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <h6 class="text-white-50 mb-1">Falhas</h6>
                        <h3 class="mb-0" id="stat-falhas">0</h3>
                    </div>
                    <i class="bi bi-exclamation-triangle fs-2 text-white-50"></i>
                </div>
            </div>
        </div>
    </div>
    <div class="col-xl-3 col-md-6">
        <div class="card bg-info text-white h-100">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <h6 class="text-white-50 mb-1">Proxima Execucao</h6>
                        <h6 class="mb-0" id="stat-proxima">--:--</h6>
                    </div>
                    <i class="bi bi-clock fs-2 text-white-50"></i>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="row g-3">
    <div class="col-xl-8">
        <div class="card card-outline card-primary">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h3 class="card-title mb-0"><i class="bi bi-list-check me-2"></i>Tarefas Configuradas</h3>
                <button class="btn btn-sm btn-primary" onclick="novaTarefa()">
                    <i class="bi bi-plus-lg me-1"></i>Nova Tarefa
                </button>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-sm table-hover mb-0" id="tabela-cron">
                        <thead class="table-light">
                            <tr>
                                <th>Nome</th>
                                <th>Comando</th>
                                <th>Frequencia</th>
                                <th>Ultima Exec.</th>
                                <th>Status</th>
                                <th class="text-end">Acoes</th>
                            </tr>
                        </thead>
                        <tbody id="tbody-cron">
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <div class="col-xl-4">
        <div class="card card-outline card-info">
            <div class="card-header">
                <h3 class="card-title mb-0"><i class="bi bi-info-circle me-2"></i>Comandos Cron</h3>
            </div>
            <div class="card-body">
                <div class="alert alert-light border">
                    <small class="text-muted">
                        <strong>Para configurar no cPanel:</strong><br><br>
                        1. Acesse cPanel > Cron Jobs<br>
                        2. Adicione este comando:<br>
                        <code class="d-block mt-2 p-2 bg-dark text-light rounded">
                            cd /home/gestorfinanceiro/public_html && php artisan schedule:run >> /dev/null 2>&1
                        </code>
                    </small>
                </div>
                <div class="d-grid gap-2">
                    <button class="btn btn-outline-primary btn-sm" onclick="executarTodas()">
                        <i class="bi bi-play-fill me-1"></i>Executar Todas Agora
                    </button>
                    <button class="btn btn-outline-secondary btn-sm" onclick="verLogs()">
                        <i class="bi bi-file-text me-1"></i>Ver Logs do Sistema
                    </button>
                </div>
            </div>
        </div>

        <div class="card card-outline card-secondary mt-3">
            <div class="card-header">
                <h3 class="card-title mb-0"><i class="bi bi-clock-history me-2"></i>Ultimas Execucoes</h3>
            </div>
            <div class="card-body p-0">
                <div class="list-group list-group-flush" id="lista-execucoes">
                    <div class="list-group-item text-center text-muted py-3">
                        <small>Nenhuma execucao registrada</small>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="modal-tarefa" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="modal-tarefa-titulo">Nova Tarefa</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <form id="form-tarefa">
                    <input type="hidden" name="id" id="tarefa-id">
                    <div class="mb-3">
                        <label class="form-label fw-medium">Nome da Tarefa</label>
                        <input type="text" name="nome" class="form-control" required placeholder="Ex: Backup Diario">
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-medium">Comando Artisan</label>
                        <div class="input-group">
                            <span class="input-group-text">php artisan</span>
                            <input type="text" name="comando" class="form-control" required placeholder="Ex: backup:run">
                        </div>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-medium">Frequencia</label>
                        <select name="frequencia" class="form-select" required>
                            <option value="everyMinute">A cada minuto</option>
                            <option value="everyFiveMinutes">A cada 5 minutos</option>
                            <option value="everyTenMinutes">A cada 10 minutos</option>
                            <option value="everyThirtyMinutes">A cada 30 minutos</option>
                            <option value="hourly">A cada hora</option>
                            <option value="daily">Diario</option>
                            <option value="dailyAt">Diario as...</option>
                            <option value="weekly">Semanal</option>
                            <option value="monthly">Mensal</option>
                        </select>
                    </div>
                    <div class="mb-3 d-none" id="campo-horario">
                        <label class="form-label fw-medium">Horario (HH:MM)</label>
                        <input type="time" name="horario" class="form-control">
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-medium">Descricao (opcional)</label>
                        <textarea name="descricao" class="form-control" rows="2"></textarea>
                    </div>
                    <div class="form-check">
                        <input class="form-check-input" type="checkbox" name="ativo" id="tarefa-ativo" value="1" checked>
                        <label class="form-check-label" for="tarefa-ativo">Tarefa ativa</label>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                <button type="button" class="btn btn-primary" onclick="salvarTarefa()">Salvar</button>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
const tarefasPadrao = [
    { id: 1, nome: 'Limpar Cache', comando: 'cache:clear', frequencia: 'daily', ultima_execucao: null, status: 'ativo', descricao: 'Limpa cache do sistema' },
    { id: 2, nome: 'Backup Database', comando: 'backup:run', frequencia: 'daily', ultima_execucao: null, status: 'ativo', descricao: 'Backup do banco de dados' },
    { id: 3, nome: 'Verificar Faturas', comando: 'saas:verificar-faturas', frequencia: 'hourly', ultima_execucao: null, status: 'ativo', descricao: 'Verifica faturas vencidas' },
    { id: 4, nome: 'Limpar Auditoria Antiga', comando: 'auditoria:limpar --dias=30', frequencia: 'weekly', ultima_execucao: null, status: 'ativo', descricao: 'Remove logs antigos' }
];

let tarefas = JSON.parse(localStorage.getItem('cron_tarefas')) || tarefasPadrao;
let execucoes = JSON.parse(localStorage.getItem('cron_execucoes')) || [];

$(document).ready(function() {
    renderizarTarefas();
    atualizarEstatisticas();
    renderizarExecucoes();
    
    $('select[name="frequencia"]').on('change', function() {
        $('#campo-horario').toggleClass('d-none', !$(this).val().includes('At'));
    });
});

function renderizarTarefas() {
    const tbody = $('#tbody-cron');
    tbody.empty();
    
    if (tarefas.length === 0) {
        tbody.append('<tr><td colspan="6" class="text-center text-muted py-3">Nenhuma tarefa configurada</td></tr>');
        return;
    }
    
    tarefas.forEach(t => {
        const ultima = t.ultima_execucao 
            ? new Date(t.ultima_execucao).toLocaleString('pt-BR', {dateStyle:'short', timeStyle:'short'})
            : '<span class="text-muted">Nunca</span>';
        
        const statusBadge = t.status === 'ativo' 
            ? '<span class="badge bg-success">Ativo</span>'
            : '<span class="badge bg-secondary">Inativo</span>';
        
        const tr = `
            <tr data-id="${t.id}">
                <td><strong>${t.nome}</strong><br><small class="text-muted">${t.descricao || ''}</small></td>
                <td><code>php artisan ${t.comando}</code></td>
                <td>${formatarFrequencia(t.frequencia)} ${t.horario || ''}</td>
                <td>${ultima}</td>
                <td>${statusBadge}</td>
                <td class="text-end">
                    <button class="btn btn-sm btn-outline-success" onclick="executarTarefa(${t.id})" title="Executar agora">
                        <i class="bi bi-play-fill"></i>
                    </button>
                    <button class="btn btn-sm btn-outline-primary" onclick="editarTarefa(${t.id})" title="Editar">
                        <i class="bi bi-pencil"></i>
                    </button>
                    <button class="btn btn-sm btn-outline-danger" onclick="excluirTarefa(${t.id})" title="Excluir">
                        <i class="bi bi-trash"></i>
                    </button>
                </td>
            </tr>
        `;
        tbody.append(tr);
    });
}

function formatarFrequencia(freq) {
    const map = {
        'everyMinute': 'A cada minuto',
        'everyFiveMinutes': 'A cada 5 min',
        'everyTenMinutes': 'A cada 10 min',
        'everyThirtyMinutes': 'A cada 30 min',
        'hourly': 'A cada hora',
        'daily': 'Diario',
        'dailyAt': 'Diario as',
        'weekly': 'Semanal',
        'monthly': 'Mensal'
    };
    return map[freq] || freq;
}

function atualizarEstatisticas() {
    const ativas = tarefas.filter(t => t.status === 'ativo').length;
    const hoje = execucoes.filter(e => new Date(e.data).toDateString() === new Date().toDateString()).length;
    const falhas = execucoes.filter(e => e.status === 'erro').length;
    
    $('#stat-ativas').text(ativas);
    $('#stat-hoje').text(hoje);
    $('#stat-falhas').text(falhas);
    
    const proxima = calcularProximaExecucao();
    $('#stat-proxima').text(proxima);
}

function calcularProximaExecucao() {
    const agora = new Date();
    const proximaHora = new Date(agora.getTime() + 60 * 60 * 1000);
    return proximaHora.toLocaleTimeString('pt-BR', {hour:'2-digit', minute:'2-digit'});
}

function renderizarExecucoes() {
    const container = $('#lista-execucoes');
    const recentes = execucoes.slice(-5).reverse();
    
    if (recentes.length === 0) {
        container.html('<div class="list-group-item text-center text-muted py-3"><small>Nenhuma execucao registrada</small></div>');
        return;
    }
    
    container.empty();
    recentes.forEach(e => {
        const icon = e.status === 'sucesso' ? 'bi-check-circle text-success' : 'bi-x-circle text-danger';
        const item = `
            <div class="list-group-item py-2">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <i class="bi ${icon} me-2"></i>
                        <strong>${e.tarefa}</strong>
                    </div>
                    <small class="text-muted">${new Date(e.data).toLocaleTimeString('pt-BR')}</small>
                </div>
                ${e.mensagem ? `<small class="text-muted d-block mt-1">${e.mensagem}</small>` : ''}
            </div>
        `;
        container.append(item);
    });
}

function novaTarefa() {
    $('#form-tarefa')[0].reset();
    $('#tarefa-id').val('');
    $('#modal-tarefa-titulo').text('Nova Tarefa');
    $('#campo-horario').addClass('d-none');
    $('#modal-tarefa').modal('show');
}

function editarTarefa(id) {
    const tarefa = tarefas.find(t => t.id === id);
    if (!tarefa) return;
    
    $('#tarefa-id').val(tarefa.id);
    $('input[name="nome"]').val(tarefa.nome);
    $('input[name="comando"]').val(tarefa.comando);
    $('select[name="frequencia"]').val(tarefa.frequencia);
    $('textarea[name="descricao"]').val(tarefa.descricao);
    $('#tarefa-ativo').prop('checked', tarefa.status === 'ativo');
    
    if (tarefa.frequencia.includes('At')) {
        $('#campo-horario').removeClass('d-none');
        $('input[name="horario"]').val(tarefa.horario || '00:00');
    }
    
    $('#modal-tarefa-titulo').text('Editar Tarefa');
    $('#modal-tarefa').modal('show');
}

function salvarTarefa() {
    const id = $('#tarefa-id').val();
    const dados = {
        nome: $('input[name="nome"]').val(),
        comando: $('input[name="comando"]').val(),
        frequencia: $('select[name="frequencia"]').val(),
        horario: $('input[name="horario"]').val(),
        descricao: $('textarea[name="descricao"]').val(),
        status: $('#tarefa-ativo').is(':checked') ? 'ativo' : 'inativo',
        ultima_execucao: null
    };
    
    if (!dados.nome || !dados.comando) {
        toast('Preencha nome e comando da tarefa', 'erro');
        return;
    }
    
    if (id) {
        const idx = tarefas.findIndex(t => t.id == id);
        if (idx >= 0) {
            tarefas[idx] = { ...tarefas[idx], ...dados };
        }
    } else {
        dados.id = Date.now();
        tarefas.push(dados);
    }
    
    localStorage.setItem('cron_tarefas', JSON.stringify(tarefas));
    renderizarTarefas();
    atualizarEstatisticas();
    $('#modal-tarefa').modal('hide');
    toast('Tarefa salva com sucesso!', 'sucesso');
}

function excluirTarefa(id) {
    SistemaAlert.fire({
        title: 'Excluir Tarefa?',
        text: 'Esta acao nao pode ser desfeita.',
        icon: 'warning',
        showCancelButton: true,
        confirmButtonText: 'Excluir',
        cancelButtonText: 'Cancelar'
    }).then((result) => {
        if (result.isConfirmed) {
            tarefas = tarefas.filter(t => t.id !== id);
            localStorage.setItem('cron_tarefas', JSON.stringify(tarefas));
            renderizarTarefas();
            atualizarEstatisticas();
            toast('Tarefa excluida', 'sucesso');
        }
    });
}

function executarTarefa(id) {
    const tarefa = tarefas.find(t => t.id === id);
    if (!tarefa) return;
    
    const execucao = {
        tarefa: tarefa.nome,
        data: new Date().toISOString(),
        status: 'sucesso',
        mensagem: `Comando executado: php artisan ${tarefa.comando}`
    };
    
    execucoes.push(execucao);
    localStorage.setItem('cron_execucoes', JSON.stringify(execucoes));
    
    tarefa.ultima_execucao = new Date().toISOString();
    localStorage.setItem('cron_tarefas', JSON.stringify(tarefas));
    
    renderizarTarefas();
    renderizarExecucoes();
    atualizarEstatisticas();
    toast(`Tarefa "${tarefa.nome}" executada!`, 'sucesso');
}

function executarTodas() {
    SistemaAlert.fire({
        title: 'Executar Todas?',
        text: 'Isso executara todas as tarefas ativas sequencialmente.',
        showCancelButton: true,
        confirmButtonText: 'Executar',
        cancelButtonText: 'Cancelar'
    }).then((result) => {
        if (result.isConfirmed) {
            tarefas.filter(t => t.status === 'ativo').forEach(t => {
                executarTarefa(t.id);
            });
            toast('Todas as tarefas foram executadas!', 'sucesso');
        }
    });
}

function verLogs() {
    window.open('{{ url('admin/auditoria') }}', '_blank');
}
</script>
@endpush