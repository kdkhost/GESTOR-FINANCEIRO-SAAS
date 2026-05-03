# Changelog

Todas as mudanças notáveis no projeto **FinanceiroSaaS** serão documentadas neste arquivo.

O formato é baseado no [Keep a Changelog](https://keepachangelog.com/pt-BR/1.0.0/), e este projeto adota o [Versionamento Semântico](https://semver.org/lang/pt-BR/).

---

## [1.1.0] - 2026-05-03

### Corrigido
- **Erro 500 crítico: `sessions` table not found** — o Laravel tenta iniciar a sessão via `StartSession` middleware antes de qualquer controller ou handler de exceção rodar. Se `SESSION_DRIVER=database` e a tabela `sessions` não existe (sistema não instalado), o erro 500 era inevitável. Corrigido no `AppServiceProvider.register()` que detecta a situação e força `file` driver antes do framework inicializar a sessão, persistindo a correção no `.env` automaticamente.
- **Erro 403 ao acessar o sistema** — o `.htaccess` raiz redirecionava para `public/` mesmo quando o servidor já apontava para esse diretório, causando loop e acesso negado.
- **Erro 500 no instalador** — o diretório `resources/views/instalador/` estava vazio.
- **`$this->middleware()` no `InstaladorController`** — método removido do Laravel 11+. Substituído por middleware dedicado `VerificarInstalacao`.
- **Rota raiz `/` sem redirecionamento** — agora redireciona para `/instalar` ou `/admin/dashboard` conforme estado da instalação.
- **Rota `/` duplicada** — removida do módulo Usuarios.
- **`SubcategoriaController` não referenciado** — faltava o `use` no arquivo de rotas do Financeiro.
- **Configuração de banco inconsistente** — `.env` apontava para MySQL enquanto `config/database.php` usava SQLite como padrão.

### Adicionado
- **`AppServiceProvider`** — lógica de auto-correção do `SESSION_DRIVER` antes da sessão ser iniciada pelo framework.
- **Middleware `VerificarInstalacao`** — bloqueia acesso às rotas de ação do instalador após instalação concluída.
- **View do instalador** (`resources/views/instalador/index.blade.php`) — interface web completa com 7 etapas responsivas.
- **13 controllers do módulo Financeiro** com implementação real: `ContaReceberController`, `ReceitaController`, `DespesaController`, `TransferenciaController`, `CategoriaController`, `SubcategoriaController`, `ContaBancariaController`, `ClienteController`, `FornecedorController`, `MetaController`, `OrcamentoController`, `RecorrenciaController`, `AnexoController`.
- **8 models do módulo Financeiro**: `Receita`, `Despesa`, `Transferencia`, `Meta`, `Orcamento`, `Recorrencia`, `Anexo`, `Fornecedor`.
- **Testes de exploração do bug** e **testes de preservação** de comportamento.

### Alterado
- `config/session.php` — padrão alterado de `database` para `file`.
- `InstaladorController` — `salvarConfiguracaoBanco()` define `SESSION_DRIVER=file` durante instalação; `finalizar()` restaura `SESSION_DRIVER=database` após migrations.
- `routes/web.php`, `Instalador/Routes/web.php`, `Usuarios/Routes/web.php`, `Financeiro/Routes/web.php` — corrigidos e reestruturados.

---

## [1.0.0] - 2026-05-02
### Adicionado
- Fundação completa da arquitetura em Laravel 13 com suporte ao PHP 8.5.
- Estrutura de banco de dados otimizada para MySQL/MariaDB.
- Sistema multiusuário isolado (multitenant básico focado no `user_id`).
- Integração da arquitetura Modular com `ModuleServiceProvider` auto-discovery.
- Módulo Instalador Web de 11 passos para deploy automatizado em hospedagens cPanel.
- Integração e configuração do pacote `spatie/laravel-permission` para controle de acesso RBAC.
- Helpers customizados para padrões brasileiros: CPF, CNPJ, Moeda BRL e Data (`BrasilHelper.php`).
- Módulo global de Auditoria interceptando ações e persistindo na base de dados (`AuditoriaHelper.php`).
- Design System completo implementando AdminLTE 4 com TailwindCSS v4.
- Implementação inicial de Single Page Application interativo mesclando Vue 3 e React 19 no frontend (via Vite 7).
- Painel Dashboard com 23 KPIs e cálculo da engine analítica `SaudeFinanceiraService` (0 a 100 pontos).
- Tela de Login customizada, com proteção *Rate Limiting* e mecanismo de reset de senha via PHPMailer nativo.
- Banco de componentes configurado: Toastify JS (notificações), SweetAlert2 (confirmações), FullCalendar, Chart.js.
- Ambiente `composer` e `npm` pré-compilados e estabilizados.
