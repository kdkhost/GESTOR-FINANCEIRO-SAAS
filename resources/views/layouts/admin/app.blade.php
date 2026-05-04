<!DOCTYPE html>
<html lang="pt-BR" data-bs-theme="light">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta name="description" content="{{ configuracao('sistema_descricao', 'Sistema financeiro modular multiusuário') }}">
    <title>@yield('titulo', 'Dashboard') — {{ configuracao('sistema_nome', config('app.name')) }}</title>

    {{-- Favicon --}}
    <link rel="icon" type="image/x-icon" href="{{ asset(configuracao('sistema_favicon', 'favicon.ico')) }}">
    <link rel="manifest" href="{{ route('pwa.manifest') }}">

    {{-- AdminLTE 4 --}}
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/admin-lte@4.0.0-rc3/dist/css/adminlte.min.css">

    {{-- Bootstrap Icons --}}
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">

    {{-- Google Fonts --}}
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap">

    {{-- Toastify --}}
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/toastify-js/src/toastify.min.css">

    {{-- SweetAlert2 --}}
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css">

    {{-- DataTables --}}
    <link rel="stylesheet" href="https://cdn.datatables.net/2.0.8/css/dataTables.bootstrap5.min.css">

    {{-- FullCalendar --}}
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/fullcalendar@6.1.14/index.global.min.css">

    {{-- CSS do sistema --}}
    @vite(['resources/css/app.css'])

    @stack('styles')
    <style>
        .app-content-header { padding: 1.25rem 0; }
        .app-content-header .breadcrumb { margin-bottom: 0; }
        .app-content-header h3 { font-size: 1.55rem; font-weight: 700; letter-spacing: -.02em; }
        .app-content { padding: 1rem 0 1.5rem; }
        .app-content .container-fluid { padding-left: 1rem; padding-right: 1rem; }
        .card-standard { border-radius: 1rem; box-shadow: 0 18px 45px rgba(15,23,42,.05); }
        .card-standard .card-header { border-bottom: 1px solid rgba(226,232,240,.7); }
        .card-standard .card-title { font-size: 1rem; font-weight: 600; }
        .card-standard .card-body { min-height: 1px; }
        .page-section-title { font-size: 1rem; font-weight: 700; letter-spacing: .01em; }
        .badge-subtle { opacity: .9; }
        .table thead th { font-weight: 700; }
    </style>
</head>
<body class="layout-fixed sidebar-expand-lg sidebar-mini">

    {{-- Loading global AJAX --}}
    <div id="loading-overlay" style="display:none;position:fixed;top:0;left:0;width:100%;height:100%;background:rgba(0,0,0,.4);z-index:9999;align-items:center;justify-content:center;">
        <div class="spinner-border text-light" role="status" style="width:3rem;height:3rem;">
            <span class="visually-hidden">Carregando...</span>
        </div>
    </div>

    <div class="app-wrapper">

        {{-- Navbar superior --}}
        @include('layouts.admin.navbar')

        {{-- Sidebar --}}
        @include('layouts.admin.sidebar')

        {{-- Conteúdo principal --}}
        <main class="app-main">

            {{-- Cabeçalho da página --}}
            <div class="app-content-header">
                <div class="container-fluid">
                    <div class="row">
                        <div class="col-sm-6">
                            <h3 class="mb-0">@yield('titulo_pagina', 'Dashboard')</h3>
                        </div>
                        <div class="col-sm-6">
                            <ol class="breadcrumb float-sm-end">
                                <li class="breadcrumb-item"><a href="{{ route('admin.dashboard.index') }}">Início</a></li>
                                @yield('breadcrumb')
                            </ol>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Conteúdo --}}
            <div class="app-content">
                <div class="container-fluid">
                    @yield('conteudo')
                </div>
            </div>
        </main>

        {{-- Footer --}}
        <footer class="app-footer">
            <strong>{{ configuracao('sistema_nome', 'FinanceiroSaaS') }}</strong> &copy; {{ date('Y') }}
            <span class="float-end">{{ configuracao('sistema_proprietario', 'Marcelo Brad RJ') }}</span>
        </footer>
    </div>

    {{-- jQuery --}}
    <script src="https://cdn.jsdelivr.net/npm/jquery@3.7.1/dist/jquery.min.js"></script>

    {{-- Bootstrap 5 --}}
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

    {{-- AdminLTE 4 --}}
    <script src="https://cdn.jsdelivr.net/npm/admin-lte@4.0.0-rc3/dist/js/adminlte.min.js"></script>

    {{-- Toastify --}}
    <script src="https://cdn.jsdelivr.net/npm/toastify-js"></script>

    {{-- SweetAlert2 --}}
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.all.min.js"></script>

    {{-- DataTables --}}
    <script src="https://cdn.datatables.net/2.0.8/js/dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/2.0.8/js/dataTables.bootstrap5.min.js"></script>

    {{-- IMask --}}
    <script src="https://cdn.jsdelivr.net/npm/imask@7.6.1/dist/imask.min.js"></script>

    {{-- Chart.js --}}
    <script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.3/dist/chart.umd.min.js"></script>

    {{-- ApexCharts --}}
    <script src="https://cdn.jsdelivr.net/npm/apexcharts@3.49.2/dist/apexcharts.min.js"></script>

    {{-- FilePond (upload com progresso) --}}
    <script src="https://cdn.jsdelivr.net/npm/filepond@4.30.6/dist/filepond.min.js"></script>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/filepond@4.30.6/dist/filepond.min.css">

    {{-- JS global do sistema --}}
    @vite(['resources/js/app.js'])

    {{-- Configurações globais JS --}}
    <script>
        // CSRF para todas as requisições AJAX
        $.ajaxSetup({
            headers: { 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') }
        });

        // Configuração global SweetAlert2
        const Swal = window.Swal.mixin({
            confirmButtonColor: '#3b82f6',
            cancelButtonColor:  '#ef4444',
            customClass: { confirmButton: 'btn btn-primary me-2', cancelButton: 'btn btn-danger' },
            buttonsStyling: false,
        });
        window.SistemaAlert = Swal;

        // Função global de toast (Toastify)
        window.toast = function(mensagem, tipo = 'sucesso') {
            const cores = {
                sucesso: 'linear-gradient(to right, #00b09b, #96c93d)',
                erro:    'linear-gradient(to right, #ff5f6d, #ffc371)',
                alerta:  'linear-gradient(to right, #f7971e, #ffd200)',
                info:    'linear-gradient(to right, #4facfe, #00f2fe)',
            };
            Toastify({
                text:       mensagem,
                duration:   4000,
                gravity:    'top',
                position:   'right',
                stopOnFocus: true,
                style: { background: cores[tipo] || cores.info, borderRadius: '8px', fontWeight: '500' },
            }).showToast();
        };

        // Função global de confirmação de exclusão
        window.confirmarExclusao = function(url, callback) {
            SistemaAlert.fire({
                title:              'Confirmar exclusão',
                text:               'Esta ação não pode ser desfeita!',
                icon:               'warning',
                showCancelButton:   true,
                confirmButtonText:  'Sim, excluir!',
                cancelButtonText:   'Cancelar',
            }).then((result) => {
                if (result.isConfirmed) {
                    if (typeof callback === 'function') callback();
                    else {
                        $.ajax({ url, type: 'DELETE',
                            success: (r) => { toast(r.mensagem || 'Excluído com sucesso!', 'sucesso'); },
                            error:   (r) => { toast(r.responseJSON?.mensagem || 'Erro ao excluir.', 'erro'); }
                        });
                    }
                }
            });
        };

        // Loading global
        window.mostrarLoading  = () => { document.getElementById('loading-overlay').style.display = 'flex'; };
        window.ocultarLoading  = () => { document.getElementById('loading-overlay').style.display = 'none'; };

        // Intercepta AJAX global para loading
        $(document).on('ajaxSend', function() { mostrarLoading(); });
        $(document).on('ajaxComplete', function() { ocultarLoading(); });

        // Máscaras globais IMask
        document.addEventListener('DOMContentLoaded', function() {
            // Moeda
            document.querySelectorAll('.mask-moeda').forEach(el => {
                IMask(el, { mask: Number, scale: 2, thousandsSeparator: '.', radix: ',', normalizeZeros: true, padFractionalZeros: true });
            });
            // CPF
            document.querySelectorAll('.mask-cpf').forEach(el => {
                IMask(el, { mask: '000.000.000-00' });
            });
            // CNPJ
            document.querySelectorAll('.mask-cnpj').forEach(el => {
                IMask(el, { mask: '00.000.000/0000-00' });
            });
            // Telefone
            document.querySelectorAll('.mask-telefone').forEach(el => {
                IMask(el, { mask: [{ mask: '(00) 0000-0000' }, { mask: '(00) 00000-0000' }] });
            });
            // CEP
            document.querySelectorAll('.mask-cep').forEach(el => {
                IMask(el, { mask: '00000-000' });
            });
            // Data
            document.querySelectorAll('.mask-data').forEach(el => {
                IMask(el, { mask: '00/00/0000' });
            });
        });

        // ViaCEP automático
        $(document).on('blur', '.viacep', function() {
            const cep = $(this).val().replace(/\D/g, '');
            if (cep.length !== 8) return;
            const form = $(this).closest('form');
            $.getJSON('https://viacep.com.br/ws/' + cep + '/json/', function(dados) {
                if (dados.erro) { toast('CEP não encontrado.', 'alerta'); return; }
                form.find('[name="logradouro"]').val(dados.logradouro);
                form.find('[name="bairro"]').val(dados.bairro);
                form.find('[name="cidade"]').val(dados.localidade);
                form.find('[name="estado"]').val(dados.uf);
                toast('Endereço preenchido automaticamente!', 'sucesso');
            });
        });
    </script>

    @stack('scripts')
</body>
</html>
