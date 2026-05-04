<aside class="app-sidebar bg-body-secondary shadow" data-bs-theme="dark">

    {{-- Logo --}}
    <div class="sidebar-brand">
        <a href="{{ route('admin.dashboard.index') }}" class="brand-link">
            @if(configuracao('sistema_logo'))
                <img src="{{ asset('storage/' . configuracao('sistema_logo')) }}" alt="Logo" class="brand-image" style="max-height:36px;">
            @else
                <i class="bi bi-graph-up-arrow brand-image" style="font-size:2rem;color:#3b82f6;"></i>
            @endif
            <span class="brand-text fw-semibold ms-2">
                {{ configuracao('sistema_nome', 'FinanceiroSaaS') }}
            </span>
        </a>
    </div>

    {{-- Menu --}}
    <div class="sidebar-wrapper">
        <nav class="mt-2">
            <ul class="nav sidebar-menu flex-column" data-lte-toggle="treeview" role="menu">

                {{-- Dashboard --}}
                <li class="nav-item">
                    <a href="{{ route('admin.dashboard.index') }}" class="nav-link {{ request()->routeIs('admin.dashboard.*') ? 'active' : '' }}">
                        <i class="nav-icon bi bi-speedometer2"></i>
                        <p>Dashboard</p>
                    </a>
                </li>

                {{-- Financeiro --}}
                <li class="nav-item {{ request()->routeIs('admin.contas-pagar.*','admin.contas-receber.*','admin.receitas.*','admin.despesas.*','admin.transferencias.*','admin.recorrencias.*') ? 'menu-open' : '' }}">
                    <a href="#" class="nav-link {{ request()->routeIs('admin.contas-pagar.*','admin.contas-receber.*','admin.receitas.*','admin.despesas.*','admin.transferencias.*','admin.recorrencias.*') ? 'active' : '' }}">
                        <i class="nav-icon bi bi-currency-dollar"></i>
                        <p>Financeiro <i class="nav-arrow bi bi-chevron-right"></i></p>
                    </a>
                    <ul class="nav nav-treeview">
                        <li class="nav-item">
                            <a href="{{ route('admin.contas-pagar.index') }}" class="nav-link {{ request()->routeIs('admin.contas-pagar.*') ? 'active' : '' }}">
                                <i class="nav-icon bi bi-arrow-up-circle text-danger"></i>
                                <p>Contas a Pagar</p>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a href="{{ route('admin.contas-receber.index') }}" class="nav-link {{ request()->routeIs('admin.contas-receber.*') ? 'active' : '' }}">
                                <i class="nav-icon bi bi-arrow-down-circle text-success"></i>
                                <p>Contas a Receber</p>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a href="{{ route('admin.receitas.index') }}" class="nav-link {{ request()->routeIs('admin.receitas.*') ? 'active' : '' }}">
                                <i class="nav-icon bi bi-plus-circle text-success"></i>
                                <p>Receitas</p>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a href="{{ route('admin.despesas.index') }}" class="nav-link {{ request()->routeIs('admin.despesas.*') ? 'active' : '' }}">
                                <i class="nav-icon bi bi-dash-circle text-danger"></i>
                                <p>Despesas</p>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a href="{{ route('admin.transferencias.index') }}" class="nav-link {{ request()->routeIs('admin.transferencias.*') ? 'active' : '' }}">
                                <i class="nav-icon bi bi-arrow-left-right text-info"></i>
                                <p>Transferências</p>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a href="{{ route('admin.recorrencias.index') }}" class="nav-link {{ request()->routeIs('admin.recorrencias.*') ? 'active' : '' }}">
                                <i class="nav-icon bi bi-repeat text-warning"></i>
                                <p>Recorrências</p>
                            </a>
                        </li>
                    </ul>
                </li>

                {{-- Planejamento --}}
                <li class="nav-item {{ request()->routeIs('admin.metas.*','admin.orcamentos.*') ? 'menu-open' : '' }}">
                    <a href="#" class="nav-link">
                        <i class="nav-icon bi bi-bullseye"></i>
                        <p>Planejamento <i class="nav-arrow bi bi-chevron-right"></i></p>
                    </a>
                    <ul class="nav nav-treeview">
                        <li class="nav-item">
                            <a href="{{ route('admin.metas.index') }}" class="nav-link {{ request()->routeIs('admin.metas.*') ? 'active' : '' }}">
                                <i class="nav-icon bi bi-trophy text-warning"></i>
                                <p>Metas Financeiras</p>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a href="{{ route('admin.orcamentos.index') }}" class="nav-link {{ request()->routeIs('admin.orcamentos.*') ? 'active' : '' }}">
                                <i class="nav-icon bi bi-pie-chart text-info"></i>
                                <p>Orçamentos</p>
                            </a>
                        </li>
                    </ul>
                </li>

                {{-- Relatórios --}}
                <li class="nav-item {{ request()->routeIs('admin.relatorios.*') ? 'menu-open' : '' }}">
                    <a href="#" class="nav-link">
                        <i class="nav-icon bi bi-file-earmark-bar-graph"></i>
                        <p>Relatórios <i class="nav-arrow bi bi-chevron-right"></i></p>
                    </a>
                    <ul class="nav nav-treeview">
                        <li class="nav-item">
                            <a href="{{ route('admin.relatorios.fluxo-caixa') }}" class="nav-link">
                                <i class="nav-icon bi bi-water"></i><p>Fluxo de Caixa</p>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a href="{{ route('admin.relatorios.dre') }}" class="nav-link">
                                <i class="nav-icon bi bi-journal-text"></i><p>DRE</p>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a href="{{ route('admin.relatorios.saude-financeira') }}" class="nav-link">
                                <i class="nav-icon bi bi-heart-pulse"></i><p>Saúde Financeira</p>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a href="{{ route('admin.relatorios.evolucao') }}" class="nav-link">
                                <i class="nav-icon bi bi-graph-up"></i><p>Evolução Mensal</p>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a href="{{ route('admin.relatorios.inadimplencia') }}" class="nav-link">
                                <i class="nav-icon bi bi-exclamation-triangle text-danger"></i><p>Inadimplência</p>
                            </a>
                        </li>
                    </ul>
                </li>

                {{-- Cadastros --}}
                <li class="nav-item {{ request()->routeIs('admin.categorias.*','admin.contas-bancarias.*','admin.clientes.*','admin.fornecedores.*') ? 'menu-open' : '' }}">
                    <a href="#" class="nav-link">
                        <i class="nav-icon bi bi-folder2-open"></i>
                        <p>Cadastros <i class="nav-arrow bi bi-chevron-right"></i></p>
                    </a>
                    <ul class="nav nav-treeview">
                        <li class="nav-item">
                            <a href="{{ route('admin.categorias.index') }}" class="nav-link"><i class="nav-icon bi bi-tags"></i><p>Categorias</p></a>
                        </li>
                        <li class="nav-item">
                            <a href="{{ route('admin.contas-bancarias.index') }}" class="nav-link"><i class="nav-icon bi bi-bank"></i><p>Contas Bancárias</p></a>
                        </li>
                        <li class="nav-item">
                            <a href="{{ route('admin.clientes.index') }}" class="nav-link"><i class="nav-icon bi bi-people"></i><p>Clientes</p></a>
                        </li>
                        <li class="nav-item">
                            <a href="{{ route('admin.fornecedores.index') }}" class="nav-link"><i class="nav-icon bi bi-shop"></i><p>Fornecedores</p></a>
                        </li>
                    </ul>
                </li>

                @if(auth()->user()?->is_admin)
                {{-- Administração --}}
                <li class="nav-header">ADMINISTRAÇÃO</li>
                <li class="nav-item">
                    <a href="{{ route('admin.usuarios.index') }}" class="nav-link {{ request()->routeIs('admin.usuarios.*') ? 'active' : '' }}">
                        <i class="nav-icon bi bi-people-fill"></i><p>Usuários</p>
                    </a>
                </li>
                <li class="nav-item">
                    <a href="{{ route('admin.permissoes.index') }}" class="nav-link {{ request()->routeIs('admin.permissoes.*') ? 'active' : '' }}">
                        <i class="nav-icon bi bi-shield-check"></i><p>Permissões</p>
                    </a>
                </li>
                <li class="nav-item">
                    <a href="{{ route('admin.auditoria.index') }}" class="nav-link {{ request()->routeIs('admin.auditoria.*') ? 'active' : '' }}">
                        <i class="nav-icon bi bi-journal-check"></i><p>Auditoria</p>
                    </a>
                </li>
                <li class="nav-item">
                    <a href="{{ route('admin.cron.index') }}" class="nav-link {{ request()->routeIs('admin.cron.*') ? 'active' : '' }}">
                        <i class="nav-icon bi bi-clock-history"></i><p>Cron Jobs</p>
                    </a>
                </li>
                <li class="nav-item">
                    <a href="{{ route('admin.gateways.index') }}" class="nav-link {{ request()->routeIs('admin.gateways.*') ? 'active' : '' }}">
                        <i class="nav-icon bi bi-wallet2"></i><p>Gateways</p>
                    </a>
                </li>
                <li class="nav-item">
                    <a href="{{ route('admin.configuracoes.index') }}" class="nav-link {{ request()->routeIs('admin.configuracoes.*') ? 'active' : '' }}">
                        <i class="nav-icon bi bi-gear-fill"></i><p>Configurações</p>
                    </a>
                </li>
                @endif

            </ul>
        </nav>
    </div>
</aside>
