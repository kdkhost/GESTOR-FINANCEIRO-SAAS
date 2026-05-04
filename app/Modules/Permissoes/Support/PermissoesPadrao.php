<?php

namespace App\Modules\Permissoes\Support;

class PermissoesPadrao
{
    public static function grupos(): array
    {
        return [
            'Dashboard' => [
                'dashboard.visualizar' => 'Visualizar dashboard',
            ],
            'Financeiro' => [
                'financeiro.visualizar' => 'Visualizar financeiro',
                'financeiro.criar' => 'Criar lancamentos',
                'financeiro.editar' => 'Editar lancamentos',
                'financeiro.excluir' => 'Excluir lancamentos',
                'financeiro.pagar' => 'Registrar pagamentos',
                'financeiro.receber' => 'Registrar recebimentos',
                'financeiro.exportar' => 'Exportar dados financeiros',
            ],
            'Cadastros' => [
                'cadastros.visualizar' => 'Visualizar cadastros',
                'cadastros.criar' => 'Criar cadastros',
                'cadastros.editar' => 'Editar cadastros',
                'cadastros.excluir' => 'Excluir cadastros',
            ],
            'Relatorios' => [
                'relatorios.visualizar' => 'Visualizar relatorios',
                'relatorios.exportar' => 'Exportar relatorios',
            ],
            'Usuarios' => [
                'usuarios.visualizar' => 'Visualizar usuarios',
                'usuarios.criar' => 'Criar usuarios',
                'usuarios.editar' => 'Editar usuarios',
                'usuarios.excluir' => 'Excluir usuarios',
            ],
            'Permissoes' => [
                'permissoes.visualizar' => 'Visualizar permissoes',
                'permissoes.gerenciar' => 'Gerenciar roles e permissoes',
            ],
            'Configuracoes' => [
                'configuracoes.visualizar' => 'Visualizar configuracoes',
                'configuracoes.editar' => 'Editar configuracoes',
            ],
            'Integracoes' => [
                'integracoes.visualizar' => 'Visualizar integracoes',
                'integracoes.editar' => 'Editar integracoes',
            ],
            'PWA' => [
                'pwa.gerenciar' => 'Gerenciar PWA',
            ],
            'Cron' => [
                'cron.visualizar' => 'Visualizar crons',
                'cron.editar' => 'Editar crons',
                'cron.executar' => 'Executar crons',
            ],
            'Auditoria' => [
                'auditoria.visualizar' => 'Visualizar auditoria',
            ],
            'SaaS' => [
                'saas.visualizar' => 'Visualizar SaaS',
                'saas.empresas' => 'Gerenciar empresas (tenants)',
                'saas.planos' => 'Gerenciar planos',
                'saas.assinaturas' => 'Gerenciar assinaturas',
                'saas.faturas' => 'Gerenciar faturas',
                'saas.notificacoes' => 'Gerenciar templates de notificacao',
                'saas.manutencao' => 'Gerenciar manutencao',
            ],
        ];
    }

    public static function nomes(): array
    {
        return collect(self::grupos())
            ->flatMap(fn (array $permissoes) => array_keys($permissoes))
            ->values()
            ->all();
    }

    public static function papeis(): array
    {
        return [
            'superadmin' => self::nomes(),
            'administrador' => [
                'dashboard.visualizar',
                'financeiro.visualizar',
                'financeiro.criar',
                'financeiro.editar',
                'financeiro.excluir',
                'financeiro.pagar',
                'financeiro.receber',
                'financeiro.exportar',
                'cadastros.visualizar',
                'cadastros.criar',
                'cadastros.editar',
                'cadastros.excluir',
                'relatorios.visualizar',
                'relatorios.exportar',
                'usuarios.visualizar',
                'usuarios.criar',
                'usuarios.editar',
                'configuracoes.visualizar',
                'integracoes.visualizar',
                'cron.visualizar',
                'auditoria.visualizar',
            ],
            'financeiro' => [
                'dashboard.visualizar',
                'financeiro.visualizar',
                'financeiro.criar',
                'financeiro.editar',
                'financeiro.pagar',
                'financeiro.receber',
                'cadastros.visualizar',
                'cadastros.criar',
                'cadastros.editar',
                'relatorios.visualizar',
            ],
            'operador' => [
                'dashboard.visualizar',
                'financeiro.visualizar',
                'financeiro.criar',
                'cadastros.visualizar',
                'relatorios.visualizar',
            ],
        ];
    }
}
