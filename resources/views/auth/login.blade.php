@extends('layouts.auth')

@section('titulo', 'Login')

@section('conteudo')
<div class="min-vh-100 d-flex align-items-center justify-content-center" style="background:linear-gradient(135deg,#1e3a5f 0%,#2563eb 50%,#0f172a 100%);">
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-xl-4 col-lg-5 col-md-7">

                {{-- Card de Login --}}
                <div class="card shadow-lg border-0" style="border-radius:20px;overflow:hidden;">

                    {{-- Cabeçalho --}}
                    <div class="card-header text-center py-4 border-0" style="background:linear-gradient(135deg,#2563eb,#1e40af);">
                        @if(configuracao('sistema_logo'))
                            <img src="{{ asset('storage/' . configuracao('sistema_logo')) }}" alt="Logo" style="max-height:60px;margin-bottom:.5rem;">
                        @else
                            <div class="mb-2" style="font-size:3rem;">💰</div>
                        @endif
                        <h4 class="text-white fw-bold mb-0">{{ configuracao('sistema_nome', 'FinanceiroSaaS') }}</h4>
                        <p class="text-white-50 small mb-0">Gestão Financeira Inteligente</p>
                    </div>

                    {{-- Corpo --}}
                    <div class="card-body p-4">
                        <h5 class="fw-semibold mb-4 text-center">Acesse sua conta</h5>

                        <form id="form-login" method="POST" action="{{ route('auth.login') }}" novalidate>
                            @csrf

                            {{-- E-mail --}}
                            <div class="mb-3">
                                <label for="email" class="form-label fw-medium small">E-mail</label>
                                <div class="input-group">
                                    <span class="input-group-text bg-light border-end-0">
                                        <i class="bi bi-envelope text-muted"></i>
                                    </span>
                                    <input type="email" id="email" name="email"
                                           class="form-control border-start-0 @error('email') is-invalid @enderror"
                                           value="{{ old('email') }}" placeholder="seu@email.com"
                                           required autocomplete="email">
                                    @error('email')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            {{-- Senha --}}
                            <div class="mb-3">
                                <label for="password" class="form-label fw-medium small">Senha</label>
                                <div class="input-group">
                                    <span class="input-group-text bg-light border-end-0">
                                        <i class="bi bi-lock text-muted"></i>
                                    </span>
                                    <input type="password" id="password" name="password"
                                           class="form-control border-start-0 border-end-0 @error('password') is-invalid @enderror"
                                           placeholder="••••••••" required autocomplete="current-password">
                                    <button class="btn btn-light border" type="button" id="toggle-senha" title="Mostrar senha">
                                        <i class="bi bi-eye" id="icone-senha"></i>
                                    </button>
                                    @error('password')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            {{-- Lembrar + Esqueci --}}
                            <div class="d-flex align-items-center justify-content-between mb-4">
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" id="remember" name="remember">
                                    <label class="form-check-label small" for="remember">Lembrar-me</label>
                                </div>
                                <a href="{{ route('auth.esqueci-senha') }}" class="small text-primary text-decoration-none">
                                    Esqueceu a senha?
                                </a>
                            </div>

                            {{-- Botão --}}
                            <button type="submit" class="btn btn-primary w-100 fw-semibold py-2" id="btn-login">
                                <span id="btn-login-texto"><i class="bi bi-box-arrow-in-right me-2"></i>Entrar</span>
                                <span id="btn-login-loading" class="d-none">
                                    <span class="spinner-border spinner-border-sm me-2"></span>Aguarde...
                                </span>
                            </button>

                        </form>
                    </div>

                    {{-- Rodapé do card --}}
                    <div class="card-footer text-center bg-light py-3">
                        <span class="small text-muted">
                            {{ configuracao('sistema_nome', 'FinanceiroSaaS') }} &copy; {{ date('Y') }} —
                            {{ configuracao('sistema_proprietario', 'Marcelo Brad RJ') }}
                        </span>
                    </div>

                </div>

            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
// Toggle senha
document.getElementById('toggle-senha')?.addEventListener('click', function() {
    const input = document.getElementById('password');
    const icone = document.getElementById('icone-senha');
    const mostrar = input.type === 'password';
    input.type = mostrar ? 'text' : 'password';
    icone.className = mostrar ? 'bi bi-eye-slash' : 'bi bi-eye';
});

// AJAX login
document.getElementById('form-login')?.addEventListener('submit', function(e) {
    e.preventDefault();
    const btn = document.getElementById('btn-login');
    document.getElementById('btn-login-texto').classList.add('d-none');
    document.getElementById('btn-login-loading').classList.remove('d-none');
    btn.disabled = true;

    fetch(this.action, {
        method: 'POST',
        headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': document.querySelector('[name=_token]').value, 'Accept': 'application/json' },
        body: JSON.stringify({ email: document.getElementById('email').value, password: document.getElementById('password').value, remember: document.getElementById('remember').checked })
    })
    .then(r => r.json())
    .then(r => {
        if (r.sucesso) {
            toast(r.mensagem || 'Login realizado!', 'sucesso');
            setTimeout(() => { window.location.href = r.redirect || '/admin/dashboard'; }, 800);
        } else {
            toast(r.mensagem || 'Credenciais inválidas.', 'erro');
            btn.disabled = false;
            document.getElementById('btn-login-texto').classList.remove('d-none');
            document.getElementById('btn-login-loading').classList.add('d-none');
        }
    })
    .catch(() => {
        toast('Erro de conexão.', 'erro');
        btn.disabled = false;
        document.getElementById('btn-login-texto').classList.remove('d-none');
        document.getElementById('btn-login-loading').classList.add('d-none');
    });
});
</script>
@endpush
