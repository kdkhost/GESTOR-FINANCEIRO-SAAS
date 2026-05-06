<?php

namespace Database\Seeders;

use App\Modules\Notificacoes\Models\TemplateNotificacao;
use Illuminate\Database\Seeder;

class EmailTemplatesSeeder extends Seeder
{
    public function run(): void
    {
        $templates = [
            [
                'canal' => 'email',
                'chave' => 'cobranca_mensal',
                'nome' => 'Cobrança Mensal',
                'assunto' => 'Fatura {{numero_fatura}} - {{competencia}} - {{nome_empresa}}',
                'conteudo' => '<p>Olá,</p>
<p>Segue em anexo a fatura referente à competência <strong>{{competencia}}</strong>.</p>
<p><strong>Detalhes da Fatura:</strong></p>
<ul>
<li>Número: {{numero_fatura}}</li>
<li>Valor: R$ {{valor}}</li>
<li>Vencimento: {{vencimento}}</li>
</ul>
<p>Para efetuar o pagamento, acesse: <a href="{{link_pagamento}}">{{link_pagamento}}</a></p>
<p>Pix Copia e Cola: <code>{{pix_copia_e_cola}}</code></p>
<p>Atenciosamente,<br>Equipe {{nome_sistema}}</p>',
                'variaveis' => ['nome_empresa', 'numero_fatura', 'competencia', 'valor', 'vencimento', 'link_pagamento', 'pix_copia_e_cola', 'nome_sistema'],
                'ativo' => true,
            ],
            [
                'canal' => 'email',
                'chave' => 'fatura_vencendo',
                'nome' => 'Alerta de Fatura Vencendo',
                'assunto' => 'Lembrete: Sua fatura vence em {{dias_restantes}} dias',
                'conteudo' => '<p>Olá,</p>
<p>Este é um lembrete de que sua fatura <strong>#{{numero_fatura}}</strong> vence em <strong>{{dias_restantes}} dias</strong>.</p>
<p><strong>Detalhes:</strong></p>
<ul>
<li>Competência: {{competencia}}</li>
<li>Valor: R$ {{valor}}</li>
<li>Vencimento: {{vencimento}}</li>
</ul>
<p>Para evitar juros, efetue o pagamento até a data de vencimento: <a href="{{link_pagamento}}">{{link_pagamento}}</a></p>
<p>Atenciosamente,<br>Equipe {{nome_sistema}}</p>',
                'variaveis' => ['nome_empresa', 'numero_fatura', 'competencia', 'valor', 'vencimento', 'dias_restantes', 'link_pagamento', 'nome_sistema'],
                'ativo' => true,
            ],
            [
                'canal' => 'email',
                'chave' => 'fatura_vencida',
                'nome' => 'Fatura Vencida',
                'assunto' => 'URGENTE: Sua fatura está vencida',
                'conteudo' => '<p>Olá,</p>
<p>Informamos que sua fatura <strong>#{{numero_fatura}}</strong> está vencida.</p>
<p><strong>Detalhes:</strong></p>
<ul>
<li>Competência: {{competencia}}</li>
<li>Valor: R$ {{valor}}</li>
<li>Vencimento: {{vencimento}}</li>
<li>Dias de atraso: {{dias_atraso}}</li>
</ul>
<p>Por favor, efetue o pagamento o mais breve possível para evitar suspensão do serviço: <a href="{{link_pagamento}}">{{link_pagamento}}</a></p>
<p>Atenciosamente,<br>Equipe {{nome_sistema}}</p>',
                'variaveis' => ['nome_empresa', 'numero_fatura', 'competencia', 'valor', 'vencimento', 'dias_atraso', 'link_pagamento', 'nome_sistema'],
                'ativo' => true,
            ],
            [
                'canal' => 'email',
                'chave' => 'boas_vindas',
                'nome' => 'Boas Vindas',
                'assunto' => 'Bem-vindo ao {{nome_sistema}}!',
                'conteudo' => '<p>Olá, <strong>{{nome_responsavel}}</strong>!</p>
<p>Seja bem-vindo ao <strong>{{nome_sistema}}</strong>.</p>
<p>Sua empresa <strong>{{nome_empresa}}</strong> foi cadastrada com sucesso em nosso sistema.</p>
<p>Seus dados de acesso:</p>
<ul>
<li>Email: {{email}}</li>
</ul>
<p>Para fazer seu primeiro acesso, acesse: <a href="{{url_login}}">{{url_login}}</a></p>
<p>Se tiver alguma dúvida, entre em contato conosco.</p>
<p>Atenciosamente,<br>Equipe {{nome_sistema}}</p>',
                'variaveis' => ['nome_empresa', 'nome_responsavel', 'email', 'nome_sistema', 'url_login'],
                'ativo' => true,
            ],
        ];

        foreach ($templates as $template) {
            TemplateNotificacao::updateOrCreate(
                ['chave' => $template['chave']],
                $template
            );
        }
    }
}
