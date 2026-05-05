@extends('layouts.auth')

@section('titulo', 'Esqueci minha senha')

@section('conteudo')
<div class="min-vh-100 d-flex align-items-center justify-content-center" style="background:linear-gradient(135deg,#1e3a5f 0%,#2563eb 50%,#0f172a 100%);">
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-xl-4 col-lg-5 col-md-7">
                <div class="card shadow-lg border-0" style="border-radius:20px;overflow:hidden;">
                    <div class="card-header text-center py-4 border-0" style="background:linear-gradient(135deg,#2563eb,#1e40af);">
                        <h4 class="text-white fw-bold mb-0">{{ configuracao('sistema_nome', 'FinanceiroSaaS') }}</h4>
                        <p class="text-white-50 small mb-0">Recuperar acesso</p>
                    </div>
                    <div class="card-body p-4">
                        <p class="small text-muted mb-3">Informe seu e-mail para receber o link de redefinição.</p>
                        <form id="form-esqueci-senha" method="POST" action="{{ route('auth.enviar-link') }}" novalidate>
                            @csrf
                            <div class="mb-3">
                                <label class="form-label fw-medium small">E-mail</label>
                                <input type="email" name="email" id="email" class="form-control" required>
                            </div>
                            <button type="submit" class="btn btn-primary w-100 fw-semibold py-2" id="btn-enviar-link">
                                <span id="btn-enviar-link-texto"><i class="bi bi-send me-2"></i>Enviar link</span>
                                <span id="btn-enviar-link-loading" class="d-none"><span class="spinner-border spinner-border-sm me-2"></span>Aguarde...</span>
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
document.getElementById('form-esqueci-senha')?.addEventListener('submit', function(e) {
    e.preventDefault();
    const btn = document.getElementById('btn-enviar-link');
    document.getElementById('btn-enviar-link-texto').classList.add('d-none');
    document.getElementById('btn-enviar-link-loading').classList.remove('d-none');
    btn.disabled = true;

    fetch(this.action, {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('[name=_token]').value,
            'Accept': 'application/json'
        },
        body: JSON.stringify({ email: document.getElementById('email').value })
    })
    .then(async r => ({ ok: r.ok, data: await r.json() }))
    .then(({ ok, data }) => {
        if (ok && data.sucesso) {
            toast(data.mensagem || 'Verifique seu e-mail.', 'sucesso');
            document.getElementById('email').value = ''; // Limpa o campo
            return;
        }
        if (data.errors) {
            toast(Object.values(data.errors).flat().join(' | '), 'erro');
        } else {
            toast(data.mensagem || 'Erro ao enviar link.', 'erro');
        }
    })
    .catch(() => toast('Erro de conexao.', 'erro'))
    .finally(() => {
        btn.disabled = false;
        document.getElementById('btn-enviar-link-texto').classList.remove('d-none');
        document.getElementById('btn-enviar-link-loading').classList.add('d-none');
    });
});
</script>
@endpush

