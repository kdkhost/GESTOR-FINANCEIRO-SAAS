# FinanceiroSaaS

> Sistema financeiro de gestão modular multiusuário — **Marcelo Brad RJ**

[![PHP](https://img.shields.io/badge/PHP-8.5-blue)](https://php.net)
[![Laravel](https://img.shields.io/badge/Laravel-13-red)](https://laravel.com)
[![License](https://img.shields.io/badge/license-proprietary-gray)]()

---

## Sobre o sistema

**FinanceiroSaaS** é um sistema financeiro completo para gestão pessoal e empresarial.
Modular, desacoplado, multiusuário, seguro, escalável e preparado para hospedagem compartilhada cPanel.

---

## Stack tecnológica

| Componente | Tecnologia |
|---|---|
| Backend | PHP 8.5 + Laravel 13 |
| Banco de dados | MySQL / MariaDB |
| Admin UI | AdminLTE 4 |
| Frontend | TailwindCSS v4 + Vue 3 + React 19 |
| Bundler | Vite 7 |
| Permissões | Spatie Laravel Permission |
| PDF | DomPDF |
| Excel | PhpSpreadsheet |
| E-mail | PHPMailer |
| Uploads | FilePond |
| Gráficos | Chart.js + ApexCharts |
| Calendário | FullCalendar 6 |
| Notificações | Toastify JS |
| Confirmações | SweetAlert2 |
| Máscaras | IMask.js |

---

## Instalação local

```
1. Clone o repositório
2. composer install
3. cp .env.example .env && php artisan key:generate
4. Configure .env com dados do MySQL
5. php artisan migrate --seed
6. npm install && npm run build
7. php artisan storage:link
8. php artisan serve
```

Acesse o instalador em: http://localhost:8000/instalar

---

## Deploy cPanel

Veja: DEPLOY_CPANEL.md

---

## Módulos

| Módulo | Descrição |
|---|---|
| Financeiro | Contas a pagar/receber, receitas, despesas, transferências |
| Usuarios | Autenticação, perfil, sessões ativas |
| Permissoes | RBAC com Roles, Permissions, Policies |
| Relatorios | Fluxo de caixa, DRE, evolução mensal, inadimplência |
| Configuracoes | Nome, logo, SMTP, tema, cores |
| Pwa | Manifest dinâmico, service worker, cache offline |
| Cron | Gestão de tarefas agendadas pelo painel |
| Auditoria | Log completo de todas as ações |
| Integracoes | Gateways, webhooks, ViaCEP, Evolution Go |
| Instalador | Instalação web em 11 etapas |

---

## Proprietário

Marcelo Brad RJ. Todos os direitos reservados (c) 2025.
