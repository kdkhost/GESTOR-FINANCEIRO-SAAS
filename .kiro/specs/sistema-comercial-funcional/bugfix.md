# Bugfix Requirements Document

## Introduction

O sistema Laravel modular não está funcional devido a problemas críticos que impedem a instalação e inicialização. O instalador não consegue ser acessado porque faltam as views necessárias, há inconsistências na configuração do banco de dados, e o sistema não consegue completar o processo de instalação inicial. Isso torna o sistema completamente inutilizável para fins comerciais.

## Bug Analysis

### Current Behavior (Defect)

1.1 WHEN o usuário acessa a rota `/instalar` THEN o sistema retorna erro 500 porque a view `instalador.index` não existe

1.2 WHEN o sistema tenta carregar o instalador THEN ocorre falha porque o diretório `resources/views/instalador` está vazio

1.3 WHEN o sistema verifica a configuração do banco THEN há conflito entre o .env (configurado para MySQL) e o database.php (padrão SQLite)

1.4 WHEN o usuário acessa a rota raiz `/` THEN o redirecionamento para o instalador falha devido aos problemas das views

1.5 WHEN o sistema tenta executar migrations durante a instalação THEN pode falhar devido à configuração inconsistente do banco de dados

1.6 WHEN o instalador tenta verificar requisitos do sistema THEN pode retornar informações incorretas sobre extensões SQLite vs MySQL

### Expected Behavior (Correct)

2.1 WHEN o usuário acessa a rota `/instalar` THEN o sistema SHALL exibir a interface completa do instalador com todas as etapas funcionais

2.2 WHEN o sistema carrega o instalador THEN SHALL apresentar uma interface web moderna e intuitiva para guiar o processo de instalação

2.3 WHEN o sistema verifica a configuração do banco THEN SHALL usar consistentemente SQLite como configurado no sistema

2.4 WHEN o usuário acessa a rota raiz `/` THEN SHALL redirecionar corretamente para o instalador funcional se não estiver instalado

2.5 WHEN o sistema executa migrations durante a instalação THEN SHALL completar com sucesso usando a configuração correta do banco

2.6 WHEN o instalador verifica requisitos THEN SHALL validar corretamente as extensões necessárias para SQLite

### Unchanged Behavior (Regression Prevention)

3.1 WHEN o sistema já está instalado (arquivo `storage/installed` existe) THEN SHALL CONTINUE TO redirecionar para o dashboard administrativo

3.2 WHEN as rotas dos módulos são carregadas THEN SHALL CONTINUE TO funcionar através do ModuleServiceProvider

3.3 WHEN o sistema usa a arquitetura modular THEN SHALL CONTINUE TO manter a estrutura desacoplada existente

3.4 WHEN o sistema processa autenticação e permissões THEN SHALL CONTINUE TO usar os módulos Usuarios e Permissoes existentes

3.5 WHEN o sistema executa operações do módulo Financeiro THEN SHALL CONTINUE TO funcionar com a estrutura atual de Controllers, Models e Services