<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ configuracao('sistema_nome', config('app.name', 'FinanceiroSaaS')) }}</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="bg-slate-950 text-slate-100">
    @php
        $props = [
            'sistemaNome' => configuracao('sistema_nome', 'FinanceiroSaaS'),
            'sistemaDescricao' => configuracao('sistema_descricao', 'CRM SaaS completo para gestao financeira e comercial.'),
            'proprietario' => configuracao('sistema_proprietario', 'FinanceiroSaaS'),
            'landingBadge' => configuracao('landing_badge', 'Premium SaaS'),
            'landingTitulo' => configuracao('landing_titulo', configuracao('sistema_nome', 'FinanceiroSaaS')),
            'landingSubtitulo' => configuracao('landing_subtitulo', 'Gestao com inteligencia e automacao'),
            'landingDescricao' => configuracao('landing_descricao', configuracao('sistema_descricao', 'CRM SaaS completo para gestao financeira e comercial.')),
            'landingCtaPrimario' => configuracao('landing_cta_primario', 'Comecar agora'),
            'landingCtaSecundario' => configuracao('landing_cta_secundario', 'Explorar modulo'),
            'logo' => configuracao('sistema_logo') ? asset('storage/' . configuracao('sistema_logo')) : null,
            'linkLogin' => route('login'),
            'linkCadastro' => route('register'),
            'linkPainel' => url('/admin/dashboard'),
            'instalado' => file_exists(storage_path('installed')),
            'autenticado' => auth()->check(),
        ];
    @endphp

    <div id="premium-landing-root" data-props='@json($props)'></div>
    <div id="vue-back-to-top"></div>
    <div id="vue-support-box" data-whatsapp="{{ configuracao('whatsapp_suporte', configuracao('sistema_telefone', '')) }}"></div>
</body>
</html>

