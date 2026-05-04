@extends('layouts.auth')

@section('titulo', 'Redefinir senha')

@section('conteudo')
<div class="min-vh-100 d-flex align-items-center justify-content-center" style="background:linear-gradient(135deg,#1e3a5f 0%,#2563eb 50%,#0f172a 100%);">
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-xl-4 col-lg-5 col-md-7">
                <div class="card shadow-lg border-0" style="border-radius:20px;overflow:hidden;">
                    <div class="card-header text-center py-4 border-0" style="background:linear-gradient(135deg,#2563eb,#1e40af);">
                        <h4 class="text-white fw-bold mb-0">{{ configuracao('sistema_nome', 'FinanceiroSaaS') }}</h4>
                        <p class="text-white-50 small mb-0">Definir nova senha</p>
                    </div>
                    <div class="card-body p-4">
                        <form id="form-redefinir-senha" method="POST" action="{{ route('auth.salvar-senha') }}" novalidate>
                            @csrf
                            <input type="hidden" name="token" id="token" value="{{ $token }}">
                            <div class="mb-3">
                                <label class="form-label fw-medium small">E-mail</label>
                                <input type="email" name="email" id="email" class="form-control" required>
                            </div>
                            <div class="mb-3">
                                <label class="form-label fw-medium small">Nova senha</label>
                                <input type="password" name="password" id="password" class="form-control" minlength="8" required>
                            </div>
                            <div class="mb-3">
                                <label class="form-label fw-medium small">Confirmar senha</label>
                                <input type="password" name="password_confirmation" id="password_confirmation" class="form-control" minlength="8" required>
                            </div>
                            <button type="submit" class="btn btn-primary w-100 fw-semibold py-2" id="btn-redefinir">
                                <span id="btn-redefinir-texto"><i class="bi bi-check2-circle me-2"></i>Salvar nova senha</span>
                                <span id="btn-redefinir-loading" class="d-none"><span class="spinner-border spinner-border-sm me-2"></span>Aguarde...</span>
                            </button>
                        </form>
                    </div>
                    <div class="card-footer text-center bg-light py-3">
                        <a href="{{ route('login') }}" class="small text-decoration-none">Voltar para login</a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
document.getElementById('form-redefinir-senha')?.addEventListener('submit', function(e) {
    e.preventDefault();
    const btn = document.getElementById('btn-redefinir');
    document.getElementById('btn-redefinir-texto').classList.add('d-none');
    document.getElementById('btn-redefinir-loading').classList.remove('d-none');
    btn.disabled = true;

    const payload = {
        token: document.getElementById('token').value,
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
            toast(data.mensagem || 'Senha redefinida com sucesso!', 'sucesso');
            setTimeout(() => window.location.href = data.redirect || '/login', 900);
            return;
        }
        if (data.errors) toast(Object.values(data.errors).flat().join(' | '), 'erro');
        else toast(data.mensagem || 'Erro ao redefinir senha.', 'erro');
    })
    .catch(() => toast('Erro de conexao.', 'erro'))
    .finally(() => {
        btn.disabled = false;
        document.getElementById('btn-redefinir-texto').classList.remove('d-none');
        document.getElementById('btn-redefinir-loading').classList.add('d-none');
    });
});
</script>
@endpush

