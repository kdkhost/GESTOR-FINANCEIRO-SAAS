<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <title>{{ config('app.name', 'Gestor Financeiro SaaS') }}</title>

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=inter:400,500,600,700" rel="stylesheet" />

    <!-- Tailwind CSS (via CDN para garantir funcionamento global na view, sem depender do build se nao gerado) -->
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            darkMode: 'class',
            theme: {
                extend: {
                    fontFamily: {
                        sans: ['Inter', 'sans-serif'],
                    },
                    colors: {
                        primary: '#3b82f6',
                        secondary: '#1e293b',
                        dark: '#0f172a'
                    }
                }
            }
        }
    </script>
</head>
@php
    $nomeSistema = configuracao('sistema_nome', 'FinanceiroSaaS');
    $descricaoSistema = configuracao('sistema_descricao', 'A plataforma SaaS definitiva para alavancar os resultados da sua empresa.');
    $proprietarioSistema = configuracao('sistema_proprietario', 'FinanceiroSaaS');
@endphp
<body class="antialiased bg-gray-50 text-gray-800 dark:bg-dark dark:text-gray-100 selection:bg-primary selection:text-white">

    <!-- Navbar -->
    <nav id="site-navbar" class="sticky top-0 w-full bg-white/95 dark:bg-slate-900/95 backdrop-blur shadow-sm border-b border-gray-200 dark:border-gray-800 z-50 transition-all duration-200">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between items-center h-20">
                <div class="flex items-center gap-2">
                    <!-- Logo Icon -->
                    <div class="w-10 h-10 bg-primary rounded-xl flex items-center justify-center shadow-lg shadow-blue-500/30">
                        <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                    </div>
                    <span class="font-bold text-xl tracking-tight text-gray-900 dark:text-white">{{ $nomeSistema }}</span>
                </div>
                
                <div class="hidden md:flex space-x-8">
                    <a href="#recursos" class="text-gray-600 hover:text-primary dark:text-gray-300 dark:hover:text-primary transition-colors font-medium">Recursos</a>
                    <a href="#planos" class="text-gray-600 hover:text-primary dark:text-gray-300 dark:hover:text-primary transition-colors font-medium">Planos</a>
                    <a href="#contato" class="text-gray-600 hover:text-primary dark:text-gray-300 dark:hover:text-primary transition-colors font-medium">Contato</a>
                </div>

                <div class="flex items-center space-x-4">
                    @if (file_exists(storage_path('installed')))
                        @auth
                            <a href="{{ url('/admin/dashboard') }}" class="font-medium text-gray-600 hover:text-primary dark:text-gray-300 transition-colors">Acessar Painel</a>
                        @else
                            <a href="{{ route('login') }}" class="font-medium text-gray-600 hover:text-primary dark:text-gray-300 transition-colors">Login</a>
                            <a href="{{ route('register') }}" class="px-5 py-2.5 rounded-lg bg-primary text-white font-medium hover:bg-blue-600 transition-colors shadow-lg shadow-blue-500/30">Cadastre-se</a>
                        @endauth
                    @else
                        <a href="{{ url('/instalar') }}" class="px-5 py-2.5 rounded-lg bg-red-600 text-white font-medium hover:bg-red-700 transition-colors shadow-lg shadow-red-500/30">Iniciar Instalação</a>
                    @endif
                </div>
            </div>
        </div>
    </nav>

    <!-- Hero Section -->
    <section class="relative pt-32 pb-20 lg:pt-48 lg:pb-32 overflow-hidden">
        <div class="absolute inset-0 bg-[url('https://www.transparenttextures.com/patterns/cubes.png')] opacity-5"></div>
        <div class="absolute -top-24 -right-24 w-96 h-96 bg-blue-500/20 rounded-full blur-3xl"></div>
        <div class="absolute top-1/2 -left-24 w-72 h-72 bg-purple-500/20 rounded-full blur-3xl"></div>

        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 relative z-10 text-center">
            <h1 class="text-5xl md:text-6xl lg:text-7xl font-extrabold text-gray-900 dark:text-white tracking-tight mb-8">
                Gestão Financeira <br class="hidden md:block" />
                <span class="text-transparent bg-clip-text bg-gradient-to-r from-blue-500 to-cyan-400">Descomplicada</span>
            </h1>
            <p class="mt-4 max-w-2xl text-xl text-gray-600 dark:text-gray-300 mx-auto mb-10">{{ $descricaoSistema }}</p>
                A plataforma SaaS definitiva para alavancar os resultados da sua empresa. Controle contas, fluxos, emita cobranças e visualize relatórios dinâmicos.
            <div class="flex justify-center gap-4">
                <a href="{{ route('register') }}" class="px-8 py-4 rounded-xl bg-primary text-white font-semibold text-lg hover:bg-blue-600 transition-all transform hover:-translate-y-1 shadow-xl shadow-blue-500/40">
                    Comece Grátis
                </a>
                <a href="#recursos" class="px-8 py-4 rounded-xl bg-white dark:bg-slate-800 text-gray-900 dark:text-white font-semibold text-lg hover:bg-gray-50 dark:hover:bg-slate-700 border border-gray-200 dark:border-gray-700 transition-all shadow-sm">
                    Ver Funcionalidades
                </a>
            </div>
        </div>
    </section>

    <!-- Funcionalidades -->
    <section id="recursos" class="py-20 bg-white dark:bg-slate-900">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="text-center max-w-3xl mx-auto mb-16">
                <h2 class="text-primary font-semibold tracking-wide uppercase text-sm mb-2">Recursos Poderosos</h2>
                <h3 class="text-3xl font-bold text-gray-900 dark:text-white mb-4">Tudo o que você precisa em um só lugar</h3>
                <p class="text-gray-600 dark:text-gray-400">Uma suíte completa de ferramentas financeiras construídas para crescer o seu negócio com segurança e performance.</p>
            </div>

            <div class="grid md:grid-cols-3 gap-8">
                <!-- Card 1 -->
                <div class="p-8 rounded-2xl bg-gray-50 dark:bg-slate-800 border border-gray-100 dark:border-slate-700 hover:shadow-xl transition-shadow group">
                    <div class="w-14 h-14 bg-blue-100 dark:bg-blue-900/50 rounded-xl flex items-center justify-center mb-6 group-hover:scale-110 transition-transform">
                        <svg class="w-7 h-7 text-primary" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path></svg>
                    </div>
                    <h4 class="text-xl font-bold text-gray-900 dark:text-white mb-3">Dashboard Interativo</h4>
                    <p class="text-gray-600 dark:text-gray-400 leading-relaxed">Visualize KPIs vitais, saúde financeira e relatórios detalhados com gráficos dinâmicos em tempo real.</p>
                </div>
                
                <!-- Card 2 -->
                <div class="p-8 rounded-2xl bg-gray-50 dark:bg-slate-800 border border-gray-100 dark:border-slate-700 hover:shadow-xl transition-shadow group">
                    <div class="w-14 h-14 bg-green-100 dark:bg-green-900/50 rounded-xl flex items-center justify-center mb-6 group-hover:scale-110 transition-transform">
                        <svg class="w-7 h-7 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                    </div>
                    <h4 class="text-xl font-bold text-gray-900 dark:text-white mb-3">Gestão de Cobranças</h4>
                    <p class="text-gray-600 dark:text-gray-400 leading-relaxed">Integração nativa com Mercado Pago, Asaas e Stripe. Gere boletos e PIX com baixa automática na hora.</p>
                </div>

                <!-- Card 3 -->
                <div class="p-8 rounded-2xl bg-gray-50 dark:bg-slate-800 border border-gray-100 dark:border-slate-700 hover:shadow-xl transition-shadow group">
                    <div class="w-14 h-14 bg-purple-100 dark:bg-purple-900/50 rounded-xl flex items-center justify-center mb-6 group-hover:scale-110 transition-transform">
                        <svg class="w-7 h-7 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"></path></svg>
                    </div>
                    <h4 class="text-xl font-bold text-gray-900 dark:text-white mb-3">Auditoria Avançada</h4>
                    <p class="text-gray-600 dark:text-gray-400 leading-relaxed">Acompanhe todas as ações no sistema com logs robustos. Saiba exatamente quem fez o quê, e quando.</p>
                </div>
            </div>
        </div>
    </section>

    <!-- Planos / Pricing -->
    <section id="planos" class="py-20 bg-gray-50 dark:bg-slate-900/50 relative border-t border-gray-200 dark:border-gray-800">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="text-center max-w-3xl mx-auto mb-16">
                <h3 class="text-3xl font-bold text-gray-900 dark:text-white mb-4">Escolha o plano ideal</h3>
                <p class="text-gray-600 dark:text-gray-400">Sem surpresas. Transparência total para você focar apenas no seu negócio.</p>
            </div>

            <div class="grid md:grid-cols-3 gap-8 max-w-5xl mx-auto">
                <!-- Plano Básico -->
                <div class="bg-white dark:bg-slate-800 rounded-2xl p-8 border border-gray-100 dark:border-slate-700 flex flex-col hover:border-blue-500 transition-colors">
                    <h4 class="text-xl font-semibold text-gray-900 dark:text-white mb-2">Básico</h4>
                    <p class="text-gray-500 dark:text-gray-400 mb-6">Perfeito para quem está começando.</p>
                    <div class="mb-8">
                        <span class="text-4xl font-extrabold text-gray-900 dark:text-white">R$ 49</span>
                        <span class="text-gray-500 dark:text-gray-400 font-medium">/mês</span>
                    </div>
                    <ul class="flex flex-col gap-4 mb-8 flex-1">
                        <li class="flex items-center gap-3 text-gray-600 dark:text-gray-300">
                            <svg class="w-5 h-5 text-green-500 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
                            1 Usuário
                        </li>
                        <li class="flex items-center gap-3 text-gray-600 dark:text-gray-300">
                            <svg class="w-5 h-5 text-green-500 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
                            Painel Financeiro
                        </li>
                        <li class="flex items-center gap-3 text-gray-600 dark:text-gray-300">
                            <svg class="w-5 h-5 text-green-500 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
                            Suporte via Email
                        </li>
                    </ul>
                    <a href="{{ route('register') }}" class="w-full py-3 px-4 bg-blue-50 dark:bg-slate-700 text-primary dark:text-white font-semibold rounded-xl hover:bg-blue-100 dark:hover:bg-slate-600 transition-colors text-center">Assinar Básico</a>
                </div>

                <!-- Plano Pro -->
                <div class="bg-primary rounded-2xl p-8 border border-blue-600 flex flex-col relative transform md:-translate-y-4 shadow-2xl shadow-blue-500/20">
                    <div class="absolute top-0 left-1/2 -translate-x-1/2 -translate-y-1/2 bg-gradient-to-r from-blue-400 to-cyan-300 text-white px-3 py-1 rounded-full text-xs font-bold tracking-wider uppercase shadow-md">
                        Mais Popular
                    </div>
                    <h4 class="text-xl font-semibold text-white mb-2">Profissional</h4>
                    <p class="text-blue-100 mb-6">Para empresas em crescimento.</p>
                    <div class="mb-8 text-white">
                        <span class="text-4xl font-extrabold">R$ 99</span>
                        <span class="text-blue-200 font-medium">/mês</span>
                    </div>
                    <ul class="flex flex-col gap-4 mb-8 flex-1">
                        <li class="flex items-center gap-3 text-white">
                            <svg class="w-5 h-5 text-blue-200 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
                            Até 5 Usuários
                        </li>
                        <li class="flex items-center gap-3 text-white">
                            <svg class="w-5 h-5 text-blue-200 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
                            Gateways de Cobrança
                        </li>
                        <li class="flex items-center gap-3 text-white">
                            <svg class="w-5 h-5 text-blue-200 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
                            Auditoria e Relatórios
                        </li>
                    </ul>
                    <a href="{{ route('register') }}" class="w-full py-3 px-4 bg-white text-primary font-bold rounded-xl hover:bg-gray-50 transition-colors shadow-lg text-center">Assinar Pro</a>
                </div>

                <!-- Plano VIP -->
                <div class="bg-white dark:bg-slate-800 rounded-2xl p-8 border border-gray-100 dark:border-slate-700 flex flex-col hover:border-blue-500 transition-colors">
                    <h4 class="text-xl font-semibold text-gray-900 dark:text-white mb-2">Empresarial</h4>
                    <p class="text-gray-500 dark:text-gray-400 mb-6">Sem limites de expansão.</p>
                    <div class="mb-8">
                        <span class="text-4xl font-extrabold text-gray-900 dark:text-white">R$ 199</span>
                        <span class="text-gray-500 dark:text-gray-400 font-medium">/mês</span>
                    </div>
                    <ul class="flex flex-col gap-4 mb-8 flex-1">
                        <li class="flex items-center gap-3 text-gray-600 dark:text-gray-300">
                            <svg class="w-5 h-5 text-green-500 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
                            Usuários Ilimitados
                        </li>
                        <li class="flex items-center gap-3 text-gray-600 dark:text-gray-300">
                            <svg class="w-5 h-5 text-green-500 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
                            Acesso a API / Integrações
                        </li>
                        <li class="flex items-center gap-3 text-gray-600 dark:text-gray-300">
                            <svg class="w-5 h-5 text-green-500 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
                            Suporte Prioritário 24/7
                        </li>
                    </ul>
                    <a href="{{ route('register') }}" class="w-full py-3 px-4 bg-blue-50 dark:bg-slate-700 text-primary dark:text-white font-semibold rounded-xl hover:bg-blue-100 dark:hover:bg-slate-600 transition-colors text-center">Assinar Empresarial</a>
                </div>
            </div>
        </div>
    </section>

    <!-- Footer -->
    <footer id="contato" class="bg-white dark:bg-slate-900 py-12 border-t border-gray-200 dark:border-gray-800">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 flex flex-col md:flex-row justify-between items-center gap-4">
            <div class="flex items-center gap-2">
                <svg class="w-6 h-6 text-primary" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                <span class="font-bold text-gray-900 dark:text-white">{{ $nomeSistema }}</span>
            </div>
            <p class="text-sm text-gray-500 dark:text-gray-400">
                &copy; {{ date('Y') }} {{ $proprietarioSistema }}. Todos os direitos reservados.
            </p>
        </div>
    </footer>

    <script>
        const nav = document.getElementById('site-navbar');
        window.addEventListener('scroll', function () {
            if (window.scrollY > 12) nav.classList.add('shadow-md');
            else nav.classList.remove('shadow-md');
        });
    </script>

</body>
</html>
