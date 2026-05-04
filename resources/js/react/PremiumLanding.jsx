import React from 'react';

const modulos = [
    ['CRM e Pipeline', 'Gestao de leads, funil por etapa e previsao comercial.'],
    ['Financeiro e Cobrancas', 'Contas a pagar/receber, PIX, boleto e recorrencias.'],
    ['Automacoes', 'Alertas, tarefas e notificacoes por regras operacionais.'],
    ['Relatorios', 'DRE, fluxo de caixa, inadimplencia e saude financeira.'],
    ['Permissoes Granulares', 'Controle de acesso por papel e por acao.'],
    ['Auditoria e Compliance', 'Rastro completo de operacoes do sistema.'],
];

const jornada = [
    ['1. Captacao', 'Landing, formulario, importacao e qualificacao de lead.'],
    ['2. Conversao', 'Pipeline visual, proposta, aprovacao e contrato.'],
    ['3. Faturamento', 'Emissao de cobranca e conciliacao financeira.'],
    ['4. Pos-venda', 'Suporte, renovacoes e expansao da carteira.'],
];

const integracoes = ['WhatsApp', 'Mercado Pago', 'Asaas', 'PIX', 'API', 'App Android', 'Webhook'];

const faq = [
    ['Preciso instalar algo?', 'Nao. O sistema e 100% SaaS e acessado pelo navegador, com app mobile quando contratado.'],
    ['Posso testar antes?', 'Sim. O ambiente permite avaliacao inicial e ativacao progressiva de modulos.'],
    ['Os modulos sao obrigatorios?', 'Nao. A arquitetura e desacoplada: voce ativa apenas o que usar.'],
    ['Tem permissoes por equipe?', 'Sim. Controle granular por perfis, usuarios e acoes.'],
];

export default function PremiumLanding({ props }) {
    const {
        sistemaNome, sistemaDescricao, proprietario, logo,
        linkLogin, linkCadastro, linkPainel, instalado, autenticado,
    } = props;

    return (
        <div className="min-h-screen bg-[radial-gradient(circle_at_top_right,_#1d4ed8_0%,_#020617_45%,_#020617_100%)]">
            <header className="sticky top-0 z-40 border-b border-white/10 bg-slate-950/80 backdrop-blur">
                <nav className="mx-auto flex h-20 max-w-7xl items-center justify-between px-4 sm:px-6 lg:px-8">
                    <div className="flex items-center gap-3">
                        {logo ? <img src={logo} alt={sistemaNome} className="h-10 w-10 rounded-xl object-cover ring-1 ring-white/20" /> : <div className="flex h-10 w-10 items-center justify-center rounded-xl bg-blue-500 font-bold text-white">S</div>}
                        <span className="text-xl font-semibold tracking-tight">{sistemaNome}</span>
                    </div>
                    <div className="hidden items-center gap-8 text-sm text-slate-300 md:flex">
                        <a href="#visao" className="hover:text-white">Visao</a>
                        <a href="#modulos" className="hover:text-white">Modulos</a>
                        <a href="#planos" className="hover:text-white">Planos</a>
                        <a href="#contato" className="hover:text-white">Contato</a>
                    </div>
                    <div className="flex items-center gap-3">
                        {instalado ? autenticado
                            ? <a href={linkPainel} className="rounded-lg border border-white/20 px-4 py-2 text-sm hover:bg-white/10">Acessar Painel</a>
                            : <><a href={linkLogin} className="rounded-lg border border-white/20 px-4 py-2 text-sm hover:bg-white/10">Login</a><a href={linkCadastro} className="rounded-lg bg-blue-500 px-4 py-2 text-sm font-semibold text-white hover:bg-blue-400">Cadastre-se</a></>
                            : <a href="/instalar" className="rounded-lg bg-rose-600 px-4 py-2 text-sm font-semibold text-white hover:bg-rose-500">Iniciar Instalacao</a>}
                    </div>
                </nav>
            </header>

            <main>
                <section id="visao" className="mx-auto grid max-w-7xl gap-10 px-4 pb-16 pt-20 sm:px-6 lg:grid-cols-2 lg:px-8 lg:pt-28">
                    <div>
                        <p className="mb-4 inline-flex rounded-full border border-blue-400/40 bg-blue-500/10 px-3 py-1 text-xs uppercase tracking-widest text-blue-200">Premium SaaS</p>
                        <h1 className="text-5xl font-semibold leading-tight tracking-tight text-white lg:text-6xl">{sistemaNome}</h1>
                        <p className="mt-6 max-w-xl text-lg leading-relaxed text-slate-300">{sistemaDescricao}</p>
                        <div className="mt-8 flex flex-wrap gap-3">
                            <a href={linkCadastro} className="rounded-xl bg-white px-6 py-3 text-sm font-semibold text-slate-900 hover:bg-slate-200">Comecar agora</a>
                            <a href="#modulos" className="rounded-xl border border-white/20 px-6 py-3 text-sm font-semibold hover:bg-white/10">Explorar modulo</a>
                        </div>
                    </div>
                    <div className="rounded-2xl border border-white/10 bg-white/5 p-6 shadow-2xl shadow-black/30">
                        <div className="mb-4 text-sm text-slate-300">Cockpit executivo em tempo real</div>
                        <div className="grid grid-cols-2 gap-4">
                            <div className="rounded-xl bg-emerald-500/10 p-4 ring-1 ring-emerald-400/20"><div className="text-xs text-emerald-300">Receita mensal</div><div className="mt-2 text-2xl font-semibold text-white">R$ 98.420</div></div>
                            <div className="rounded-xl bg-violet-500/10 p-4 ring-1 ring-violet-400/20"><div className="text-xs text-violet-300">Taxa de conversao</div><div className="mt-2 text-2xl font-semibold text-white">18,7%</div></div>
                            <div className="rounded-xl bg-cyan-500/10 p-4 ring-1 ring-cyan-400/20"><div className="text-xs text-cyan-300">Leads ativos</div><div className="mt-2 text-2xl font-semibold text-white">1.284</div></div>
                            <div className="rounded-xl bg-amber-500/10 p-4 ring-1 ring-amber-400/20"><div className="text-xs text-amber-300">NPS</div><div className="mt-2 text-2xl font-semibold text-white">74</div></div>
                        </div>
                    </div>
                </section>

                <section id="recursos" className="border-y border-white/10 bg-slate-900/60">
                    <div className="mx-auto max-w-7xl px-4 py-16 sm:px-6 lg:px-8">
                        <div className="mb-8 flex items-end justify-between">
                            <div>
                                <h2 className="text-3xl font-semibold text-white">Plataforma rica em operacao</h2>
                                <p className="mt-2 text-sm text-slate-300">Tudo conectado entre comercial, financeiro e suporte.</p>
                            </div>
                            <a href={linkPainel} className="hidden rounded-lg border border-white/20 px-4 py-2 text-sm text-slate-200 hover:bg-white/10 md:inline-flex">Abrir painel</a>
                        </div>
                        <div className="grid gap-4 md:grid-cols-2 lg:grid-cols-3">
                            {modulos.map(([titulo, desc]) => (
                                <article key={titulo} className="rounded-2xl border border-white/10 bg-white/5 p-6">
                                    <h3 className="text-lg font-semibold text-white">{titulo}</h3>
                                    <p className="mt-2 text-sm text-slate-300">{desc}</p>
                                </article>
                            ))}
                        </div>
                    </div>
                </section>

                <section id="modulos" className="mx-auto max-w-7xl px-4 py-16 sm:px-6 lg:px-8">
                    <div className="mb-8">
                        <h2 className="text-3xl font-semibold text-white">Modulos opcionais e adicionais</h2>
                        <p className="mt-2 text-sm text-slate-300">Monte o CRM SaaS sob medida para sua operacao.</p>
                    </div>
                    <div className="grid gap-4 md:grid-cols-2">
                        {[
                            ['Painel de Multi-Atendimento', 'Operadores simultaneos, filas, transferencia e historico de conversas.'],
                            ['Chatbot Profissional', 'Fluxos com menus, respostas condicionais e transferencia para humano.'],
                            ['Conexoes WhatsApp Extras', 'Adicione numeros por setor, equipe ou filial.'],
                            ['Funcionarios Extras', 'Escalone equipe sem travas de arquitetura.'],
                        ].map(([titulo, texto]) => (
                            <article key={titulo} className="rounded-2xl border border-white/10 bg-white/[0.03] p-6">
                                <h3 className="text-lg font-semibold text-white">{titulo}</h3>
                                <p className="mt-2 text-sm text-slate-300">{texto}</p>
                            </article>
                        ))}
                    </div>
                </section>

                <section className="mx-auto max-w-7xl px-4 py-16 sm:px-6 lg:px-8">
                    <div className="grid gap-6 lg:grid-cols-2">
                        <div className="rounded-2xl border border-white/10 bg-white/[0.03] p-6">
                            <h3 className="text-xl font-semibold text-white">Jornada operacional completa</h3>
                            <div className="mt-6 space-y-4">
                                {jornada.map(([etapa, desc]) => (
                                    <div key={etapa} className="rounded-xl border border-white/10 bg-slate-900/50 p-4">
                                        <div className="text-sm font-semibold text-blue-300">{etapa}</div>
                                        <div className="mt-1 text-sm text-slate-300">{desc}</div>
                                    </div>
                                ))}
                            </div>
                        </div>
                        <div className="rounded-2xl border border-white/10 bg-white/[0.03] p-6">
                            <h3 className="text-xl font-semibold text-white">Indicadores para decisoes rapidas</h3>
                            <div className="mt-6 grid gap-3">
                                {[
                                    ['SLA suporte', '97% em ate 2h'],
                                    ['Inadimplencia', '4,2%'],
                                    ['Ticket medio', 'R$ 1.480'],
                                    ['Churn mensal', '1,9%'],
                                ].map(([k, v]) => (
                                    <div key={k} className="flex items-center justify-between rounded-xl border border-white/10 bg-slate-900/50 p-4 text-sm">
                                        <span className="text-slate-300">{k}</span>
                                        <span className="font-semibold text-white">{v}</span>
                                    </div>
                                ))}
                            </div>
                        </div>
                    </div>
                </section>

                <section id="planos" className="mx-auto max-w-7xl px-4 pb-16 sm:px-6 lg:px-8">
                    <h2 className="text-3xl font-semibold text-white">Planos</h2>
                    <div className="mt-8 grid gap-4 md:grid-cols-3">
                        {[['Starter', 'R$ 89/mes'], ['Growth', 'R$ 199/mes'], ['Scale', 'R$ 399/mes']].map(([nome, preco]) => (
                            <div key={nome} className="rounded-2xl border border-white/10 bg-white/[0.03] p-6">
                                <h3 className="text-lg font-semibold text-white">{nome}</h3>
                                <p className="mt-2 text-2xl font-semibold text-blue-300">{preco}</p>
                                <ul className="mt-4 space-y-2 text-sm text-slate-300">
                                    <li>CRM + Financeiro integrado</li>
                                    <li>Permissoes granulares</li>
                                    <li>Suporte operacional</li>
                                </ul>
                                <a href={linkCadastro} className="mt-6 inline-flex rounded-lg bg-blue-500 px-4 py-2 text-sm font-semibold text-white hover:bg-blue-400">Assinar</a>
                            </div>
                        ))}
                    </div>
                </section>

                <section className="border-y border-white/10 bg-slate-900/60">
                    <div className="mx-auto max-w-7xl px-4 py-16 sm:px-6 lg:px-8">
                        <h2 className="text-3xl font-semibold text-white">Integracoes nativas</h2>
                        <div className="mt-8 grid grid-cols-2 gap-3 md:grid-cols-4 lg:grid-cols-7">
                            {integracoes.map((i) => (
                                <div key={i} className="rounded-xl border border-white/10 bg-white/[0.03] px-4 py-3 text-center text-sm text-slate-200">{i}</div>
                            ))}
                        </div>
                    </div>
                </section>

                <section className="mx-auto max-w-7xl px-4 py-16 sm:px-6 lg:px-8">
                    <h2 className="text-3xl font-semibold text-white">Depoimentos</h2>
                    <div className="mt-8 grid gap-4 md:grid-cols-3">
                        {[
                            ['“Automatizamos cobranca e atendimento em semanas.”', 'Gestora Financeira'],
                            ['“Pipeline e CRM deram previsibilidade comercial.”', 'Diretor Comercial'],
                            ['“Painel robusto, suporte rapido e operacao estavel.”', 'CEO SaaS'],
                        ].map(([texto, cargo]) => (
                            <blockquote key={texto} className="rounded-2xl border border-white/10 bg-white/[0.03] p-6">
                                <p className="text-sm text-slate-200">{texto}</p>
                                <footer className="mt-4 text-xs text-slate-400">{cargo}</footer>
                            </blockquote>
                        ))}
                    </div>
                </section>

                <section id="faq" className="mx-auto max-w-7xl px-4 pb-16 sm:px-6 lg:px-8">
                    <h2 className="text-3xl font-semibold text-white">FAQ</h2>
                    <div className="mt-8 space-y-3">
                        {faq.map(([q, a]) => (
                            <details key={q} className="rounded-xl border border-white/10 bg-white/[0.03] p-4">
                                <summary className="cursor-pointer text-sm font-semibold text-white">{q}</summary>
                                <p className="mt-2 text-sm text-slate-300">{a}</p>
                            </details>
                        ))}
                    </div>
                </section>
            </main>

            <footer id="contato" className="border-t border-white/10 bg-slate-950/80">
                <div className="mx-auto flex max-w-7xl flex-col items-center justify-between gap-3 px-4 py-8 text-sm text-slate-400 sm:px-6 md:flex-row lg:px-8">
                    <span>{sistemaNome}</span>
                    <span>&copy; {new Date().getFullYear()} {proprietario}</span>
                </div>
            </footer>
        </div>
    );
}
