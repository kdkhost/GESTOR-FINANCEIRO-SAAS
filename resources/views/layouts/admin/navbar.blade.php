<nav class="app-header navbar navbar-expand bg-body-tertiary shadow">
    <div class="container-fluid">

        {{-- Toggle sidebar --}}
        <ul class="navbar-nav">
            <li class="nav-item">
                <a class="nav-link" data-lte-toggle="sidebar" href="#" role="button">
                    <i class="bi bi-list fs-4"></i>
                </a>
            </li>
        </ul>

        {{-- Spacer --}}
        <div class="collapse navbar-collapse d-flex">
            {{-- Período rápido --}}
            <ul class="navbar-nav me-auto">
                <li class="nav-item d-none d-md-block">
                    <span class="nav-link text-muted small">
                        <i class="bi bi-calendar3 me-1"></i>{{ now()->format('d/m/Y') }}
                    </span>
                </li>
            </ul>

            {{-- Itens direita --}}
            <ul class="navbar-nav ms-auto">

                {{-- Notificações --}}
                <li class="nav-item dropdown">
                    <a class="nav-link" data-bs-toggle="dropdown" href="#" id="notificacoes-btn">
                        <i class="bi bi-bell fs-5"></i>
                        <span class="badge bg-danger navbar-badge" id="notificacoes-count" style="display:none;"></span>
                    </a>
                    <div class="dropdown-menu dropdown-menu-end" style="min-width:320px;" id="notificacoes-lista">
                        <span class="dropdown-header">Notificações</span>
                        <div class="text-center text-muted py-3 small" id="notificacoes-vazio">
                            <i class="bi bi-bell-slash me-1"></i>Nenhuma notificação
                        </div>
                    </div>
                </li>

                {{-- Tema dark/light --}}
                <li class="nav-item">
                    <a class="nav-link" href="#" id="toggle-tema" title="Alternar tema">
                        <i class="bi bi-moon-stars-fill" id="icone-tema"></i>
                    </a>
                </li>

                {{-- Usuário --}}
                <li class="nav-item dropdown">
                    <a class="nav-link d-flex align-items-center gap-2" data-bs-toggle="dropdown" href="#" id="menu-usuario">
                        <img src="{{ auth()->user()?->avatar_url }}" alt="Avatar"
                             class="rounded-circle" width="32" height="32" style="object-fit:cover;">
                        <span class="d-none d-md-inline fw-medium">{{ auth()->user()?->name }}</span>
                        <i class="bi bi-chevron-down small"></i>
                    </a>
                    <ul class="dropdown-menu dropdown-menu-end">
                        <li>
                            <a class="dropdown-item" href="{{ route('admin.perfil') }}">
                                <i class="bi bi-person-circle me-2"></i>Meu Perfil
                            </a>
                        </li>
                        <li>
                            <a class="dropdown-item" href="{{ route('admin.configuracoes.index') }}">
                                <i class="bi bi-gear me-2"></i>Configurações
                            </a>
                        </li>
                        <li><hr class="dropdown-divider"></li>
                        <li>
                            <form method="POST" action="{{ route('auth.logout') }}" id="form-logout">
                                @csrf
                                <a class="dropdown-item text-danger" href="#"
                                   onclick="event.preventDefault();
                                   SistemaAlert.fire({title:'Sair do sistema?',icon:'question',showCancelButton:true,confirmButtonText:'Sim, sair',cancelButtonText:'Cancelar'}).then(r=>{if(r.isConfirmed)document.getElementById('form-logout').submit()})">
                                    <i class="bi bi-box-arrow-right me-2"></i>Sair
                                </a>
                            </form>
                        </li>
                    </ul>
                </li>

            </ul>
        </div>
    </div>
</nav>

<script>
// Toggle tema dark/light
document.getElementById('toggle-tema')?.addEventListener('click', function(e) {
    e.preventDefault();
    const html = document.documentElement;
    const atual = html.getAttribute('data-bs-theme');
    const novo  = atual === 'dark' ? 'light' : 'dark';
    html.setAttribute('data-bs-theme', novo);
    document.getElementById('icone-tema').className = novo === 'dark' ? 'bi bi-sun-fill' : 'bi bi-moon-stars-fill';
    localStorage.setItem('tema-saas', novo);
});

// Restaurar tema salvo
const temaSalvo = localStorage.getItem('tema-saas');
if (temaSalvo) {
    document.documentElement.setAttribute('data-bs-theme', temaSalvo);
    const icone = document.getElementById('icone-tema');
    if (icone) icone.className = temaSalvo === 'dark' ? 'bi bi-sun-fill' : 'bi bi-moon-stars-fill';
}

// Polling de notificações a cada 60s
function atualizarNotificacoes() {
    $.getJSON('{{ route("admin.notificacoes.nao-lidas") }}', function(r) {
        if (r.total > 0) {
            $('#notificacoes-count').text(r.total).show();
            $('#notificacoes-vazio').hide();
            let html = '';
            r.itens.forEach(function(n) {
                html += `<a class="dropdown-item" href="${n.url || '#'}">
                    <i class="bi bi-bell-fill text-warning me-2"></i>
                    <span class="fw-medium">${n.titulo}</span>
                    <br><small class="text-muted">${n.criado_em}</small>
                </a><li><hr class="dropdown-divider"></li>`;
            });
            $('#notificacoes-lista').html('<span class="dropdown-header">Notificações</span>' + html);
        } else {
            $('#notificacoes-count').hide();
        }
    }).fail(function() {});
}
setInterval(atualizarNotificacoes, 60000);
atualizarNotificacoes();
</script>
