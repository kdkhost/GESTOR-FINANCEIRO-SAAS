<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Instalação — FinanceiroSaaS</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <style>
        body { background: linear-gradient(135deg,#1e3a5f 0%,#2563eb 50%,#0f172a 100%); min-height:100vh; font-family:'Segoe UI',sans-serif; }
        .installer-card { border-radius:20px; overflow:hidden; box-shadow:0 25px 60px rgba(0,0,0,.4); }
        .step-header { background:linear-gradient(135deg,#2563eb,#1e40af); }
        .step-item { cursor:pointer; transition:.2s; }
        .step-item.active .step-num { background:#2563eb; color:#fff; }
        .step-item.done .step-num { background:#22c55e; color:#fff; }
        .step-item .step-num { width:32px;height:32px;border-radius:50%;background:#e2e8f0;display:inline-flex;align-items:center;justify-content:center;font-weight:700;font-size:.85rem;flex-shrink:0; }
        .step-panel { display:none; }
        .step-panel.active { display:block; }
        .req-item { padding:.5rem .75rem; border-radius:8px; margin-bottom:.4rem; }
        .req-ok { background:#f0fdf4; border:1px solid #bbf7d0; }
        .req-fail { background:#fef2f2; border:1px solid #fecaca; }
        .progress-bar-animated { transition:width .5s ease; }
    </style>
</head>
<body class="d-flex align-items-center justify-content-center py-5">
<div class="container">
<div class="row justify-content-center">
<div class="col-xl-8 col-lg-9">

<div class="card installer-card border-0">

    <!-- Header -->
    <div class="step-header text-white text-center py-4 px-4">
        <div style="font-size:2.5rem;">⚙️</div>
        <h3 class="fw-bold mb-1">Instalação do Sistema</h3>
        <p class="mb-0 opacity-75">FinanceiroSaaS — Siga os passos para configurar o sistema</p>
    </div>

    <!-- Progress -->
    <div class="px-4 pt-3 pb-0 bg-white">
        <div class="progress" style="height:6px;border-radius:3px;">
            <div id="progress-bar" class="progress-bar bg-primary progress-bar-animated" style="width:14%"></div>
        </div>
        <div class="d-flex justify-content-between mt-1 mb-2">
            <small class="text-muted" id="progress-label">Etapa 1 de 7</small>
            <small class="text-muted" id="progress-pct">14%</small>
        </div>
    </div>

    <!-- Steps Nav -->
    <div class="bg-white px-4 pb-2">
        <div class="d-flex flex-wrap gap-2" id="steps-nav">
            <div class="step-item active d-flex align-items-center gap-2 px-3 py-2 rounded-pill border" data-step="1">
                <span class="step-num">1</span><span class="small fw-medium">Requisitos</span>
            </div>
            <div class="step-item d-flex align-items-center gap-2 px-3 py-2 rounded-pill border" data-step="2">
                <span class="step-num">2</span><span class="small fw-medium">Permissões</span>
            </div>
            <div class="step-item d-flex align-items-center gap-2 px-3 py-2 rounded-pill border" data-step="3">
                <span class="step-num">3</span><span class="small fw-medium">Banco de Dados</span>
            </div>
            <div class="step-item d-flex align-items-center gap-2 px-3 py-2 rounded-pill border" data-step="4">
                <span class="step-num">4</span><span class="small fw-medium">Migrations</span>
            </div>
            <div class="step-item d-flex align-items-center gap-2 px-3 py-2 rounded-pill border" data-step="5">
                <span class="step-num">5</span><span class="small fw-medium">Administrador</span>
            </div>
            <div class="step-item d-flex align-items-center gap-2 px-3 py-2 rounded-pill border" data-step="6">
                <span class="step-num">6</span><span class="small fw-medium">Configurações</span>
            </div>
            <div class="step-item d-flex align-items-center gap-2 px-3 py-2 rounded-pill border" data-step="7">
                <span class="step-num">7</span><span class="small fw-medium">Finalizar</span>
            </div>
        </div>
    </div>

    <!-- Body -->
    <div class="card-body bg-white px-4 py-4">

        <!-- STEP 1: Requisitos -->
        <div class="step-panel active" id="panel-1">
            <h5 class="fw-bold mb-1"><i class="bi bi-cpu me-2 text-primary"></i>Verificação de Requisitos</h5>
            <p class="text-muted small mb-3">Verificando se o servidor atende aos requisitos mínimos do sistema.</p>
            <div id="req-list"><div class="text-center py-4"><div class="spinner-border text-primary"></div><p class="mt-2 text-muted">Verificando...</p></div></div>
            <div class="d-flex justify-content-end mt-3">
                <button class="btn btn-primary" id="btn-req-next" disabled onclick="goStep(2)">
                    Próximo <i class="bi bi-arrow-right ms-1"></i>
                </button>
            </div>
        </div>

        <!-- STEP 2: Permissões -->
        <div class="step-panel" id="panel-2">
            <h5 class="fw-bold mb-1"><i class="bi bi-folder-check me-2 text-primary"></i>Permissões de Pastas</h5>
            <p class="text-muted small mb-3">Verificando permissões de escrita nas pastas necessárias.</p>
            <div id="perm-list"><div class="text-center py-4"><div class="spinner-border text-primary"></div><p class="mt-2 text-muted">Verificando...</p></div></div>
            <div class="d-flex justify-content-between mt-3">
                <button class="btn btn-outline-secondary" onclick="goStep(1)"><i class="bi bi-arrow-left me-1"></i>Anterior</button>
                <button class="btn btn-primary" id="btn-perm-next" disabled onclick="goStep(3)">Próximo <i class="bi bi-arrow-right ms-1"></i></button>
            </div>
        </div>

        <!-- STEP 3: Banco de Dados -->
        <div class="step-panel" id="panel-3">
            <h5 class="fw-bold mb-1"><i class="bi bi-database me-2 text-primary"></i>Configuração do Banco de Dados</h5>
            <p class="text-muted small mb-3">Informe os dados de conexão com o banco MySQL/MariaDB.</p>
            <form id="form-banco" novalidate>
                <div class="row g-3">
                    <div class="col-md-8">
                        <label class="form-label fw-medium small">Host</label>
                        <input type="text" name="db_host" class="form-control" value="127.0.0.1" required>
                    </div>
                    <div class="col-md-4">
                        <label class="form-label fw-medium small">Porta</label>
                        <input type="number" name="db_port" class="form-control" value="3306" required>
                    </div>
                    <div class="col-md-12">
                        <label class="form-label fw-medium small">Nome do Banco</label>
                        <input type="text" name="db_database" class="form-control" placeholder="gestor_financeiro" required>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label fw-medium small">Usuário</label>
                        <input type="text" name="db_username" class="form-control" placeholder="root" required>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label fw-medium small">Senha</label>
                        <input type="password" name="db_password" class="form-control" placeholder="(deixe em branco se não houver)">
                    </div>
                </div>
                <div id="banco-result" class="mt-3"></div>
                <div class="d-flex justify-content-between mt-3">
                    <button type="button" class="btn btn-outline-secondary" onclick="goStep(2)"><i class="bi bi-arrow-left me-1"></i>Anterior</button>
                    <div class="d-flex gap-2">
                        <button type="button" class="btn btn-outline-primary" id="btn-testar-banco" onclick="testarBanco()">
                            <i class="bi bi-plug me-1"></i>Testar Conexão
                        </button>
                        <button type="button" class="btn btn-primary" id="btn-salvar-banco" disabled onclick="salvarBanco()">
                            Salvar e Continuar <i class="bi bi-arrow-right ms-1"></i>
                        </button>
                    </div>
                </div>
            </form>
        </div>

        <!-- STEP 4: Migrations -->
        <div class="step-panel" id="panel-4">
            <h5 class="fw-bold mb-1"><i class="bi bi-table me-2 text-primary"></i>Criação das Tabelas</h5>
            <p class="text-muted small mb-3">Executando as migrations para criar a estrutura do banco de dados.</p>
            <div id="migration-result"></div>
            <div class="d-flex justify-content-between mt-3">
                <button class="btn btn-outline-secondary" onclick="goStep(3)"><i class="bi bi-arrow-left me-1"></i>Anterior</button>
                <button class="btn btn-primary" id="btn-migrations" onclick="executarMigrations()">
                    <i class="bi bi-play-circle me-1"></i>Executar Migrations
                </button>
            </div>
        </div>

        <!-- STEP 5: Superadmin -->
        <div class="step-panel" id="panel-5">
            <h5 class="fw-bold mb-1"><i class="bi bi-person-badge me-2 text-primary"></i>Criar Administrador</h5>
            <p class="text-muted small mb-3">Crie o usuário superadministrador do sistema.</p>
            <form id="form-admin" novalidate>
                <div class="row g-3">
                    <div class="col-12">
                        <label class="form-label fw-medium small">Nome Completo</label>
                        <input type="text" name="name" class="form-control" placeholder="Administrador" required>
                    </div>
                    <div class="col-12">
                        <label class="form-label fw-medium small">E-mail</label>
                        <input type="email" name="email" class="form-control" placeholder="admin@empresa.com" required>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label fw-medium small">Senha</label>
                        <input type="password" name="password" class="form-control" placeholder="Mínimo 8 caracteres" required minlength="8">
                    </div>
                    <div class="col-md-6">
                        <label class="form-label fw-medium small">Confirmar Senha</label>
                        <input type="password" name="password_confirmation" class="form-control" placeholder="Repita a senha" required>
                    </div>
                </div>
                <div id="admin-result" class="mt-3"></div>
                <div class="d-flex justify-content-between mt-3">
                    <button type="button" class="btn btn-outline-secondary" onclick="goStep(4)"><i class="bi bi-arrow-left me-1"></i>Anterior</button>
                    <button type="button" class="btn btn-primary" onclick="criarAdmin()">
                        Criar Administrador <i class="bi bi-arrow-right ms-1"></i>
                    </button>
                </div>
            </form>
        </div>

        <!-- STEP 6: Configurações -->
        <div class="step-panel" id="panel-6">
            <h5 class="fw-bold mb-1"><i class="bi bi-gear me-2 text-primary"></i>Configurações do Sistema</h5>
            <p class="text-muted small mb-3">Configure as informações básicas do sistema.</p>
            <form id="form-config" novalidate>
                <div class="row g-3">
                    <div class="col-md-6">
                        <label class="form-label fw-medium small">Nome do Sistema</label>
                        <input type="text" name="sistema_nome" class="form-control" value="FinanceiroSaaS" required>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label fw-medium small">Proprietário / Empresa</label>
                        <input type="text" name="sistema_proprietario" class="form-control" placeholder="Nome da empresa" required>
                    </div>
                    <div class="col-12">
                        <label class="form-label fw-medium small">Descrição (opcional)</label>
                        <input type="text" name="sistema_descricao" class="form-control" placeholder="Sistema de gestão financeira">
                    </div>
                    <div class="col-12"><hr><h6 class="fw-semibold text-muted">E-mail / SMTP (opcional)</h6></div>
                    <div class="col-md-8">
                        <label class="form-label fw-medium small">Servidor SMTP</label>
                        <input type="text" name="smtp_host" class="form-control" placeholder="smtp.gmail.com">
                    </div>
                    <div class="col-md-4">
                        <label class="form-label fw-medium small">Porta</label>
                        <input type="number" name="smtp_porta" class="form-control" value="587">
                    </div>
                    <div class="col-md-6">
                        <label class="form-label fw-medium small">Usuário SMTP</label>
                        <input type="text" name="smtp_usuario" class="form-control" placeholder="seu@email.com">
                    </div>
                    <div class="col-md-6">
                        <label class="form-label fw-medium small">Senha SMTP</label>
                        <input type="password" name="smtp_senha" class="form-control">
                    </div>
                    <div class="col-12">
                        <label class="form-label fw-medium small">E-mail Remetente</label>
                        <input type="email" name="smtp_remetente" class="form-control" placeholder="noreply@empresa.com">
                    </div>
                </div>
                <div id="config-result" class="mt-3"></div>
                <div class="d-flex justify-content-between mt-3">
                    <button type="button" class="btn btn-outline-secondary" onclick="goStep(5)"><i class="bi bi-arrow-left me-1"></i>Anterior</button>
                    <button type="button" class="btn btn-primary" onclick="salvarConfig()">
                        Salvar e Continuar <i class="bi bi-arrow-right ms-1"></i>
                    </button>
                </div>
            </form>
        </div>

        <!-- STEP 7: Finalizar -->
        <div class="step-panel" id="panel-7">
            <div class="text-center py-3">
                <div id="final-icon" style="font-size:4rem;">🚀</div>
                <h4 class="fw-bold mt-2" id="final-title">Tudo pronto para finalizar!</h4>
                <p class="text-muted" id="final-desc">Clique no botão abaixo para concluir a instalação e acessar o sistema.</p>
                <div id="final-result" class="mb-3"></div>
                <button class="btn btn-success btn-lg px-5" id="btn-finalizar" onclick="finalizar()">
                    <i class="bi bi-check-circle me-2"></i>Concluir Instalação
                </button>
            </div>
        </div>

    </div><!-- /card-body -->

    <!-- Footer -->
    <div class="card-footer bg-light text-center py-3">
        <small class="text-muted">FinanceiroSaaS &copy; {{ date('Y') }} — Instalador v1.0</small>
    </div>

</div><!-- /card -->
</div>
</div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
<script>
const CSRF = document.querySelector('meta[name="csrf-token"]').content;
let currentStep = 1;
const totalSteps = 7;
const pcts = [14,28,43,57,71,86,100];

function goStep(n) {
    document.getElementById('panel-' + currentStep).classList.remove('active');
    document.querySelectorAll('.step-item').forEach(el => el.classList.remove('active'));
    if (n > currentStep) {
        document.querySelector('[data-step="' + currentStep + '"]').classList.add('done');
    }
    currentStep = n;
    document.getElementById('panel-' + n).classList.add('active');
    document.querySelector('[data-step="' + n + '"]').classList.add('active');
    document.getElementById('progress-bar').style.width = pcts[n-1] + '%';
    document.getElementById('progress-label').textContent = 'Etapa ' + n + ' de ' + totalSteps;
    document.getElementById('progress-pct').textContent = pcts[n-1] + '%';
    if (n === 2) carregarPermissoes();
}

function alertBox(msg, tipo) {
    const cls = tipo === 'success' ? 'alert-success' : (tipo === 'danger' ? 'alert-danger' : 'alert-warning');
    return `<div class="alert ${cls} py-2 small mb-0">${msg}</div>`;
}

// STEP 1 — Requisitos
fetch('/instalar/requisitos')
    .then(r => r.json())
    .then(data => {
        let html = '';
        data.requisitos.forEach(r => {
            html += `<div class="req-item ${r.ok ? 'req-ok' : 'req-fail'} d-flex justify-content-between align-items-center">
                <span><i class="bi bi-${r.ok ? 'check-circle-fill text-success' : 'x-circle-fill text-danger'} me-2"></i>${r.nome}</span>
                <span class="badge ${r.ok ? 'bg-success' : 'bg-danger'}">${r.valor}</span>
            </div>`;
        });
        document.getElementById('req-list').innerHTML = html;
        if (data.tudo_ok) {
            document.getElementById('btn-req-next').disabled = false;
        } else {
            document.getElementById('req-list').innerHTML += alertBox('⚠️ Alguns requisitos não foram atendidos. Corrija antes de continuar.', 'warning');
        }
    })
    .catch(() => {
        document.getElementById('req-list').innerHTML = alertBox('Erro ao verificar requisitos.', 'danger');
    });

// STEP 2 — Permissões
function carregarPermissoes() {
    fetch('/instalar/permissoes')
        .then(r => r.json())
        .then(data => {
            let html = '';
            data.pastas.forEach(p => {
                html += `<div class="req-item ${p.ok ? 'req-ok' : 'req-fail'} d-flex justify-content-between align-items-center">
                    <span><i class="bi bi-${p.ok ? 'check-circle-fill text-success' : 'x-circle-fill text-danger'} me-2"></i>${p.pasta}</span>
                    <span class="badge ${p.ok ? 'bg-success' : 'bg-danger'}">${p.ok ? 'Gravável' : 'Sem permissão'}</span>
                </div>`;
            });
            document.getElementById('perm-list').innerHTML = html;
            if (data.tudo_ok) {
                document.getElementById('btn-perm-next').disabled = false;
            } else {
                document.getElementById('perm-list').innerHTML += alertBox('⚠️ Corrija as permissões das pastas antes de continuar (chmod 775).', 'warning');
                document.getElementById('btn-perm-next').disabled = false; // permite continuar mesmo assim
            }
        })
        .catch(() => {
            document.getElementById('perm-list').innerHTML = alertBox('Erro ao verificar permissões.', 'danger');
        });
}

// STEP 3 — Banco
function testarBanco() {
    const btn = document.getElementById('btn-testar-banco');
    btn.disabled = true; btn.innerHTML = '<span class="spinner-border spinner-border-sm me-1"></span>Testando...';
    const fd = new FormData(document.getElementById('form-banco'));
    fetch('/instalar/testar-banco', { method:'POST', headers:{'X-CSRF-TOKEN':CSRF,'Accept':'application/json'}, body: new URLSearchParams(fd) })
        .then(r => r.json())
        .then(data => {
            document.getElementById('banco-result').innerHTML = alertBox(data.mensagem + (data.versao ? ' (MySQL ' + data.versao + ')' : ''), data.sucesso ? 'success' : 'danger');
            if (data.sucesso) document.getElementById('btn-salvar-banco').disabled = false;
        })
        .catch(() => { document.getElementById('banco-result').innerHTML = alertBox('Erro de conexão.', 'danger'); })
        .finally(() => { btn.disabled = false; btn.innerHTML = '<i class="bi bi-plug me-1"></i>Testar Conexão'; });
}

function salvarBanco() {
    const btn = document.getElementById('btn-salvar-banco');
    btn.disabled = true; btn.innerHTML = '<span class="spinner-border spinner-border-sm me-1"></span>Salvando...';
    const fd = new FormData(document.getElementById('form-banco'));
    fetch('/instalar/salvar-banco', { method:'POST', headers:{'X-CSRF-TOKEN':CSRF,'Accept':'application/json'}, body: new URLSearchParams(fd) })
        .then(r => r.json())
        .then(data => {
            if (data.sucesso) { goStep(4); }
            else { document.getElementById('banco-result').innerHTML = alertBox(data.mensagem, 'danger'); btn.disabled = false; btn.innerHTML = 'Salvar e Continuar <i class="bi bi-arrow-right ms-1"></i>'; }
        })
        .catch(() => { document.getElementById('banco-result').innerHTML = alertBox('Erro ao salvar.', 'danger'); btn.disabled = false; });
}

// STEP 4 — Migrations
function executarMigrations() {
    const btn = document.getElementById('btn-migrations');
    btn.disabled = true; btn.innerHTML = '<span class="spinner-border spinner-border-sm me-1"></span>Executando...';
    document.getElementById('migration-result').innerHTML = '<div class="alert alert-info py-2 small">Executando migrations, aguarde...</div>';
    fetch('/instalar/migrations', { method:'POST', headers:{'X-CSRF-TOKEN':CSRF,'Accept':'application/json'} })
        .then(r => r.json())
        .then(data => {
            document.getElementById('migration-result').innerHTML = alertBox(data.mensagem, data.sucesso ? 'success' : 'danger');
            if (data.sucesso) {
                btn.innerHTML = '<i class="bi bi-check-circle me-1"></i>Concluído';
                setTimeout(() => goStep(5), 1200);
            } else { btn.disabled = false; btn.innerHTML = '<i class="bi bi-play-circle me-1"></i>Tentar Novamente'; }
        })
        .catch(() => { document.getElementById('migration-result').innerHTML = alertBox('Erro ao executar migrations.', 'danger'); btn.disabled = false; });
}

// STEP 5 — Admin
function criarAdmin() {
    const form = document.getElementById('form-admin');
    const fd = new FormData(form);
    if (fd.get('password') !== fd.get('password_confirmation')) {
        document.getElementById('admin-result').innerHTML = alertBox('As senhas não coincidem.', 'danger'); return;
    }
    fetch('/instalar/superadmin', { method:'POST', headers:{'X-CSRF-TOKEN':CSRF,'Accept':'application/json'}, body: new URLSearchParams(fd) })
        .then(r => r.json())
        .then(data => {
            document.getElementById('admin-result').innerHTML = alertBox(data.mensagem, data.sucesso ? 'success' : 'danger');
            if (data.sucesso) setTimeout(() => goStep(6), 1000);
        })
        .catch(() => { document.getElementById('admin-result').innerHTML = alertBox('Erro ao criar administrador.', 'danger'); });
}

// STEP 6 — Config
function salvarConfig() {
    const fd = new FormData(document.getElementById('form-config'));
    fetch('/instalar/configuracao', { method:'POST', headers:{'X-CSRF-TOKEN':CSRF,'Accept':'application/json'}, body: new URLSearchParams(fd) })
        .then(r => r.json())
        .then(data => {
            document.getElementById('config-result').innerHTML = alertBox(data.mensagem, data.sucesso ? 'success' : 'danger');
            if (data.sucesso) setTimeout(() => goStep(7), 1000);
        })
        .catch(() => { document.getElementById('config-result').innerHTML = alertBox('Erro ao salvar configurações.', 'danger'); });
}

// STEP 7 — Finalizar
function finalizar() {
    const btn = document.getElementById('btn-finalizar');
    btn.disabled = true; btn.innerHTML = '<span class="spinner-border spinner-border-sm me-2"></span>Finalizando...';
    fetch('/instalar/finalizar', { method:'POST', headers:{'X-CSRF-TOKEN':CSRF,'Accept':'application/json'} })
        .then(r => r.json())
        .then(data => {
            if (data.sucesso) {
                document.getElementById('final-icon').textContent = '✅';
                document.getElementById('final-title').textContent = 'Instalação concluída!';
                document.getElementById('final-desc').textContent = 'Redirecionando para o painel...';
                document.getElementById('final-result').innerHTML = alertBox(data.mensagem, 'success');
                setTimeout(() => { window.location.href = data.redirect || '/admin/dashboard'; }, 2000);
            } else {
                document.getElementById('final-result').innerHTML = alertBox(data.mensagem, 'danger');
                btn.disabled = false; btn.innerHTML = '<i class="bi bi-check-circle me-2"></i>Tentar Novamente';
            }
        })
        .catch(() => { document.getElementById('final-result').innerHTML = alertBox('Erro ao finalizar.', 'danger'); btn.disabled = false; });
}
</script>
</body>
</html>
