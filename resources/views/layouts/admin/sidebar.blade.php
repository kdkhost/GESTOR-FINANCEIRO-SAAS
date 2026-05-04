@php
    $usuario = auth()->user();
    $isAdmin = (bool) ($usuario?->is_admin ?? false) || in_array((string) ($usuario?->tipo ?? ''), ['admin', 'superadmin'], true);

    $menuPrincipal = [
        [
            'type' => 'link',
            'label' => 'Dashboard',
            'icon' => 'bi bi-speedometer2',
            'route' => 'admin.dashboard.index',
            'active' => ['admin.dashboard.*'],
        ],
        [
            'type' => 'tree',
            'label' => 'Financeiro',
            'icon' => 'bi bi-cash-stack',
            'active' => ['admin.contas-pagar.*', 'admin.contas-receber.*', 'admin.receitas.*', 'admin.despesas.*', 'admin.transferencias.*', 'admin.recorrencias.*'],
            'children' => [
                ['label' => 'Contas a Pagar', 'icon' => 'bi bi-arrow-up-circle', 'route' => 'admin.contas-pagar.index', 'active' => ['admin.contas-pagar.*'], 'icon_class' => 'text-danger'],
                ['label' => 'Contas a Receber', 'icon' => 'bi bi-arrow-down-circle', 'route' => 'admin.contas-receber.index', 'active' => ['admin.contas-receber.*'], 'icon_class' => 'text-success'],
                ['label' => 'Receitas', 'icon' => 'bi bi-plus-circle', 'route' => 'admin.receitas.index', 'active' => ['admin.receitas.*'], 'icon_class' => 'text-success'],
                ['label' => 'Despesas', 'icon' => 'bi bi-dash-circle', 'route' => 'admin.despesas.index', 'active' => ['admin.despesas.*'], 'icon_class' => 'text-danger'],
                ['label' => 'Transferencias', 'icon' => 'bi bi-arrow-left-right', 'route' => 'admin.transferencias.index', 'active' => ['admin.transferencias.*'], 'icon_class' => 'text-info'],
                ['label' => 'Recorrencias', 'icon' => 'bi bi-repeat', 'route' => 'admin.recorrencias.index', 'active' => ['admin.recorrencias.*'], 'icon_class' => 'text-warning'],
            ],
        ],
        [
            'type' => 'tree',
            'label' => 'Planejamento',
            'icon' => 'bi bi-bullseye',
            'active' => ['admin.metas.*', 'admin.orcamentos.*'],
            'children' => [
                ['label' => 'Metas Financeiras', 'icon' => 'bi bi-trophy', 'route' => 'admin.metas.index', 'active' => ['admin.metas.*'], 'icon_class' => 'text-warning'],
                ['label' => 'Orcamentos', 'icon' => 'bi bi-pie-chart', 'route' => 'admin.orcamentos.index', 'active' => ['admin.orcamentos.*'], 'icon_class' => 'text-info'],
            ],
        ],
        [
            'type' => 'tree',
            'label' => 'Cadastros',
            'icon' => 'bi bi-folder2-open',
            'active' => ['admin.categorias.*', 'admin.contas-bancarias.*', 'admin.clientes.*', 'admin.fornecedores.*'],
            'children' => [
                ['label' => 'Categorias', 'icon' => 'bi bi-tags', 'route' => 'admin.categorias.index', 'active' => ['admin.categorias.*']],
                ['label' => 'Contas Bancarias', 'icon' => 'bi bi-bank', 'route' => 'admin.contas-bancarias.index', 'active' => ['admin.contas-bancarias.*']],
                ['label' => 'Clientes', 'icon' => 'bi bi-people', 'route' => 'admin.clientes.index', 'active' => ['admin.clientes.*']],
                ['label' => 'Fornecedores', 'icon' => 'bi bi-shop', 'route' => 'admin.fornecedores.index', 'active' => ['admin.fornecedores.*']],
            ],
        ],
        [
            'type' => 'tree',
            'label' => 'Relatorios',
            'icon' => 'bi bi-file-earmark-bar-graph',
            'active' => ['admin.relatorios.*'],
            'children' => [
                ['label' => 'Fluxo de Caixa', 'icon' => 'bi bi-water', 'route' => 'admin.relatorios.fluxo-caixa', 'active' => ['admin.relatorios.fluxo-caixa']],
                ['label' => 'DRE', 'icon' => 'bi bi-journal-text', 'route' => 'admin.relatorios.dre', 'active' => ['admin.relatorios.dre']],
                ['label' => 'Saude Financeira', 'icon' => 'bi bi-heart-pulse', 'route' => 'admin.relatorios.saude-financeira', 'active' => ['admin.relatorios.saude-financeira']],
                ['label' => 'Evolucao Mensal', 'icon' => 'bi bi-graph-up', 'route' => 'admin.relatorios.evolucao', 'active' => ['admin.relatorios.evolucao']],
                ['label' => 'Inadimplencia', 'icon' => 'bi bi-exclamation-triangle', 'route' => 'admin.relatorios.inadimplencia', 'active' => ['admin.relatorios.inadimplencia'], 'icon_class' => 'text-danger'],
            ],
        ],
    ];

    $menuAdmin = [
        [
            'type' => 'tree',
            'label' => 'Usuarios e Acessos',
            'icon' => 'bi bi-people-fill',
            'active' => ['admin.usuarios.*', 'admin.permissoes.*'],
            'children' => [
                ['label' => 'Usuarios', 'icon' => 'bi bi-person-lines-fill', 'route' => 'admin.usuarios.index', 'active' => ['admin.usuarios.*']],
                ['label' => 'Permissoes', 'icon' => 'bi bi-shield-check', 'route' => 'admin.permissoes.index', 'active' => ['admin.permissoes.*']],
            ],
        ],
        [
            'type' => 'tree',
            'label' => 'Sistema',
            'icon' => 'bi bi-sliders2',
            'active' => ['admin.configuracoes.*', 'admin.modulos.*', 'admin.gateways.*', 'admin.notificacoes.*', 'admin.manutencao.*', 'admin.cron.*', 'admin.auditoria.*'],
            'children' => [
                ['label' => 'Configuracoes', 'icon' => 'bi bi-gear-fill', 'route' => 'admin.configuracoes.index', 'active' => ['admin.configuracoes.*']],
                ['label' => 'Modulos', 'icon' => 'bi bi-boxes', 'route' => 'admin.modulos.index', 'active' => ['admin.modulos.*']],
                ['label' => 'Gateways', 'icon' => 'bi bi-wallet2', 'route' => 'admin.gateways.index', 'active' => ['admin.gateways.*']],
                ['label' => 'Notificacoes', 'icon' => 'bi bi-bell-fill', 'route' => 'admin.notificacoes.templates.index', 'active' => ['admin.notificacoes.*']],
                ['label' => 'Cron Jobs', 'icon' => 'bi bi-clock-history', 'route' => 'admin.cron.index', 'active' => ['admin.cron.*']],
                ['label' => 'Auditoria', 'icon' => 'bi bi-journal-check', 'route' => 'admin.auditoria.index', 'active' => ['admin.auditoria.*']],
                ['label' => 'Manutencao', 'icon' => 'bi bi-cone-striped', 'route' => 'admin.manutencao.index', 'active' => ['admin.manutencao.*']],
            ],
        ],
        [
            'type' => 'tree',
            'label' => 'SaaS',
            'icon' => 'bi bi-grid-1x2-fill',
            'active' => ['admin.saas.*'],
            'children' => [
                ['label' => 'Planos', 'icon' => 'bi bi-layers', 'route' => 'admin.saas.planos.index', 'active' => ['admin.saas.planos.*']],
                ['label' => 'Empresas', 'icon' => 'bi bi-buildings', 'route' => 'admin.saas.empresas.index', 'active' => ['admin.saas.empresas.*']],
                ['label' => 'Assinaturas', 'icon' => 'bi bi-receipt', 'route' => 'admin.saas.assinaturas.index', 'active' => ['admin.saas.assinaturas.*']],
                ['label' => 'Faturas', 'icon' => 'bi bi-cash-coin', 'route' => 'admin.saas.faturas.index', 'active' => ['admin.saas.faturas.*']],
            ],
        ],
    ];
@endphp

<aside class="app-sidebar shadow premium-sidebar" data-bs-theme="dark">
    <div class="sidebar-brand">
        <a href="{{ route('admin.dashboard.index') }}" class="brand-link">
            <span class="brand-mark">
                @if(configuracao('sistema_logo'))
                    <img src="{{ asset('storage/' . configuracao('sistema_logo')) }}" alt="Logo" class="brand-image" style="max-height: 36px;">
                @else
                    <i class="bi bi-graph-up-arrow"></i>
                @endif
            </span>
            <span class="brand-copy">
                <span class="brand-title">{{ configuracao('sistema_nome', 'FinanceiroSaaS') }}</span>
                <span class="brand-subtitle">Painel administrativo</span>
            </span>
        </a>
    </div>

    <div class="sidebar-wrapper">
        <div class="sidebar-user-panel">
            <div class="sidebar-user-avatar">
                @if($usuario?->avatar_url)
                    <img src="{{ $usuario->avatar_url }}" alt="Avatar" class="img-fluid rounded-circle">
                @else
                    <span>{{ strtoupper(substr($usuario?->name ?? 'FS', 0, 2)) }}</span>
                @endif
            </div>
            <div class="sidebar-user-copy">
                <strong>{{ $usuario?->name ?? 'Usuario' }}</strong>
                <span>{{ $usuario?->email ?? 'sem-email' }}</span>
            </div>
            <span class="sidebar-user-badge">{{ strtoupper((string) ($usuario?->tipo ?? 'usuario')) }}</span>
        </div>

        <nav class="mt-2">
            <ul class="nav nav-pills nav-sidebar flex-column nav-compact" data-lte-toggle="treeview" data-accordion="false" role="menu">
                <li class="nav-header">OPERACAO</li>
                @foreach($menuPrincipal as $item)
                    @php
                        $ativo = request()->routeIs(...$item['active']);
                    @endphp
                    @if($item['type'] === 'link')
                        <li class="nav-item">
                            <a href="{{ route($item['route']) }}" class="nav-link {{ $ativo ? 'active' : '' }}">
                                <i class="nav-icon {{ $item['icon'] }}"></i>
                                <p>{{ $item['label'] }}</p>
                            </a>
                        </li>
                    @else
                        <li class="nav-item {{ $ativo ? 'menu-open' : '' }}">
                            <a href="#" class="nav-link {{ $ativo ? 'active' : '' }}">
                                <i class="nav-icon {{ $item['icon'] }}"></i>
                                <p>
                                    {{ $item['label'] }}
                                    <i class="nav-arrow bi bi-chevron-right"></i>
                                </p>
                            </a>
                            <ul class="nav nav-treeview">
                                @foreach($item['children'] as $child)
                                    <li class="nav-item">
                                        <a href="{{ route($child['route']) }}" class="nav-link {{ request()->routeIs(...$child['active']) ? 'active' : '' }}">
                                            <i class="nav-icon {{ $child['icon'] }} {{ $child['icon_class'] ?? '' }}"></i>
                                            <p>{{ $child['label'] }}</p>
                                        </a>
                                    </li>
                                @endforeach
                            </ul>
                        </li>
                    @endif
                @endforeach

                @if($isAdmin)
                    <li class="nav-header">ADMINISTRACAO</li>
                    @foreach($menuAdmin as $item)
                        @php
                            $ativo = request()->routeIs(...$item['active']);
                        @endphp
                        <li class="nav-item {{ $ativo ? 'menu-open' : '' }}">
                            <a href="#" class="nav-link {{ $ativo ? 'active' : '' }}">
                                <i class="nav-icon {{ $item['icon'] }}"></i>
                                <p>
                                    {{ $item['label'] }}
                                    <i class="nav-arrow bi bi-chevron-right"></i>
                                </p>
                            </a>
                            <ul class="nav nav-treeview">
                                @foreach($item['children'] as $child)
                                    <li class="nav-item">
                                        <a href="{{ route($child['route']) }}" class="nav-link {{ request()->routeIs(...$child['active']) ? 'active' : '' }}">
                                            <i class="nav-icon {{ $child['icon'] }}"></i>
                                            <p>{{ $child['label'] }}</p>
                                        </a>
                                    </li>
                                @endforeach
                            </ul>
                        </li>
                    @endforeach
                @endif

                <li class="nav-header">CONTA</li>
                <li class="nav-item">
                    <a href="{{ route('admin.perfil') }}" class="nav-link {{ request()->routeIs('admin.perfil*') ? 'active' : '' }}">
                        <i class="nav-icon bi bi-person-circle"></i>
                        <p>Meu Perfil</p>
                    </a>
                </li>
                <li class="nav-item">
                    <form method="POST" action="{{ route('auth.logout') }}" id="form-logout-sidebar">
                        @csrf
                        <a class="nav-link text-danger" href="#"
                           onclick="event.preventDefault(); SistemaAlert.fire({title:'Sair do sistema?',icon:'question',showCancelButton:true,confirmButtonText:'Sim, sair',cancelButtonText:'Cancelar'}).then(r=>{if(r.isConfirmed)document.getElementById('form-logout-sidebar').submit()})">
                            <i class="nav-icon bi bi-box-arrow-right"></i>
                            <p>Sair</p>
                        </a>
                    </form>
                </li>
            </ul>
        </nav>
    </div>
</aside>
