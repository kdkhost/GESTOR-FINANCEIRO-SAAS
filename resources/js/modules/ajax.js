/**
 * Módulo AJAX centralizado — FinanceiroSaaS
 * Wrapper padronizado sobre fetch com tratamento de erros globais.
 */

const csrfToken = () => document.querySelector('meta[name="csrf-token"]')?.content ?? '';

async function requisicao(url, opcoes = {}) {
    const defaults = {
        headers: {
            'Accept':       'application/json',
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': csrfToken(),
        },
    };

    const config = { ...defaults, ...opcoes, headers: { ...defaults.headers, ...(opcoes.headers ?? {}) } };

    try {
        mostrarLoading?.();
        const resposta = await fetch(url, config);
        const dados    = await resposta.json();

        if (! resposta.ok) {
            const msg = dados?.mensagem ?? `Erro ${resposta.status}`;
            window.toast?.(msg, 'erro');
            throw new Error(msg);
        }

        return dados;
    } catch (erro) {
        if (! erro.message.includes('Erro ')) {
            window.toast?.('Falha na comunicação com o servidor.', 'erro');
        }
        throw erro;
    } finally {
        ocultarLoading?.();
    }
}

export default {
    get:    (url, params = {}) => {
        const qs = new URLSearchParams(params).toString();
        return requisicao(qs ? `${url}?${qs}` : url, { method: 'GET' });
    },
    post:   (url, dados = {}) => requisicao(url, { method: 'POST',   body: JSON.stringify(dados) }),
    put:    (url, dados = {}) => requisicao(url, { method: 'PUT',    body: JSON.stringify(dados) }),
    delete: (url)             => requisicao(url, { method: 'DELETE' }),
};
