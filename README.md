# FinanceiroSaaS

> Sistema financeiro de gestão modular multiusuário — **Marcelo Brad RJ**

[![PHP](https://img.shields.io/badge/PHP-8.4-blue)](https://php.net)
[![Laravel](https://img.shields.io/badge/Laravel-13-red)](https://laravel.com)
[![Versão](https://img.shields.io/badge/versão-1.1.0-green)]()
[![License](https://img.shields.io/badge/license-proprietary-gray)]()

---

## Sobre o sistema

**FinanceiroSaaS** é um sistema financeiro completo para gestão pessoal e empresarial.
Modular, desacoplado, multiusuário, seguro, escalável e preparado para hospedagem compartilhada cPanel.

Cada módulo é completamente independente, carregado automaticamente pelo `ModuleServiceProvider`,
com seus próprios Controllers, Models, Services, Routes e Views.

---

## Stack tecnológica

| Componente       | Tecnologia                        |
|------------------|-----------------------------------|
| Backend          | PHP 8.4 + Laravel 13              |
| Banco de dados   | MySQL / MariaDB                   |
| Admin UI         | AdminLTE 4 + Bootstrap 5          |
| Frontend         | TailwindCSS v4 + Vue 3 + React 19 |
| Bundler          | Vite 7                            |
| Permissões       | Spatie Laravel Permission         |
| PDF              | DomPDF                            |
| Excel            | PhpSpreadsheet                    |
| E-mail           | PHPMailer                         |
| Uploads          | FilePond                          |
| Gráficos         | Chart.js + ApexCharts             |
| Calendário       | FullCalendar 6                    |
| Notificações     | Toastify JS                       |
| Confirmações     | SweetAlert2                       |
| Máscaras         | IMask.js                          |

---

## Instalação local

```bash
# 1. Clone o repositório
git clone <url-do-repositorio>
cd gestor-financeiro-saas

# 2. Instale as dependências PHP
composer install

# 3. Configure o ambiente
cp .env.example .env
php artisan key:generate

# 4. Configure o banco de dados no .env
# DB_HOST, DB_DATABASE, DB_USERNAME, DB_PASSWORD

# 5. Instale as dependências JS e compile os assets
npm install && npm run build

# 6. Inicie o servidor
php artisan serve
```

Acesse o instalador em: **http://localhost:8000/instalar**

O instalador web guia você por 7 etapas:
1. Verificação de requisitos do servidor
2. Verificação de permissões de pastas
3. Configuração do banco de dados MySQL
4. Execução das migrations
5. Criação do superadministrador
6. Configurações do sistema (nome, SMTP)
7. Finalização

> **Importante:** O sistema usa `SESSION_DRIVER=file` durante a instalação
> e muda automaticamente para `SESSION_DRIVER=database` após as migrations.
> Isso evita o erro 500 causado pela tabela `sessions` inexistente.

---

## Deploy em cPanel

Veja: `DEPLOY_CPANEL.md`

**Resumo rápido:**
1. Faça upload dos arquivos (exceto `vendor/` e `node_modules/`)
2. Configure o `document root` para apontar para a pasta `public/`
3. Configure o `.env` com os dados do banco de dados do cPanel
4. Execute `composer install --no-dev` via SSH ou terminal do cPanel
5. Acesse `https://seudominio.com/instalar` e siga o instalador

---

## Módulos

| Módulo          | Status     | Descrição                                                        |
|-----------------|------------|------------------------------------------------------------------|
| **Instalador**  | ✅ Completo | Instalação web em 7 etapas com interface responsiva              |
| **Usuarios**    | ✅ Completo | Autenticação, perfil, sessões ativas, reset de senha             |
| **Financeiro**  | ✅ Completo | Contas a pagar/receber, receitas, despesas, transferências       |
| **Permissoes**  | 🔧 Estrutura | RBAC com Roles, Permissions, Policies (Spatie)                  |
| **Configuracoes**| 🔧 Estrutura | Nome, logo, SMTP, tema, cores                                   |
| **Relatorios**  | 🔧 Estrutura | Fluxo de caixa, DRE, evolução mensal, inadimplência             |
| **Auditoria**   | ✅ Completo | Log completo de todas as ações do sistema                        |
| **Pwa**         | 🔧 Estrutura | Manifest dinâmico, service worker, cache offline                |
| **Cron**        | 🔧 Estrutura | Gestão de tarefas agendadas pelo painel                         |
| **Integracoes** | 🔧 Estrutura | Gateways, webhooks, ViaCEP, Evolution Go                        |

---

## Módulo Financeiro — Controllers disponíveis

| Controller               | Operações                                              |
|--------------------------|--------------------------------------------------------|
| `DashboardController`    | KPIs, saúde financeira, gráficos                       |
| `ContaPagarController`   | CRUD + pagar + cancelar                                |
| `ContaReceberController` | CRUD + receber (parcial/total)                         |
| `ReceitaController`      | CRUD com filtros por período e categoria               |
| `DespesaController`      | CRUD com filtros por período e categoria               |
| `TransferenciaController`| Criar, listar, excluir entre contas                    |
| `CategoriaController`    | CRUD com tipo receita/despesa/ambos                    |
| `SubcategoriaController` | CRUD vinculado à categoria pai                         |
| `ContaBancariaController`| CRUD + ajuste de saldo                                 |
| `ClienteController`      | CRUD + busca AJAX por nome                             |
| `FornecedorController`   | CRUD + busca AJAX por nome                             |
| `MetaController`         | CRUD com percentual de progresso automático            |
| `OrcamentoController`    | CRUD por mês/ano/categoria                             |
| `RecorrenciaController`  | CRUD com tipo pagar/receber e dia de vencimento        |
| `AnexoController`        | Upload real + exclusão com limpeza do disco            |

---

## Arquitetura modular

```
app/
├── Http/
│   └── Middleware/
│       └── VerificarInstalacao.php   ← bloqueia instalador após instalação
├── Modules/
│   ├── Financeiro/
│   │   ├── Controllers/              ← 15 controllers implementados
│   │   ├── Models/                   ← 10 models com relacionamentos
│   │   ├── Services/                 ← DashboardService, SaudeFinanceiraService
│   │   └── Routes/web.php
│   ├── Usuarios/
│   │   ├── Controllers/              ← AuthController, PerfilController
│   │   ├── Models/User.php
│   │   └── Routes/web.php
│   ├── Instalador/
│   │   ├── Controllers/InstaladorController.php
│   │   └── Routes/web.php
│   └── [outros módulos...]
└── Providers/
    └── ModuleServiceProvider.php     ← auto-discovery de rotas por módulo
```

---

## Helpers globais (BrasilHelper)

```php
moeda_br(1234.56)        // "R$ 1.234,56"
moeda_para_float("1.234,56") // 1234.56
data_br("2026-05-03")    // "03/05/2026"
data_banco("03/05/2026") // "2026-05-03"
formatar_cpf("12345678901")  // "123.456.789-01"
formatar_cnpj("12345678000195") // "12.345.678/0001-95"
configuracao('sistema_nome', 'Padrão') // lê da tabela configuracoes
auditoria('criou', 'Modulo', 'tabela', $id, $antes, $depois)
```

---

## Variáveis de ambiente importantes

```dotenv
# Banco de dados
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=nome_do_banco
DB_USERNAME=usuario
DB_PASSWORD=senha

# Sessão — use 'file' antes das migrations, 'database' após
SESSION_DRIVER=file

# Cache — use 'file' antes das migrations, 'database' após
CACHE_STORE=file

# Segurança
MAX_LOGIN_ATTEMPTS=5
LOGIN_LOCKOUT_MINUTES=15
AUDIT_ENABLED=true
```

---

## Proprietário

**Marcelo Brad RJ** — Todos os direitos reservados © 2026.