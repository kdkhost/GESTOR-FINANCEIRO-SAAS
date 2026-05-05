@php
    $usuario = auth()->user();
    $saudacao = now()->hour < 12 ? 'Bom dia' : (now()->hour < 18 ? 'Boa tarde' : 'Boa noite');
    $primeiroNome = explode(' ', (string) ($usuario?->name ?? 'Administrador'))[0] ?? 'Administrador';
@endphp

<nav class="app-header navbar navbar-expand premium-navbar">
    <div class="container-fluid">
        <ul class="navbar-nav align-items-center">
            <li class="nav-item">
                <a class="nav-link nav-icon-button" data-lte-toggle="sidebar" href="#" role="button" aria-label="Alternar menu">
                    <i class="bi bi-list fs-4"></i>
                </a>
            </li>
            <li class="nav-item d-none d-md-flex flex-column justify-content-center ms-2">
                <span class="navbar-welcome">{{ $saudacao }}, {{ $primeiroNome }}</span>
                <small class="navbar-meta">{{ now()->format('d/m/Y') }} | {{ configuracao('sistema_nome', 'FinanceiroSaaS') }}</small>
            </li>
        </ul>

        <ul class="navbar-nav ms-auto align-items-center">
            @if(auth()->check() && auth()->user()->is_admin)
            <li class="nav-item d-none d-lg-block">
                <a class="nav-link quick-link" href="{{ route('admin.configuracoes.index') }}">
                    <i class="bi bi-sliders2 me-2"></i>Configuracoes
                </a>
            </li>
            @endif

            <li class="nav-item dropdown">
                <a class="nav-link nav-icon-button position-relative" data-bs-toggle="dropdown" href="#" id="notificacoes-btn" aria-label="Notificacoes">
                    <i class="bi bi-bell fs-5"></i>
                    <span class="badge text-bg-danger navbar-badge" id="notificacoes-count" style="display:none;"></span>
                </a>
                <div class="dropdown-menu dropdown-menu-end premium-dropdown" style="min-width: 340px;" id="notificacoes-lista">
                    <div class="dropdown-header border-bottom pb-2">
                        <strong>Notificacoes</strong>
                    </div>
                    <div class="text-center text-muted py-4 small" id="notificacoes-vazio">
                        <i class="bi bi-bell-slash me-1"></i>Nenhuma notificacao no momento
                    </div>
                </div>
            </li>

            <li class="nav-item">
                <a class="nav-link nav-icon-button" href="#" id="toggle-tema" title="Alternar tema">
                    <i class="bi bi-moon-stars-fill" id="icone-tema"></i>
                </a>
            </li>

            <li class="nav-item dropdown ms-2">
                <a class="nav-link d-flex align-items-center gap-2 premium-user-menu" data-bs-toggle="dropdown" href="#" id="menu-usuario">
                    <span class="premium-user-avatar">
                        @if($usuario?->avatar_url)
                            <img src="{{ $usuario->avatar_url }}" alt="Avatar" width="38" height="38" class="rounded-circle" style="object-fit: cover;">
                        @else
                            {{ strtoupper(substr((string) ($usuario?->name ?? 'FS'), 0, 2)) }}
                        @endif
                    </span>
                    <span class="d-none d-md-flex flex-column text-start">
                        <strong class="premium-user-name">{{ $usuario?->name }}</strong>
                        <small class="premium-user-role">{{ strtoupper((string) ($usuario?->tipo ?? 'usuario')) }}</small>
                    </span>
                    <i class="bi bi-chevron-down small"></i>
                </a>
                <ul class="dropdown-menu dropdown-menu-end premium-dropdown">
                    <li class="px-3 py-2 border-bottom">
                        <strong class="d-block">{{ $usuario?->name }}</strong>
                        <small class="text-muted">{{ $usuario?->email }}</small>
                    </li>
                    <li>
                        <a class="dropdown-item" href="{{ route('admin.perfil') }}">
                            <i class="bi bi-person-circle me-2"></i>Meu Perfil
                        </a>
                    </li>
                    @if(auth()->check() && auth()->user()->is_admin)
                    <li>
                        <a class="dropdown-item" href="{{ route('admin.configuracoes.index') }}">
                            <i class="bi bi-gear me-2"></i>Configuracoes
                        </a>
                    </li>
                    @endif
                    <li><hr class="dropdown-divider"></li>
                    <li>
                        <form method="POST" action="{{ route('auth.logout') }}" id="form-logout">
                            @csrf
                            <a class="dropdown-item text-danger" href="#"
                               onclick="event.preventDefault(); SistemaAlert.fire({title:'Sair do sistema?',icon:'question',showCancelButton:true,confirmButtonText:'Sim, sair',cancelButtonText:'Cancelar'}).then(r=>{if(r.isConfirmed)document.getElementById('form-logout').submit()})">
                                <i class="bi bi-box-arrow-right me-2"></i>Sair
                            </a>
                        </form>
                    </li>
                </ul>
            </li>
        </ul>
    </div>
</nav>

<script>
document.getElementById('toggle-tema')?.addEventListener('click', function (e) {
    e.preventDefault();
    const html = document.documentElement;
    const atual = html.getAttribute('data-bs-theme');
    const novo = atual === 'dark' ? 'light' : 'dark';
    html.setAttribute('data-bs-theme', novo);
    document.getElementById('icone-tema').className = novo === 'dark' ? 'bi bi-sun-fill' : 'bi bi-moon-stars-fill';
    localStorage.setItem('tema-saas', novo);
});

const temaSalvo = localStorage.getItem('tema-saas');
if (temaSalvo) {
    document.documentElement.setAttribute('data-bs-theme', temaSalvo);
    const icone = document.getElementById('icone-tema');
    if (icone) {
        icone.className = temaSalvo === 'dark' ? 'bi bi-sun-fill' : 'bi bi-moon-stars-fill';
    }
}

function atualizarNotificacoes() {
    $.getJSON('{{ route("admin.notificacoes.nao-lidas") }}', function (r) {
        if (r.total > 0) {
            $('#notificacoes-count').text(r.total).show();
            $('#notificacoes-vazio').hide();
            let html = '<div class="dropdown-header border-bottom pb-2"><strong>Notificacoes</strong></div>';
            r.itens.forEach(function (n) {
                html += `<a class="dropdown-item py-3" href="${n.url || '#'}">
                    <div class="d-flex gap-2 align-items-start">
                        <i class="bi bi-bell-fill text-warning mt-1"></i>
                        <div>
                            <span class="fw-semibold d-block">${n.titulo}</span>
                            <small class="text-muted">${n.criado_em}</small>
                        </div>
                    </div>
                </a>`;
            });
            $('#notificacoes-lista').html(html);
        } else {
            $('#notificacoes-count').hide();
        }
    }).fail(function () {});
}

setInterval(atualizarNotificacoes, 60000);
atualizarNotificacoes();
</script>
