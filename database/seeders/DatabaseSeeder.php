<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Carbon\Carbon;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        $this->call([
            ConfiguracoesSeeder::class,
            FormasPagamentoSeeder::class,
            CategoriasSeeder::class,
            CronJobsSeeder::class,
        ]);
    }
}

// -------------------------------------------------------

class ConfiguracoesSeeder extends Seeder
{
    public function run(): void
    {
        $configuracoes = [
            // Geral
            ['grupo' => 'geral', 'chave' => 'sistema_nome',         'valor' => 'FinanceiroSaaS',             'tipo' => 'texto',    'label' => 'Nome do Sistema'],
            ['grupo' => 'geral', 'chave' => 'sistema_versao',       'valor' => '1.0.0',                      'tipo' => 'texto',    'label' => 'Versão'],
            ['grupo' => 'geral', 'chave' => 'sistema_proprietario', 'valor' => 'Marcelo Brad RJ',            'tipo' => 'texto',    'label' => 'Proprietário'],
            ['grupo' => 'geral', 'chave' => 'sistema_descricao',    'valor' => 'Gestão Financeira Pessoal e Empresarial', 'tipo' => 'texto', 'label' => 'Descrição'],
            ['grupo' => 'geral', 'chave' => 'sistema_logo',         'valor' => '',                           'tipo' => 'arquivo',  'label' => 'Logo'],
            ['grupo' => 'geral', 'chave' => 'sistema_favicon',      'valor' => 'favicon.ico',                'tipo' => 'arquivo',  'label' => 'Favicon'],
            ['grupo' => 'geral', 'chave' => 'sistema_timezone',     'valor' => 'America/Sao_Paulo',          'tipo' => 'texto',    'label' => 'Fuso Horário'],
            ['grupo' => 'geral', 'chave' => 'sistema_moeda',        'valor' => 'BRL',                        'tipo' => 'texto',    'label' => 'Moeda Padrão'],
            ['grupo' => 'geral', 'chave' => 'instalacao_concluida', 'valor' => '0',                          'tipo' => 'booleano', 'label' => 'Instalação Concluída'],

            // Aparência
            ['grupo' => 'aparencia', 'chave' => 'tema_cor_primaria', 'valor' => '#3b82f6', 'tipo' => 'texto', 'label' => 'Cor Primária'],
            ['grupo' => 'aparencia', 'chave' => 'tema_cor_sucesso',  'valor' => '#22c55e', 'tipo' => 'texto', 'label' => 'Cor Sucesso'],
            ['grupo' => 'aparencia', 'chave' => 'tema_cor_perigo',   'valor' => '#ef4444', 'tipo' => 'texto', 'label' => 'Cor Perigo'],

            // Segurança
            ['grupo' => 'seguranca', 'chave' => 'max_tentativas_login',    'valor' => '5',    'tipo' => 'numero',   'label' => 'Máx. tentativas login'],
            ['grupo' => 'seguranca', 'chave' => 'minutos_bloqueio_login',  'valor' => '15',   'tipo' => 'numero',   'label' => 'Minutos de bloqueio'],
            ['grupo' => 'seguranca', 'chave' => 'auditoria_ativa',         'valor' => '1',    'tipo' => 'booleano', 'label' => 'Auditoria ativa'],
            ['grupo' => 'seguranca', 'chave' => 'session_lifetime',        'valor' => '120',  'tipo' => 'numero',   'label' => 'Tempo de sessão (min)'],

            // SMTP
            ['grupo' => 'smtp', 'chave' => 'smtp_host',      'valor' => '', 'tipo' => 'texto',  'label' => 'Host SMTP',        'sensivel' => true],
            ['grupo' => 'smtp', 'chave' => 'smtp_porta',     'valor' => '587', 'tipo' => 'numero', 'label' => 'Porta SMTP',    'sensivel' => true],
            ['grupo' => 'smtp', 'chave' => 'smtp_usuario',   'valor' => '', 'tipo' => 'texto',  'label' => 'Usuário SMTP',     'sensivel' => true],
            ['grupo' => 'smtp', 'chave' => 'smtp_senha',     'valor' => '', 'tipo' => 'texto',  'label' => 'Senha SMTP',       'sensivel' => true],
            ['grupo' => 'smtp', 'chave' => 'smtp_criptografia', 'valor' => 'tls', 'tipo' => 'texto', 'label' => 'Criptografia', 'sensivel' => true],
            ['grupo' => 'smtp', 'chave' => 'smtp_remetente', 'valor' => '', 'tipo' => 'texto',  'label' => 'E-mail Remetente', 'sensivel' => true],
            ['grupo' => 'smtp', 'chave' => 'smtp_nome_remetente', 'valor' => 'FinanceiroSaaS', 'tipo' => 'texto', 'label' => 'Nome Remetente'],

            // PWA
            ['grupo' => 'pwa', 'chave' => 'pwa_nome',       'valor' => 'FinanceiroSaaS',  'tipo' => 'texto',  'label' => 'Nome do App'],
            ['grupo' => 'pwa', 'chave' => 'pwa_nome_curto', 'valor' => 'Financeiro',      'tipo' => 'texto',  'label' => 'Nome Curto'],
            ['grupo' => 'pwa', 'chave' => 'pwa_descricao',  'valor' => 'Gestão Financeira', 'tipo' => 'texto','label' => 'Descrição'],
            ['grupo' => 'pwa', 'chave' => 'pwa_cor_tema',   'valor' => '#3b82f6',          'tipo' => 'texto',  'label' => 'Cor do Tema'],
            ['grupo' => 'pwa', 'chave' => 'pwa_cor_fundo',  'valor' => '#ffffff',          'tipo' => 'texto',  'label' => 'Cor de Fundo'],
            ['grupo' => 'pwa', 'chave' => 'pwa_exibicao',   'valor' => 'standalone',       'tipo' => 'texto',  'label' => 'Modo de Exibição'],
            ['grupo' => 'pwa', 'chave' => 'pwa_ativo',      'valor' => '1',                'tipo' => 'booleano', 'label' => 'PWA Ativo'],
        ];

        foreach ($configuracoes as $config) {
            DB::table('configuracoes')->updateOrInsert(
                ['chave' => $config['chave']],
                array_merge($config, [
                    'sensivel'   => $config['sensivel'] ?? false,
                    'visivel'    => true,
                    'descricao'  => $config['descricao'] ?? null,
                    'created_at' => Carbon::now(),
                    'updated_at' => Carbon::now(),
                ])
            );
        }
    }
}
