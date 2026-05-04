import React from 'react';

export default function PremiumLanding({ props }) {
    const {
        sistemaNome,
        sistemaDescricao,
        proprietario,
        logo,
        linkLogin,
        linkCadastro,
        linkPainel,
        instalado,
        autenticado,
    } = props;

    return (
        <div className="min-h-screen bg-[radial-gradient(circle_at_top_right,_#1d4ed8_0%,_#020617_45%,_#020617_100%)]">
            <header className="sticky top-0 z-40 border-b border-white/10 bg-slate-950/80 backdrop-blur">
                <nav className="mx-auto flex h-20 w-full max-w-7xl items-center justify-between px-4 sm:px-6 lg:px-8">
                    <div className="flex items-center gap-3">
                        {logo ? (
                            <img src={logo} alt={sistemaNome} className="h-10 w-10 rounded-xl object-cover ring-1 ring-white/20" />
                        ) : (
                            <div className="flex h-10 w-10 items-center justify-center rounded-xl bg-blue-500 font-bold text-white">S</div>
                        )}
                        <span className="text-xl font-semibold tracking-tight">{sistemaNome}</span>
                    </div>
                    <div className="hidden items-center gap-8 md:flex text-sm text-slate-300">
                        <a href="#recursos" className="hover:text-white">Recursos</a>
                        <a href="#planos" className="hover:text-white">Planos</a>
                        <a href="#contato" className="hover:text-white">Contato</a>
                    </div>
                    <div className="flex items-center gap-3">
                        {instalado ? (
                            autenticado ? (
                                <a href={linkPainel} className="rounded-lg border border-white/20 px-4 py-2 text-sm hover:bg-white/10">Acessar Painel</a>
                            ) : (
                                <>
                                    <a href={linkLogin} className="rounded-lg border border-white/20 px-4 py-2 text-sm hover:bg-white/10">Login</a>
                                    <a href={linkCadastro} className="rounded-lg bg-blue-500 px-4 py-2 text-sm font-semibold text-white hover:bg-blue-400">Cadastre-se</a>
                                </>
                            )
                        ) : (
                            <a href="/instalar" className="rounded-lg bg-rose-600 px-4 py-2 text-sm font-semibold text-white hover:bg-rose-500">Iniciar Instalação</a>
                        )}
                    </div>
                </nav>
            </header>

            <main>
                <section className="mx-auto grid max-w-7xl gap-10 px-4 pb-20 pt-20 sm:px-6 lg:grid-cols-2 lg:px-8 lg:pt-28">
                    <div>
                        <p className="mb-4 inline-flex rounded-full border border-blue-400/40 bg-blue-500/10 px-3 py-1 text-xs uppercase tracking-widest text-blue-200">Premium SaaS</p>
                        <h1 className="text-5xl font-semibold leading-tight tracking-tight text-white lg:text-6xl">{sistemaNome}</h1>
                        <p className="mt-6 max-w-xl text-lg leading-relaxed text-slate-300">{sistemaDescricao}</p>
                        <div className="mt-8 flex gap-3">
                            <a href={linkCadastro} className="rounded-xl bg-white px-6 py-3 text-sm font-semibold text-slate-900 hover:bg-slate-200">Começar agora</a>
                            <a href="#recursos" className="rounded-xl border border-white/20 px-6 py-3 text-sm font-semibold hover:bg-white/10">Ver funcionalidades</a>
                        </div>
                    </div>
                    <div className="rounded-2xl border border-white/10 bg-white/5 p-6 shadow-2xl shadow-black/30">
                        <div className="mb-4 text-sm text-slate-300">Visão executiva em tempo real</div>
                        <div className="grid grid-cols-2 gap-4">
                            <div className="rounded-xl bg-emerald-500/10 p-4 ring-1 ring-emerald-400/20">
                                <div className="text-xs text-emerald-300">MRR</div>
                                <div className="mt-2 text-2xl font-semibold text-white">R$ 98.420</div>
                            </div>
                            <div className="rounded-xl bg-violet-500/10 p-4 ring-1 ring-violet-400/20">
                                <div className="text-xs text-violet-300">Conversão</div>
                                <div className="mt-2 text-2xl font-semibold text-white">18,7%</div>
                            </div>
                            <div className="rounded-xl bg-cyan-500/10 p-4 ring-1 ring-cyan-400/20">
                                <div className="text-xs text-cyan-300">Leads Ativos</div>
                                <div className="mt-2 text-2xl font-semibold text-white">1.284</div>
                            </div>
                            <div className="rounded-xl bg-amber-500/10 p-4 ring-1 ring-amber-400/20">
                                <div className="text-xs text-amber-300">NPS</div>
                                <div className="mt-2 text-2xl font-semibold text-white">74</div>
                            </div>
                        </div>
                    </div>
                </section>

                <section id="recursos" className="border-y border-white/10 bg-slate-900/60">
                    <div className="mx-auto max-w-7xl px-4 py-16 sm:px-6 lg:px-8">
                        <div className="grid gap-4 md:grid-cols-3">
                            {['Pipeline CRM', 'Financeiro Integrado', 'Automação de Cobrança'].map((title) => (
                                <article key={title} className="rounded-2xl border border-white/10 bg-white/5 p-6">
                                    <h3 className="text-lg font-semibold text-white">{title}</h3>
                                    <p className="mt-2 text-sm text-slate-300">Fluxos completos com gestão comercial, tarefas, clientes, cobranças e indicadores em uma única plataforma.</p>
                                </article>
                            ))}
                        </div>
                    </div>
                </section>

                <section id="planos" className="mx-auto max-w-7xl px-4 py-16 sm:px-6 lg:px-8">
                    <h2 className="text-3xl font-semibold text-white">Planos</h2>
                    <div className="mt-8 grid gap-4 md:grid-cols-3">
                        {[
                            { nome: 'Starter', preco: 'R$ 89/mês' },
                            { nome: 'Growth', preco: 'R$ 199/mês' },
                            { nome: 'Scale', preco: 'R$ 399/mês' },
                        ].map((plano) => (
                            <div key={plano.nome} className="rounded-2xl border border-white/10 bg-white/[0.03] p-6">
                                <h3 className="text-lg font-semibold text-white">{plano.nome}</h3>
                                <p className="mt-2 text-2xl font-semibold text-blue-300">{plano.preco}</p>
                                <a href={linkCadastro} className="mt-6 inline-flex rounded-lg bg-blue-500 px-4 py-2 text-sm font-semibold text-white hover:bg-blue-400">Assinar</a>
                            </div>
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

