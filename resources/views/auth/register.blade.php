@extends('layouts.auth')

@section('titulo', 'Cadastro')

@section('conteudo')
<div class="min-vh-100 d-flex align-items-center justify-content-center" style="background:linear-gradient(135deg,#1e3a5f 0%,#2563eb 50%,#0f172a 100%);">
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-xl-5 col-lg-6 col-md-8">
                <div class="card shadow-lg border-0" style="border-radius:20px;overflow:hidden;">
                    <div class="card-header text-center py-4 border-0" style="background:linear-gradient(135deg,#2563eb,#1e40af);">
                        <h4 class="text-white fw-bold mb-0">{{ configuracao('sistema_nome', 'FinanceiroSaaS') }}</h4>
                        <p class="text-white-50 small mb-0">Crie sua conta</p>
                    </div>

                    <div class="card-body p-4">
                        <form id="form-register" method="POST" action="{{ route('auth.register.post') }}" novalidate>
                            @csrf
                            <div class="mb-3">
                                <label class="form-label fw-medium small">Nome</label>
                                <input type="text" name="name" id="name" class="form-control" required>
                            </div>
                            <div class="mb-3">
                                <label class="form-label fw-medium small">E-mail</label>
                                <input type="email" name="email" id="email" class="form-control" required>
                            </div>
                            <div class="mb-3">
                                <label class="form-label fw-medium small">Senha</label>
                                <input type="password" name="password" id="password" class="form-control" minlength="8" required>
                            </div>
                            <div class="mb-4">
                                <label class="form-label fw-medium small">Confirmar senha</label>
                                <input type="password" name="password_confirmation" id="password_confirmation" class="form-control" minlength="8" required>
                            </div>

                            <button type="submit" class="btn btn-primary w-100 fw-semibold py-2" id="btn-register">
                                <span id="btn-register-texto"><i class="bi bi-person-plus me-2"></i>Criar conta</span>
                                <span id="btn-register-loading" class="d-none"><span class="spinner-border spinner-border-sm me-2"></span>Aguarde...</span>
                            </button>
                        </form>
                    </div>

                    <div class="card-footer text-center bg-light py-3">
                        <span class="small text-muted">Já possui conta? <a href="{{ route('login') }}" class="text-decoration-none">Entrar</a></span>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
document.getElementById('form-register')?.addEventListener('submit', function (e) {
    e.preventDefault();
    const btn = document.getElementById('btn-register');
    document.getElementById('btn-register-texto').classList.add('d-none');
    document.getElementById('btn-register-loading').classList.remove('d-none');
    btn.disabled = true;

    const payload = {
        name: document.getElementById('name').value,
        email: document.getElementById('email').value,
        password: document.getElementById('password').value,
        password_confirmation: document.getElementById('password_confirmation').value
    };

    fetch(this.action, {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('[name=_token]').value,
            'Accept': 'application/json'
        },
        body: JSON.stringify(payload)
    })
    .then(async r => ({ ok: r.ok, data: await r.json() }))
    .then(({ ok, data }) => {
        if (ok && data.sucesso) {
            toast(data.mensagem || 'Cadastro realizado!', 'sucesso');
            setTimeout(() => window.location.href = data.redirect || '/admin/dashboard', 800);
            return;
        }

        if (data.errors) {
            const mensagens = Object.values(data.errors).flat().join(' | ');
            toast(mensagens, 'erro');
        } else {
            toast(data.mensagem || 'Erro ao cadastrar.', 'erro');
        }

        btn.disabled = false;
        document.getElementById('btn-register-texto').classList.remove('d-none');
        document.getElementById('btn-register-loading').classList.add('d-none');
    })
    .catch(() => {
        toast('Erro de conexao.', 'erro');
        btn.disabled = false;
        document.getElementById('btn-register-texto').classList.remove('d-none');
        document.getElementById('btn-register-loading').classList.add('d-none');
    });
});
</script>
@endpush

