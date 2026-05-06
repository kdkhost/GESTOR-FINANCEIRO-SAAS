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
                        <label class="form-label fw-medium">Expressao Cron</label>
                        <div class="input-group">
                            <input type="text" name="expressao_cron" class="form-control" required placeholder="Ex: 0 2 * * * (2h da manha diario)" value="0 2 * * *">
                            <button type="button" class="btn btn-outline-secondary dropdown-toggle" data-bs-toggle="dropdown" aria-expanded="false">Predefinidas</button>
                            <ul class="dropdown-menu dropdown-menu-end">
                                <li><a class="dropdown-item" href="#" onclick="setCron('* * * * *')">A cada minuto</a></li>
                                <li><a class="dropdown-item" href="#" onclick="setCron('*/5 * * * *')">A cada 5 min</a></li>
                                <li><a class="dropdown-item" href="#" onclick="setCron('*/10 * * * *')">A cada 10 min</a></li>
                                <li><a class="dropdown-item" href="#" onclick="setCron('*/30 * * * *')">A cada 30 min</a></li>
                                <li><a class="dropdown-item" href="#" onclick="setCron('0 * * * *')">A cada hora</a></li>
                                <li><hr class="dropdown-divider"></li>
                                <li><a class="dropdown-item" href="#" onclick="setCron('0 2 * * *')">Diario 2h da manha</a></li>
                                <li><a class="dropdown-item" href="#" onclick="setCron('0 */6 * * *')">A cada 6 horas</a></li>
                                <li><a class="dropdown-item" href="#" onclick="setCron('0 0 * * 0')">Semanal (domingo)</a></li>
                                <li><a class="dropdown-item" href="#" onclick="setCron('0 0 1 * *')">Mensal (dia 1)</a></li>
                            </ul>
                        </div>
                        <small class="text-muted">Formato: minuto hora dia-mes mes dia-semana</small>
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
const API_URL = '{{ url('api/admin/cron') }}';
let tarefas = [];
let execucoes = [];

$(document).ready(function() {
    carregarDados();

    // Atualiza a cada 30 segundos
    setInterval(carregarDados, 30000);
});

// Carrega dados da API
function carregarDados() {
    // Carrega tarefas
    $.get(API_URL)
        .done(function(res) {
            if (res.sucesso) {
                tarefas = res.dados;
                renderizarTarefas();
            }
        })
        .fail(function() {
            toast('Erro ao carregar tarefas', 'erro');
        });

    // Carrega estatisticas
    $.get(API_URL + '/estatisticas')
        .done(function(res) {
            if (res.sucesso) {
                $('#stat-ativas').text(res.ativas);
                $('#stat-hoje').text(res.executadas_hoje);
                $('#stat-falhas').text(res.falhas);
                $('#stat-proxima').text(res.proxima_execucao || '--:--');
            }
        });

    // Carrega execucoes recentes (do primeiro job com logs)
    if (tarefas.length > 0) {
        $.get(API_URL + '/' + tarefas[0].id + '/logs')
            .done(function(res) {
                if (res.sucesso) {
                    execucoes = res.dados.map(log => ({
                        tarefa: tarefas.find(t => t.id === log.cron_job_id)?.nome || 'Desconhecida',
                        data: log.executado_em_formatado || '-',
                        status: log.status,
                        mensagem: log.saida || log.erro
                    }));
                    renderizarExecucoes();
                }
            });
    }
}

function renderizarTarefas() {
    const tbody = $('#tbody-cron');
    tbody.empty();

    if (tarefas.length === 0) {
        tbody.append('<tr><td colspan="6" class="text-center text-muted py-3">Nenhuma tarefa configurada</td></tr>');
        return;
    }

    tarefas.forEach(t => {
        const ultima = t.ultima_execucao_formatada || 'Nunca';
        const proxima = t.proxima_execucao_formatada || '--';

        let statusBadge;
        if (t.ultimo_status === 'erro') {
            statusBadge = '<span class="badge bg-danger">Erro</span>';
        } else if (t.ativo) {
            statusBadge = '<span class="badge bg-success">Ativo</span>';
        } else {
            statusBadge = '<span class="badge bg-secondary">Inativo</span>';
        }

        const tr = `
            <tr data-id="${t.id}">
                <td><strong>${t.nome}</strong><br><small class="text-muted">${t.descricao || ''}</small></td>
                <td><code>php artisan ${t.comando}</code></td>
                <td>${t.frequencia_formatada || t.expressao_cron}</td>
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

function renderizarExecucoes() {
    const container = $('#lista-execucoes');

    if (execucoes.length === 0) {
        container.html('<div class="list-group-item text-center text-muted py-3"><small>Nenhuma execucao registrada</small></div>');
        return;
    }

    container.empty();
    execucoes.slice(0, 5).forEach(e => {
        const icon = e.status === 'sucesso' ? 'bi-check-circle text-success' : 'bi-x-circle text-danger';
        const data = new Date(e.data);
        const item = `
            <div class="list-group-item py-2">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <i class="bi ${icon} me-2"></i>
                        <strong>${e.tarefa}</strong>
                    </div>
                    <small class="text-muted">${data.toLocaleTimeString('pt-BR')}</small>
                </div>
                ${e.mensagem ? `<small class="text-muted d-block mt-1">${e.mensagem.substring(0, 100)}</small>` : ''}
            </div>
        `;
        container.append(item);
    });
}

function setCron(expressao) {
    $('input[name="expressao_cron"]').val(expressao);
}

function novaTarefa() {
    $('#form-tarefa')[0].reset();
    $('#tarefa-id').val('');
    $('#modal-tarefa-titulo').text('Nova Tarefa');
    $('#modal-tarefa').modal('show');
}

function editarTarefa(id) {
    const tarefa = tarefas.find(t => t.id === id);
    if (!tarefa) return;

    $('#tarefa-id').val(tarefa.id);
    $('input[name="nome"]').val(tarefa.nome);
    $('input[name="comando"]').val(tarefa.comando);
    $('input[name="expressao_cron"]').val(tarefa.expressao_cron);
    $('textarea[name="descricao"]').val(tarefa.descricao);
    $('#tarefa-ativo').prop('checked', tarefa.ativo);

    $('#modal-tarefa-titulo').text('Editar Tarefa');
    $('#modal-tarefa').modal('show');
}

function salvarTarefa() {
    const id = $('#tarefa-id').val();
    const dados = {
        nome: $('input[name="nome"]').val(),
        comando: $('input[name="comando"]').val(),
        expressao_cron: $('input[name="expressao_cron"]').val(),
        descricao: $('textarea[name="descricao"]').val(),
        ativo: $('#tarefa-ativo').is(':checked')
    };

    if (!dados.nome || !dados.comando || !dados.expressao_cron) {
        toast('Preencha todos os campos obrigatorios', 'erro');
        return;
    }

    const url = id ? API_URL + '/' + id : API_URL;
    const method = id ? 'PUT' : 'POST';

    $.ajax({
        url: url,
        method: method,
        data: dados,
        headers: {
            'X-CSRF-TOKEN': '{{ csrf_token() }}'
        }
    })
    .done(function(res) {
        if (res.sucesso) {
            toast(res.mensagem, 'sucesso');
            $('#modal-tarefa').modal('hide');
            carregarDados();
        } else {
            toast(res.mensagem || 'Erro ao salvar', 'erro');
        }
    })
    .fail(function(xhr) {
        const msg = xhr.responseJSON?.mensagem || 'Erro ao salvar tarefa';
        toast(msg, 'erro');
    });
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
            $.ajax({
                url: API_URL + '/' + id,
                method: 'DELETE',
                headers: {
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                }
            })
            .done(function(res) {
                if (res.sucesso) {
                    toast(res.mensagem, 'sucesso');
                    carregarDados();
                } else {
                    toast(res.mensagem || 'Erro ao excluir', 'erro');
                }
            })
            .fail(function() {
                toast('Erro ao excluir tarefa', 'erro');
            });
        }
    });
}

function executarTarefa(id) {
    const tarefa = tarefas.find(t => t.id === id);
    if (!tarefa) {
        console.error('Tarefa não encontrada:', id);
        return;
    }

    console.log('Executando tarefa:', tarefa.nome);

    // Desabilita o botão durante a execução
    const btn = $(`button[onclick="executarTarefa(${id})"]`);
    btn.prop('disabled', true).html('<i class="bi bi-hourglass-split"></i>');

    $.ajax({
        url: API_URL + '/' + id + '/executar',
        method: 'POST',
        headers: {
            'X-CSRF-TOKEN': '{{ csrf_token() }}'
        }
    })
    .done(function(res) {
        console.log('Resposta da execução:', res);
        btn.prop('disabled', false).html('<i class="bi bi-play-fill"></i>');
        if (res.sucesso) {
            toast(`Tarefa "${tarefa.nome}" executada em ${res.duracao_ms}ms`, 'sucesso');
            carregarDados();
        } else {
            toast(res.erro || 'Erro ao executar tarefa', 'erro');
        }
    })
    .fail(function(xhr) {
        console.error('Erro na execução:', xhr);
        btn.prop('disabled', false).html('<i class="bi bi-play-fill"></i>');
        const msg = xhr.responseJSON?.erro || xhr.responseJSON?.mensagem || 'Erro ao executar tarefa';
        toast(msg, 'erro');
    });
}

function executarTodas() {
    const ativas = tarefas.filter(t => t.ativo);
    if (ativas.length === 0) {
        toast('Nenhuma tarefa ativa para executar.', 'alerta');
        return;
    }

    SistemaAlert.fire({
        title: 'Executar Todas?',
        text: `Isso executará ${ativas.length} tarefa(s) ativa(s) sequencialmente.`,
        icon: 'warning',
        showCancelButton: true,
        confirmButtonText: 'Executar',
        cancelButtonText: 'Cancelar'
    }).then((result) => {
        if (result.isConfirmed) {
            console.log('Iniciando execução em massa de', ativas.length, 'tarefas');

            // Mostra loading
            const btn = $('button[onclick="executarTodas()"]');
            btn.prop('disabled', true).html('<i class="bi bi-hourglass-split me-1"></i>Executando...');

            let executadas = 0;
            let sucessos = 0;
            let erros = 0;

            // Executa sequencialmente (não em paralelo) para evitar sobrecarga
            async function executarSequencial() {
                for (const t of ativas) {
                    try {
                        console.log(`Executando tarefa ${t.id}: ${t.nome}`);
                        const res = await $.ajax({
                            url: API_URL + '/' + t.id + '/executar',
                            method: 'POST',
                            headers: { 'X-CSRF-TOKEN': '{{ csrf_token() }}' }
                        });

                        if (res.sucesso) {
                            sucessos++;
                            console.log(`Tarefa ${t.nome} executada com sucesso`);
                        } else {
                            erros++;
                            console.error(`Erro na tarefa ${t.nome}:`, res.erro);
                        }
                    } catch (err) {
                        erros++;
                        console.error(`Falha na tarefa ${t.nome}:`, err);
                    }
                    executadas++;
                }

                // Finaliza
                btn.prop('disabled', false).html('<i class="bi bi-play-fill me-1"></i>Executar Todas Agora');
                carregarDados();

                if (erros === 0) {
                    toast(`Todas as ${sucessos} tarefas executadas com sucesso!`, 'sucesso');
                } else {
                    toast(`${sucessos} sucesso(s), ${erros} erro(s). Verifique os logs.`, sucessos > 0 ? 'alerta' : 'erro');
                }
            }

            executarSequencial();
        }
    });
}

function verLogs() {
    window.open('{{ url('admin/auditoria') }}', '_blank');
}
</script>
@endpush