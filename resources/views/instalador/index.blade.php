<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Instalador - FinanceiroSaaS</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/toastify-js/src/toastify.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css">
    <style>
        :root { --inst-primary: #2563eb; --inst-dark: #0f172a; --inst-soft: #eef4ff; }
        body { min-height: 100vh; background: #f4f7fb; font-family: Inter, system-ui, -apple-system, BlinkMacSystemFont, "Segoe UI", sans-serif; color: #172033; }
        .install-shell { min-height: 100vh; display: grid; grid-template-columns: minmax(280px, 380px) 1fr; }
        .install-aside { background: linear-gradient(160deg, #0f172a, #1d4ed8); color: #fff; padding: 2rem; position: relative; overflow: hidden; }
        .install-aside::after { content: ""; position: absolute; inset: auto -80px -120px auto; width: 260px; height: 260px; border-radius: 50%; background: rgba(255,255,255,.12); }
        .brand-mark { width: 52px; height: 52px; border-radius: 14px; background: rgba(255,255,255,.15); display: grid; place-items: center; font-size: 1.6rem; margin-bottom: 1.25rem; }
        .step-list { list-style: none; padding: 0; margin: 2rem 0 0; position: relative; z-index: 1; }
        .step-list li { display: flex; gap: .75rem; align-items: center; padding: .65rem 0; color: rgba(255,255,255,.82); }
        .step-dot { width: 30px; height: 30px; border-radius: 50%; display: grid; place-items: center; background: rgba(255,255,255,.14); font-weight: 700; font-size: .85rem; }
        .step-list li.active { color: #fff; }
        .step-list li.active .step-dot { background: #fff; color: var(--inst-primary); }
        .install-main { padding: 2rem; }
        .install-card { background: #fff; border: 1px solid #e5e7eb; border-radius: 12px; box-shadow: 0 20px 60px rgba(15,23,42,.08); overflow: hidden; }
        .install-header { padding: 1.25rem 1.5rem; border-bottom: 1px solid #edf0f5; display: flex; align-items: center; justify-content: space-between; gap: 1rem; }
        .install-body { padding: 1.5rem; }
        .section-title { font-size: 1.02rem; font-weight: 800; margin-bottom: .25rem; }
        .section-desc { color: #64748b; margin-bottom: 1rem; }
        .status-row { border: 1px solid #edf0f5; border-radius: 10px; padding: .75rem .9rem; display: flex; align-items: center; justify-content: space-between; margin-bottom: .55rem; }
        .status-row .name { font-weight: 700; }
        .step-pane { display: none; }
        .step-pane.active { display: block; }
        .progress { height: .6rem; border-radius: 999px; }
        .btn-primary { background: var(--inst-primary); border-color: var(--inst-primary); }
        .form-label { font-weight: 700; font-size: .9rem; }
        .log-box { display: none; background: #0f172a; color: #dbeafe; border-radius: 10px; padding: .85rem; max-height: 180px; overflow: auto; font-size: .82rem; white-space: pre-wrap; }
        @media (max-width: 900px) { .install-shell { grid-template-columns: 1fr; } .install-aside { padding: 1.4rem; } .step-list { display: grid; grid-template-columns: repeat(2, minmax(0, 1fr)); gap: .25rem .75rem; } .install-main { padding: 1rem; } }
    </style>
</head>
<body>
<div class="install-shell">
    <aside class="install-aside">
        <div class="brand-mark"><i class="bi bi-wallet2"></i></div>
        <h1 class="h3 fw-bold mb-2">Instalador FinanceiroSaaS</h1>
        <p class="mb-0 opacity-75">Configure o sistema para Laravel 13, PHP 8.4+, MariaDB e hospedagem cPanel com segurança.</p>

        <ol class="step-list" id="steps">
            <li class="active" data-step-item="0"><span class="step-dot">1</span><span>Requisitos</span></li>
            <li data-step-item="1"><span class="step-dot">2</span><span>Banco</span></li>
            <li data-step-item="2"><span class="step-dot">3</span><span>Superadmin</span></li>
            <li data-step-item="3"><span class="step-dot">4</span><span>Dados</span></li>
            <li data-step-item="4"><span class="step-dot">5</span><span>Sistema</span></li>
            <li data-step-item="5"><span class="step-dot">6</span><span>Finalizar</span></li>
        </ol>
    </aside>

    <main class="install-main">
        <div class="install-card">
            <div class="install-header">
                <div>
                    <div class="text-uppercase text-primary fw-bold small">Instalação automática</div>
                    <h2 class="h4 mb-0" id="titulo-etapa">Verificação de requisitos</h2>
                </div>
                <span class="badge text-bg-light border" id="badge-progresso">Etapa 1 de 6</span>
            </div>
            <div class="progress rounded-0">
                <div class="progress-bar" id="barra-progresso" style="width: 16.66%"></div>
            </div>

            <div class="install-body">
                <section class="step-pane active" data-step-pane="0">
                    <h3 class="section-title">Ambiente do servidor</h3>
                    <p class="section-desc">Confirme extensões PHP, escrita em pastas críticas e compatibilidade para cPanel.</p>
                    <div class="row g-3">
                        <div class="col-lg-6">
                            <div class="d-flex justify-content-between align-items-center mb-2">
                                <strong>Requisitos PHP</strong>
                                <button class="btn btn-sm btn-outline-primary" id="btn-requisitos"><i class="bi bi-arrow-clockwise me-1"></i>Verificar</button>
                            </div>
                            <div id="lista-requisitos"></div>
                        </div>
                        <div class="col-lg-6">
                            <div class="d-flex justify-content-between align-items-center mb-2">
                                <strong>Permissões de escrita</strong>
                                <button class="btn btn-sm btn-outline-primary" id="btn-permissoes"><i class="bi bi-folder-check me-1"></i>Verificar</button>
                            </div>
                            <div id="lista-permissoes"></div>
                        </div>
                    </div>
                </section>

                <section class="step-pane" data-step-pane="1">
                    <h3 class="section-title">Banco de dados MariaDB/MySQL</h3>
                    <p class="section-desc">Informe os dados criados no cPanel. A configuração será salva no arquivo .env.</p>
                    <form id="form-banco" class="row g-3">
                        <div class="col-md-5">
                            <label class="form-label">Host</label>
                            <input type="text" name="db_host" class="form-control" value="{{ env('DB_HOST', '127.0.0.1') }}" required>
                        </div>
                        <div class="col-md-2">
                            <label class="form-label">Porta</label>
                            <input type="number" name="db_port" class="form-control" value="{{ env('DB_PORT', '3306') }}" required>
                        </div>
                        <div class="col-md-5">
                            <label class="form-label">Banco</label>
                            <input type="text" name="db_database" class="form-control" value="{{ env('DB_DATABASE') }}" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Usuário</label>
                            <input type="text" name="db_username" class="form-control" value="{{ env('DB_USERNAME') }}" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Senha</label>
                            <input type="password" name="db_password" class="form-control" value="{{ env('DB_PASSWORD') }}">
                        </div>
                        <div class="col-12 d-flex flex-wrap gap-2">
                            <button type="button" class="btn btn-outline-primary" id="btn-testar-banco"><i class="bi bi-plug me-1"></i>Testar conexão</button>
                            <button type="submit" class="btn btn-primary"><i class="bi bi-save me-1"></i>Salvar banco</button>
                            <button type="button" class="btn btn-success" id="btn-migrations"><i class="bi bi-database-check me-1"></i>Executar migrations</button>
                        </div>
                    </form>
                    <pre class="log-box mt-3" id="log-migrations"></pre>
                </section>

                <section class="step-pane" data-step-pane="2">
                    <h3 class="section-title">Usuário superadministrador</h3>
                    <p class="section-desc">Crie o primeiro acesso com permissão total ao administrativo.</p>
                    <form id="form-superadmin" class="row g-3">
                        <div class="col-md-4">
                            <label class="form-label">Nome</label>
                            <input type="text" name="name" class="form-control" required>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">E-mail</label>
                            <input type="email" name="email" class="form-control" required>
                        </div>
                        <div class="col-md-2">
                            <label class="form-label">Senha</label>
                            <input type="password" name="password" class="form-control" required minlength="8">
                        </div>
                        <div class="col-md-2">
                            <label class="form-label">Confirmar</label>
                            <input type="password" name="password_confirmation" class="form-control" required minlength="8">
                        </div>
                        <div class="col-12">
                            <button class="btn btn-primary" type="submit"><i class="bi bi-person-check me-1"></i>Criar superadmin</button>
                        </div>
                    </form>
                </section>

                <section class="step-pane" data-step-pane="3">
                    <h3 class="section-title">Dados iniciais, permissões e storage</h3>
                    <p class="section-desc">Publique roles, permissões granulares, seeds editáveis e link de uploads.</p>
                    <div class="row g-3">
                        <div class="col-md-4">
                            <button class="btn btn-outline-primary w-100 py-3" id="btn-seeders"><i class="bi bi-stars d-block fs-3 mb-2"></i>Executar seeders</button>
                        </div>
                        <div class="col-md-4">
                            <button class="btn btn-outline-primary w-100 py-3" id="btn-publicar-permissoes"><i class="bi bi-shield-lock d-block fs-3 mb-2"></i>Publicar permissões</button>
                        </div>
                        <div class="col-md-4">
                            <button class="btn btn-outline-primary w-100 py-3" id="btn-storage"><i class="bi bi-link-45deg d-block fs-3 mb-2"></i>Criar storage link</button>
                        </div>
                    </div>
                    <pre class="log-box mt-3" id="log-seeders"></pre>
                </section>

                <section class="step-pane" data-step-pane="4">
                    <h3 class="section-title">Identidade do sistema</h3>
                    <p class="section-desc">Defina nome, proprietário e SMTP inicial. Tudo poderá ser alterado no painel.</p>
                    <form id="form-configuracao" class="row g-3">
                        <div class="col-md-4">
                            <label class="form-label">Nome do sistema</label>
                            <input type="text" name="sistema_nome" class="form-control" value="FinanceiroSaaS" required>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">Proprietário</label>
                            <input type="text" name="sistema_proprietario" class="form-control" value="Marcelo Brad RJ" required>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">Descrição</label>
                            <input type="text" name="sistema_descricao" class="form-control" value="Gestão financeira SaaS">
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">Host SMTP</label>
                            <input type="text" name="smtp_host" class="form-control">
                        </div>
                        <div class="col-md-2">
                            <label class="form-label">Porta</label>
                            <input type="number" name="smtp_porta" class="form-control" value="587">
                        </div>
                        <div class="col-md-3">
                            <label class="form-label">Usuário SMTP</label>
                            <input type="text" name="smtp_usuario" class="form-control">
                        </div>
                        <div class="col-md-3">
                            <label class="form-label">Senha SMTP</label>
                            <input type="password" name="smtp_senha" class="form-control">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">E-mail remetente</label>
                            <input type="email" name="smtp_remetente" class="form-control">
                        </div>
                        <div class="col-12">
                            <button class="btn btn-primary" type="submit"><i class="bi bi-check2-circle me-1"></i>Salvar configurações</button>
                        </div>
                    </form>
                </section>

                <section class="step-pane" data-step-pane="5">
                    <h3 class="section-title">Finalizar instalação</h3>
                    <p class="section-desc">O sistema criará o arquivo de instalação concluída, limpará caches e ativará o painel administrativo.</p>
                    <div class="alert alert-info mb-3">
                        <i class="bi bi-info-circle me-1"></i>
                        Depois de finalizar, a rota do instalador bloqueará ações sensíveis e o acesso principal seguirá para o dashboard.
                    </div>
                    <button class="btn btn-success btn-lg" id="btn-finalizar"><i class="bi bi-rocket-takeoff me-1"></i>Finalizar e acessar o painel</button>
                </section>

                <div class="d-flex justify-content-between align-items-center mt-4 pt-3 border-top">
                    <button class="btn btn-outline-secondary" id="btn-voltar"><i class="bi bi-arrow-left me-1"></i>Voltar</button>
                    <button class="btn btn-primary" id="btn-avancar">Avançar<i class="bi bi-arrow-right ms-1"></i></button>
                </div>
            </div>
        </div>
    </main>
</div>

<script src="https://cdn.jsdelivr.net/npm/jquery@3.7.1/dist/jquery.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/toastify-js"></script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.all.min.js"></script>
<script>
const rotas = {
    requisitos: '{{ route('instalador.requisitos') }}',
    permissoes: '{{ route('instalador.permissoes') }}',
    testarBanco: '{{ route('instalador.testar-banco') }}',
    salvarBanco: '{{ route('instalador.salvar-banco') }}',
    migrations: '{{ route('instalador.migrations') }}',
    seeders: '{{ route('instalador.seeders') }}',
    publicarPermissoes: '{{ route('instalador.permissoes-sistema') }}',
    storage: '{{ route('instalador.storage-link') }}',
    superadmin: '{{ route('instalador.superadmin') }}',
    configuracao: '{{ route('instalador.configuracao') }}',
    finalizar: '{{ route('instalador.finalizar') }}',
};
let etapaAtual = 0;
const titulos = ['Verificação de requisitos', 'Banco de dados', 'Superadministrador', 'Dados iniciais', 'Identidade do sistema', 'Finalização'];

$.ajaxSetup({ headers: { 'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content } });

function toast(mensagem, tipo = 'sucesso') {
    const cores = { sucesso: '#16a34a', erro: '#dc2626', alerta: '#f59e0b', info: '#2563eb' };
    Toastify({ text: mensagem, duration: 3600, gravity: 'top', position: 'right', style: { background: cores[tipo] || cores.info, borderRadius: '8px' } }).showToast();
}

function setEtapa(indice) {
    etapaAtual = Math.max(0, Math.min(5, indice));
    $('[data-step-pane]').removeClass('active').filter(`[data-step-pane="${etapaAtual}"]`).addClass('active');
    $('[data-step-item]').removeClass('active').filter(`[data-step-item="${etapaAtual}"]`).addClass('active');
    $('#titulo-etapa').text(titulos[etapaAtual]);
    $('#badge-progresso').text(`Etapa ${etapaAtual + 1} de 6`);
    $('#barra-progresso').css('width', `${((etapaAtual + 1) / 6) * 100}%`);
    $('#btn-voltar').prop('disabled', etapaAtual === 0);
    $('#btn-avancar').toggle(etapaAtual < 5);
}

function linhaStatus(item, campoNome = 'nome') {
    const ok = Boolean(item.ok);
    return `<div class="status-row">
        <div><span class="name">${item[campoNome]}</span><small class="text-muted d-block">${item.valor || ''}</small></div>
        <span class="badge ${ok ? 'text-bg-success' : 'text-bg-danger'}">${ok ? 'OK' : 'Atenção'}</span>
    </div>`;
}

function tratarErro(xhr, padrao = 'Erro ao processar solicitação.') {
    const erros = xhr.responseJSON?.errors;
    if (erros) return toast(Object.values(erros).flat().join(' | '), 'erro');
    toast(xhr.responseJSON?.mensagem || padrao, 'erro');
}

$('#btn-voltar').on('click', () => setEtapa(etapaAtual - 1));
$('#btn-avancar').on('click', () => setEtapa(etapaAtual + 1));
$('[data-step-item]').on('click', function () { setEtapa(Number($(this).data('step-item'))); });

$('#btn-requisitos').on('click', function () {
    const btn = $(this).prop('disabled', true);
    $.get(rotas.requisitos, function (r) {
        $('#lista-requisitos').html(r.requisitos.map(item => linhaStatus(item)).join(''));
        toast(r.tudo_ok ? 'Requisitos verificados com sucesso.' : 'Há requisitos pendentes no servidor.', r.tudo_ok ? 'sucesso' : 'alerta');
    }).fail(xhr => tratarErro(xhr)).always(() => btn.prop('disabled', false));
});

$('#btn-permissoes').on('click', function () {
    const btn = $(this).prop('disabled', true);
    $.get(rotas.permissoes, function (r) {
        $('#lista-permissoes').html(r.pastas.map(item => linhaStatus(item, 'pasta')).join(''));
        toast(r.tudo_ok ? 'Pastas graváveis verificadas.' : 'Ajuste permissões no cPanel.', r.tudo_ok ? 'sucesso' : 'alerta');
    }).fail(xhr => tratarErro(xhr)).always(() => btn.prop('disabled', false));
});

$('#btn-testar-banco').on('click', function () {
    $.post(rotas.testarBanco, $('#form-banco').serialize())
        .done(r => toast(r.mensagem, r.sucesso ? 'sucesso' : 'erro'))
        .fail(xhr => tratarErro(xhr));
});

$('#form-banco').on('submit', function (e) {
    e.preventDefault();
    $.post(rotas.salvarBanco, $(this).serialize())
        .done(r => toast(r.mensagem, r.sucesso ? 'sucesso' : 'erro'))
        .fail(xhr => tratarErro(xhr));
});

$('#btn-migrations').on('click', function () {
    const btn = $(this).prop('disabled', true);
    $.post(rotas.migrations)
        .done(r => {
            $('#log-migrations').show().text(r.log || r.mensagem);
            toast(r.mensagem, r.sucesso ? 'sucesso' : 'erro');
        })
        .fail(xhr => tratarErro(xhr, 'Erro nas migrations.'))
        .always(() => btn.prop('disabled', false));
});

$('#form-superadmin').on('submit', function (e) {
    e.preventDefault();
    $.post(rotas.superadmin, $(this).serialize())
        .done(r => toast(r.mensagem, r.sucesso ? 'sucesso' : 'erro'))
        .fail(xhr => tratarErro(xhr));
});

$('#btn-seeders').on('click', function () {
    const btn = $(this).prop('disabled', true);
    $.post(rotas.seeders)
        .done(r => {
            $('#log-seeders').show().text(r.log || r.mensagem);
            toast(r.mensagem, r.sucesso ? 'sucesso' : 'erro');
        })
        .fail(xhr => tratarErro(xhr))
        .always(() => btn.prop('disabled', false));
});

$('#btn-publicar-permissoes').on('click', function () {
    $.post(rotas.publicarPermissoes)
        .done(r => toast(r.mensagem, r.sucesso ? 'sucesso' : 'erro'))
        .fail(xhr => tratarErro(xhr));
});

$('#btn-storage').on('click', function () {
    $.post(rotas.storage)
        .done(r => toast(r.mensagem, r.sucesso ? 'sucesso' : 'erro'))
        .fail(xhr => tratarErro(xhr));
});

$('#form-configuracao').on('submit', function (e) {
    e.preventDefault();
    $.post(rotas.configuracao, $(this).serialize())
        .done(r => toast(r.mensagem, r.sucesso ? 'sucesso' : 'erro'))
        .fail(xhr => tratarErro(xhr));
});

$('#btn-finalizar').on('click', function () {
    Swal.fire({
        title: 'Finalizar instalação?',
        text: 'O sistema será marcado como instalado e o painel administrativo será liberado.',
        icon: 'question',
        showCancelButton: true,
        confirmButtonText: 'Sim, finalizar',
        cancelButtonText: 'Cancelar',
    }).then(result => {
        if (!result.isConfirmed) return;
        $.post(rotas.finalizar)
            .done(r => {
                toast(r.mensagem, r.sucesso ? 'sucesso' : 'erro');
                if (r.redirect) window.location.href = r.redirect;
            })
            .fail(xhr => tratarErro(xhr));
    });
});

setEtapa(0);
$('#btn-requisitos').trigger('click');
$('#btn-permissoes').trigger('click');
</script>
</body>
</html>
