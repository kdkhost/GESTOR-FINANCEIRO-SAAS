# Changelog

Todas as mudanças notáveis no projeto **FinanceiroSaaS** serão documentadas neste arquivo.

O formato é baseado no [Keep a Changelog](https://keepachangelog.com/pt-BR/1.0.0/), e este projeto adota o [Versionamento Semântico](https://semver.org/lang/pt-BR/).

---

## [1.2.2] - 2026-05-04

### Adicionado
- Módulo administrativo **Gestão de Módulos** (`/admin/modulos`) com listagem paginada AJAX, filtros, criação, edição, ativação/desativação e remoção de módulos adicionais.
- Estrutura de dados `modulos` com suporte a módulos nativos e desacoplados adicionais.
- Novo item **Módulos** no menu de administração do AdminLTE.

### Alterado
- `ModuleServiceProvider` agora:
  - registra/carrega o módulo nativo `Modulos`;
  - carrega dinamicamente módulos adicionais ativos cadastrados no banco;
  - sincroniza automaticamente os módulos nativos na tabela `modulos` quando disponível.

### Validado
- `php artisan migrate --force` executado com sucesso (migration `create_modulos_table` aplicada).
- `php artisan route:list --path=admin/modulos` confirmou 7 rotas do novo módulo.

---

## [1.2.1] - 2026-05-04

### Corrigido
- A rota raiz com o sistema instalado agora redireciona para `/admin/dashboard`, permitindo o fluxo direto para login/dashboard do superadmin.
- Logout e redefinição de senha agora redirecionam para a rota real `login`, removendo referência a alias inexistente.
- `public/index.php` voltou a usar os caminhos padrão do Laravel (`../vendor` e `../bootstrap`), corrigindo o acesso via `php artisan serve`.
- `storage/installed` e `deploy_sync.tar` passaram a ser ignorados pelo Git por serem arquivos de estado/artefato local.

### Validado
- `php artisan test` com 15 testes passando.
- `npm run build` concluído com sucesso.
- `php artisan route:list` confirmou login, dashboard e configurações gerais carregados.
- Login superadmin local validado por HTTP e acesso a `/admin/configuracoes` confirmado.

---

## [1.2.0] - 2026-05-04

### Adicionado
- Instalador web completo substituindo o placeholder, com etapas para requisitos, permissões de pastas, banco MariaDB/MySQL, migrations, superadmin, seeders, permissões internas, storage link, configuração inicial e finalização.
- Endpoints reais do instalador para `seeders` e publicação de permissões do sistema.
- Módulo administrativo de permissões com matriz granular de roles/permissões, CRUD AJAX de papéis e atribuição de roles por usuário.
- Migration própria das tabelas do Spatie Permission, evitando publicação duplicada durante a instalação.
- Seeds padrão para permissões, papéis, categorias, formas de pagamento e crons iniciais.

### Alterado
- Requisito do projeto ajustado para PHP `^8.4`.
- Provider de autenticação passou a usar o model modular `App\Modules\Usuarios\Models\User`, compatível com roles do Spatie.
- Configuração padrão de banco passou a priorizar MySQL/MariaDB fora do ambiente de testes.
- Testes antigos de bug foram convertidos para preservar o comportamento corrigido do instalador.

### Validado
- `php artisan test` com 15 testes passando.
- `php artisan route:list` carregando 138 rotas.
- `npm run build` concluído com sucesso.
- `composer validate --strict` sem erros.

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
