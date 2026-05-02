# 🚀 Deploy Contínuo no cPanel (Git + Repositório Privado)

Para garantir que você possa fazer atualizações e correções do **FinanceiroSaaS** utilizando `git pull` de um repositório privado (GitHub, GitLab, Bitbucket) diretamente no seu cPanel, **sem erros de autenticação ou permissão**, siga rigorosamente as etapas abaixo.

---

## Passo 1: Gerar a Chave SSH no cPanel

Como o repositório é privado, o cPanel precisa de uma "identidade" segura para ter permissão de leitura no repositório sem precisar digitar senha toda vez.

1. Acesse seu painel **cPanel**.
2. Vá até a seção **Segurança** e clique em **Acesso SSH**.
3. Clique em **Gerenciar chaves SSH**.
4. Clique em **Gerar uma nova chave**.
   - **Nome da Chave:** `id_rsa` (deixe o padrão).
   - **Senha da chave:** Deixe **EM BRANCO** (se colocar senha, o processo automático exigirá ela a cada pull, travando o deploy).
   - **Tipo da Chave:** `RSA`
   - **Tamanho da Chave:** `4096`
5. Clique em **Gerar Chave**.
6. Volte à tela anterior. Na seção "Chaves Públicas", você verá a chave recém-criada. Na coluna "Status da autorização", clique em **Gerenciar** e depois no botão **Authorize** (Autorizar).

---

## Passo 2: Copiar a Chave Pública para o GitHub/GitLab

O cPanel já tem a chave, agora o GitHub precisa conhecê-la para liberar o acesso.

1. Ainda na tela **Gerenciar chaves SSH** do cPanel.
2. Na aba **Chaves Públicas**, encontre a chave `id_rsa` que você acabou de gerar e clique em **Exibir/Baixar**.
3. **Copie todo o texto** da chave pública (geralmente começa com `ssh-rsa ...`).

**No seu repositório remoto (exemplo GitHub):**
1. Acesse o repositório privado do FinanceiroSaaS no GitHub.
2. Vá em **Settings** (Configurações) > **Deploy keys** (Chaves de implantação).
3. Clique em **Add deploy key**.
   - **Title:** `cPanel Produção` (ou outro nome identificável).
   - **Key:** Cole o código da chave pública copiado do cPanel.
   - **Allow write access:** **Não marque** (o cPanel só precisa ler e puxar código, não alterar).
4. Salve.

---

## Passo 3: Clonar o repositório via "Git Version Control" no cPanel

1. Acesse a tela principal do **cPanel**.
2. Vá na seção **Arquivos** e clique em **Git™ Version Control**.
3. Clique em **Create** (Criar).
4. Preencha os campos da seguinte forma:
   - **Clone URL:** *IMPORTANTE!* Use a URL do SSH, não a HTTPS. (Exemplo: `git@github.com:seu-usuario/seu-repositorio.git`).
   - **Repository Path:** O caminho onde o sistema vai ficar no seu servidor (ex: `/financeirosaas`).
   - **Repository Name:** O nome para identificação no painel cPanel (ex: `Financeiro SaaS`).
5. Clique em **Create**.

Se a chave SSH estiver correta no passo 2, o cPanel não pedirá senha e clonará todo o código do seu repositório privado instantaneamente.

---

## Passo 4: Como atualizar o sistema (`git pull`)

Sempre que você fizer alterações no código (por exemplo, na sua máquina local com o VSCode) e enviar (`git push`) para o GitHub, você precisará atualizar o cPanel.

**Método 1: Pelo painel cPanel (Interface Web)**
1. Vá em **Git™ Version Control**.
2. Encontre o repositório "Financeiro SaaS" e clique em **Manage** (Gerenciar).
3. Na aba **Pull or Deploy**, clique em **Update from Remote**. 
4. O cPanel fará o `git pull` perfeitamente!

**Método 2: Pelo Terminal SSH do cPanel**
1. Abra a opção **Terminal** no cPanel.
2. Acesse a pasta do projeto:
   ```bash
   cd ~/financeirosaas
   ```
3. Rode o comando de pull:
   ```bash
   git pull origin main
   ```

---

## Dica Pro: Executando comandos Laravel automaticamente após o Pull

Você pode criar um arquivo `.cpanel.yml` na raiz do seu projeto (adicione no VSCode e faça commit) para rodar os comandos vitais sempre que você clicar em "Update from Remote" no cPanel.

Crie um arquivo `.cpanel.yml` no seu código com o seguinte:

```yaml
---
deployment:
  tasks:
    - export PATH=/opt/cpanel/ea-php82/root/usr/bin:$PATH
    - composer install --no-dev --optimize-autoloader
    - php artisan migrate --force
    - php artisan optimize:clear
```
> *Atenção:* O caminho do PHP (`ea-php82`) pode variar (ex: `ea-php85` dependendo do servidor da hospedagem). 

Pronto! Configuração imune a bugs de credenciais concluída.
