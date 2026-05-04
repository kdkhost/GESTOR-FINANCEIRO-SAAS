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
            'sistemaDescricao' => configuracao('sistema_descricao', 'CRM SaaS completo para gestão financeira e comercial.'),
            'proprietario' => configuracao('sistema_proprietario', 'FinanceiroSaaS'),
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
    <div id="vue-support-box" data-whatsapp="{{ configuracao('sistema_telefone', '') }}"></div>
</body>
</html>

