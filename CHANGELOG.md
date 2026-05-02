# Changelog

Todas as mudanças notáveis no projeto **FinanceiroSaaS** serão documentadas neste arquivo.

O formato é baseado no [Keep a Changelog](https://keepachangelog.com/pt-BR/1.0.0/), e este projeto adota o [Versionamento Semântico](https://semver.org/lang/pt-BR/).

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
