<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Schema;
use Carbon\Carbon;
use App\Modules\Saas\Models\Plano;
use App\Modules\Saas\Models\Empresa;
use App\Modules\Saas\Models\Assinatura;
use App\Modules\Saas\Models\Fatura;
use App\Models\User;

class DemoDataSeeder extends Seeder
{
    public function run(): void
    {
        $this->call([
            DemoPlanosSeeder::class,
            DemoEmpresasSeeder::class,
            DemoUsuariosSeeder::class,
            DemoClientesFornecedoresSeeder::class,
            DemoFinanceiroSeeder::class,
        ]);
    }
}

// -------------------------------------------------------
// PLANOS DE ASSINATURA
// -------------------------------------------------------
class DemoPlanosSeeder extends Seeder
{
    public function run(): void
    {
        if (!Schema::hasTable('saas_planos')) {
            return;
        }

        $planos = [
            [
                'nome' => 'Gratuito',
                'slug' => 'gratuito',
                'descricao' => 'Perfeito para começar. Acesso básico ao sistema financeiro.',
                'valor_mensal' => 0.00,
                'valor_anual' => 0.00,
                'limites' => json_encode([
                    'usuarios' => 1,
                    'clientes' => 10,
                    'fornecedores' => 10,
                    'contas_bancarias' => 1,
                    'lancamentos_mes' => 50,
                    'relatorios_avancados' => false,
                ]),
                'ativo' => true,
                'ordem' => 1,
            ],
            [
                'nome' => 'Básico',
                'slug' => 'basico',
                'descricao' => 'Ideal para profissionais autônomos e pequenas empresas.',
                'valor_mensal' => 29.90,
                'valor_anual' => 299.00,
                'limites' => json_encode([
                    'usuarios' => 2,
                    'clientes' => 50,
                    'fornecedores' => 50,
                    'contas_bancarias' => 2,
                    'lancamentos_mes' => 200,
                    'relatorios_avancados' => true,
                ]),
                'ativo' => true,
                'ordem' => 2,
            ],
            [
                'nome' => 'Profissional',
                'slug' => 'profissional',
                'descricao' => 'Para empresas em crescimento. Recursos completos de gestão.',
                'valor_mensal' => 79.90,
                'valor_anual' => 799.00,
                'limites' => json_encode([
                    'usuarios' => 5,
                    'clientes' => 200,
                    'fornecedores' => 200,
                    'contas_bancarias' => 5,
                    'lancamentos_mes' => 1000,
                    'relatorios_avancados' => true,
                    'multi_empresa' => true,
                ]),
                'ativo' => true,
                'ordem' => 3,
            ],
            [
                'nome' => 'Empresarial',
                'slug' => 'empresarial',
                'descricao' => 'Solução completa para grandes empresas. Suporte prioritário.',
                'valor_mensal' => 199.90,
                'valor_anual' => 1999.00,
                'limites' => json_encode([
                    'usuarios' => -1, // ilimitado
                    'clientes' => -1,
                    'fornecedores' => -1,
                    'contas_bancarias' => -1,
                    'lancamentos_mes' => -1,
                    'relatorios_avancados' => true,
                    'multi_empresa' => true,
                    'api_acesso' => true,
                    'suporte_prioritario' => true,
                ]),
                'ativo' => true,
                'ordem' => 4,
            ],
        ];

        foreach ($planos as $plano) {
            Plano::updateOrCreate(
                ['slug' => $plano['slug']],
                array_merge($plano, [
                    'created_at' => Carbon::now(),
                    'updated_at' => Carbon::now(),
                ])
            );
        }
    }
}

// -------------------------------------------------------
// EMPRESAS E ASSINATURAS
// -------------------------------------------------------
class DemoEmpresasSeeder extends Seeder
{
    public function run(): void
    {
        if (!Schema::hasTable('saas_empresas') || !Schema::hasTable('saas_planos')) {
            return;
        }

        $planos = Plano::all();
        if ($planos->isEmpty()) {
            return;
        }

        $empresas = [
            [
                'nome_fantasia' => 'Tech Solutions Brasil',
                'razao_social' => 'Tech Solutions Brasil LTDA',
                'cnpj' => '12.345.678/0001-90',
                'email' => 'financeiro@techsolutions.com.br',
                'telefone' => '(11) 3456-7890',
                'cep' => '01310-100',
                'logradouro' => 'Av. Paulista',
                'numero' => '1000',
                'complemento' => 'Sala 1501',
                'bairro' => 'Bela Vista',
                'cidade' => 'São Paulo',
                'estado' => 'SP',
                'status' => 'ativo',
                'timezone' => 'America/Sao_Paulo',
                'locale' => 'pt_BR',
                'observacoes' => 'Empresa de tecnologia em crescimento',
            ],
            [
                'nome_fantasia' => 'Construtora Horizonte',
                'razao_social' => 'Horizonte Construções e Empreendimentos S.A.',
                'cnpj' => '23.456.789/0001-01',
                'email' => 'contato@horizonte.com.br',
                'telefone' => '(21) 2345-6789',
                'cep' => '20040-010',
                'logradouro' => 'Rua do Ouvidor',
                'numero' => '150',
                'complemento' => 'Bloco B, 3º andar',
                'bairro' => 'Centro',
                'cidade' => 'Rio de Janeiro',
                'estado' => 'RJ',
                'status' => 'ativo',
                'timezone' => 'America/Sao_Paulo',
                'locale' => 'pt_BR',
                'observacoes' => 'Grande construtora nacional',
            ],
            [
                'nome_fantasia' => 'Restaurante Sabor & Arte',
                'razao_social' => 'Sabor e Arte Gastronomia LTDA',
                'cnpj' => '34.567.890/0001-12',
                'email' => 'admin@saborarte.com.br',
                'telefone' => '(31) 3456-7890',
                'cep' => '30140-130',
                'logradouro' => 'Av. Afonso Pena',
                'numero' => '850',
                'complemento' => 'Loja 12',
                'bairro' => 'Centro',
                'cidade' => 'Belo Horizonte',
                'estado' => 'MG',
                'status' => 'ativo',
                'timezone' => 'America/Sao_Paulo',
                'locale' => 'pt_BR',
                'observacoes' => 'Restaurante tradicional',
            ],
            [
                'nome_fantasia' => 'Comércio Avenida',
                'razao_social' => 'Avenida Comércio de Artigos LTDA',
                'cnpj' => '45.678.901/0001-23',
                'email' => 'vendas@avenidacomercio.com.br',
                'telefone' => '(41) 3456-7890',
                'cep' => '80020-010',
                'logradouro' => 'Rua XV de Novembro',
                'numero' => '500',
                'complemento' => null,
                'bairro' => 'Centro',
                'cidade' => 'Curitiba',
                'estado' => 'PR',
                'status' => 'ativo',
                'timezone' => 'America/Sao_Paulo',
                'locale' => 'pt_BR',
                'observacoes' => 'Comércio varejista tradicional',
            ],
            [
                'nome_fantasia' => 'Consultoria Alfa',
                'razao_social' => 'Alfa Consultoria Empresarial LTDA',
                'cnpj' => '56.789.012/0001-34',
                'email' => 'contato@alfaconsultoria.com.br',
                'telefone' => '(51) 3456-7890',
                'cep' => '90520-010',
                'logradouro' => 'Av. Carlos Gomes',
                'numero' => '300',
                'complemento' => 'Sala 801',
                'bairro' => 'Boa Vista',
                'cidade' => 'Porto Alegre',
                'estado' => 'RS',
                'status' => 'ativo',
                'timezone' => 'America/Sao_Paulo',
                'locale' => 'pt_BR',
                'observacoes' => 'Consultoria de negócios',
            ],
        ];

        foreach ($empresas as $index => $empresaData) {
            $empresa = Empresa::updateOrCreate(
                ['cnpj' => $empresaData['cnpj']],
                array_merge($empresaData, [
                    'created_at' => Carbon::now(),
                    'updated_at' => Carbon::now(),
                ])
            );

            // Cria assinatura para cada empresa
            $plano = $planos->get($index % $planos->count());
            $status = ['trial', 'ativa', 'ativa', 'ativa', 'ativa'][$index % 5];
            
            Assinatura::updateOrCreate(
                ['empresa_id' => $empresa->id],
                [
                    'empresa_id' => $empresa->id,
                    'plano_id' => $plano->id,
                    'status' => $status,
                    'inicio_em' => Carbon::now()->subMonths(rand(1, 12)),
                    'proxima_cobranca_em' => Carbon::now()->addDays(rand(1, 30)),
                    'gateway' => ['stripe', 'mercadopago', 'pagar.me'][$index % 3],
                    'trial_ate' => $status === 'trial' ? Carbon::now()->addDays(14) : null,
                    'created_at' => Carbon::now(),
                    'updated_at' => Carbon::now(),
                ]
            );

            // Cria faturas de exemplo
            $this->criarFaturasDemo($empresa);
        }
    }

    private function criarFaturasDemo(Empresa $empresa): void
    {
        if (!Schema::hasTable('saas_faturas')) {
            return;
        }

        $assinatura = Assinatura::where('empresa_id', $empresa->id)->first();
        if (!$assinatura) {
            return;
        }

        $plano = Plano::find($assinatura->plano_id);
        if (!$plano || $plano->valor_mensal == 0) {
            return;
        }

        // Cria 6 faturas (3 anteriores pagas, 3 futuras)
        for ($i = -3; $i < 3; $i++) {
            $competencia = Carbon::now()->addMonths($i);
            $vencimento = $competencia->copy()->addDays(10);
            
            $status = match(true) {
                $i < 0 => 'paga',
                $i === 0 && rand(0, 1) === 1 => 'paga',
                $vencimento->isPast() => 'atrasada',
                default => 'aberta',
            };

            Fatura::updateOrCreate(
                [
                    'empresa_id' => $empresa->id,
                    'competencia' => $competencia->format('Y-m'),
                ],
                [
                    'empresa_id' => $empresa->id,
                    'assinatura_id' => $assinatura->id,
                    'status' => $status,
                    'competencia' => $competencia->format('Y-m'),
                    'valor' => $plano->valor_mensal,
                    'vencimento_em' => $vencimento,
                    'pago_em' => $status === 'paga' ? $vencimento->copy()->subDays(rand(1, 5)) : null,
                    'gateway' => $assinatura->gateway,
                    'observacoes' => $i === -1 ? 'Pagamento processado automaticamente' : null,
                    'created_at' => Carbon::now(),
                    'updated_at' => Carbon::now(),
                ]
            );
        }
    }
}

// -------------------------------------------------------
// USUÁRIOS DE DEMONSTRAÇÃO
// -------------------------------------------------------
class DemoUsuariosSeeder extends Seeder
{
    public function run(): void
    {
        if (!Schema::hasTable('users')) {
            return;
        }

        $usuarios = [
            [
                'name' => 'Super Admin',
                'email' => 'admin@gestorfinanceiro.com.br',
                'password' => Hash::make('admin123'),
                'email_verified_at' => Carbon::now(),
            ],
            [
                'name' => 'Gestor Financeiro',
                'email' => 'gestor@gestorfinanceiro.com.br',
                'password' => Hash::make('gestor123'),
                'email_verified_at' => Carbon::now(),
            ],
            [
                'name' => 'Usuário Comum',
                'email' => 'usuario@gestorfinanceiro.com.br',
                'password' => Hash::make('usuario123'),
                'email_verified_at' => Carbon::now(),
            ],
        ];

        foreach ($usuarios as $usuario) {
            User::updateOrCreate(
                ['email' => $usuario['email']],
                array_merge($usuario, [
                    'created_at' => Carbon::now(),
                    'updated_at' => Carbon::now(),
                ])
            );
        }
    }
}

// -------------------------------------------------------
// CLIENTES E FORNECEDORES
// -------------------------------------------------------
class DemoClientesFornecedoresSeeder extends Seeder
{
    public function run(): void
    {
        if (!Schema::hasTable('clientes') || !Schema::hasTable('fornecedores')) {
            return;
        }

        $usuarios = User::pluck('id');
        if ($usuarios->isEmpty()) {
            return;
        }

        $clientes = [
            ['nome' => 'Empresa Alpha Ltda', 'documento' => '11.222.333/0001-44', 'email' => 'contato@alpha.com.br', 'telefone' => '(11) 99999-1111'],
            ['nome' => 'Comércio Beta', 'documento' => '22.333.444/0001-55', 'email' => 'vendas@beta.com.br', 'telefone' => '(11) 99999-2222'],
            ['nome' => 'Indústria Gamma', 'documento' => '33.444.555/0001-66', 'email' => 'financeiro@gamma.com.br', 'telefone' => '(11) 99999-3333'],
            ['nome' => 'Serviços Delta', 'documento' => '44.555.666/0001-77', 'email' => 'admin@delta.com.br', 'telefone' => '(11) 99999-4444'],
            ['nome' => 'João Silva', 'documento' => '123.456.789-00', 'email' => 'joao.silva@email.com', 'telefone' => '(11) 99999-5555'],
            ['nome' => 'Maria Santos', 'documento' => '234.567.890-11', 'email' => 'maria.santos@email.com', 'telefone' => '(11) 99999-6666'],
            ['nome' => 'Transportadora Ômega', 'documento' => '55.666.777/0001-88', 'email' => 'logistica@omega.com.br', 'telefone' => '(11) 99999-7777'],
            ['nome' => 'Consultoria Sigma', 'documento' => '66.777.888/0001-99', 'email' => 'consultoria@sigma.com.br', 'telefone' => '(11) 99999-8888'],
        ];

        $fornecedores = [
            ['nome' => 'Fornecedor A Materiais', 'documento' => '77.888.999/0001-00', 'email' => 'vendas@fornecedora.com.br', 'telefone' => '(11) 98888-1111'],
            ['nome' => 'Distribuidora B', 'documento' => '88.999.000/0001-11', 'email' => 'pedidos@distribuidorab.com.br', 'telefone' => '(11) 98888-2222'],
            ['nome' => 'Energia Elétrica C', 'documento' => '99.000.111/0001-22', 'email' => 'faturamento@energiac.com.br', 'telefone' => '(11) 98888-3333'],
            ['nome' => 'Telefonia D', 'documento' => '00.111.222/0001-33', 'email' => 'cobranca@telefoniad.com.br', 'telefone' => '(11) 98888-4444'],
            ['nome' => 'Imobiliária E', 'documento' => '11.222.333/0001-44', 'email' => 'locacao@imobiliariae.com.br', 'telefone' => '(11) 98888-5555'],
            ['nome' => 'Seguradora F', 'documento' => '22.333.444/0001-55', 'email' => 'seguros@seguradoraf.com.br', 'telefone' => '(11) 98888-6666'],
            ['nome' => 'Escritório de Contabilidade G', 'documento' => '33.444.555/0001-66', 'email' => 'contato@contabilidadeg.com.br', 'telefone' => '(11) 98888-7777'],
            ['nome' => 'Limpeza e Conservação H', 'documento' => '44.555.666/0001-77', 'email' => 'admin@limpezah.com.br', 'telefone' => '(11) 98888-8888'],
        ];

        foreach ($usuarios as $userId) {
            // Cria clientes
            foreach ($clientes as $cliente) {
                DB::table('clientes')->updateOrInsert(
                    ['documento' => $cliente['documento'], 'user_id' => $userId],
                    array_merge($cliente, [
                        'user_id' => $userId,
                        'ativo' => true,
                        'created_at' => Carbon::now(),
                        'updated_at' => Carbon::now(),
                    ])
                );
            }

            // Cria fornecedores
            foreach ($fornecedores as $fornecedor) {
                DB::table('fornecedores')->updateOrInsert(
                    ['documento' => $fornecedor['documento'], 'user_id' => $userId],
                    array_merge($fornecedor, [
                        'user_id' => $userId,
                        'ativo' => true,
                        'created_at' => Carbon::now(),
                        'updated_at' => Carbon::now(),
                    ])
                );
            }
        }
    }
}

// -------------------------------------------------------
// DADOS FINANCEIROS (Contas, Receitas, Despesas)
// -------------------------------------------------------
class DemoFinanceiroSeeder extends Seeder
{
    public function run(): void
    {
        if (!Schema::hasTable('users')) {
            return;
        }

        $usuarios = User::pluck('id');
        if ($usuarios->isEmpty()) {
            return;
        }

        foreach ($usuarios as $userId) {
            $this->criarContasBancarias($userId);
            $this->criarContasPagar($userId);
            $this->criarContasReceber($userId);
            $this->criarReceitasDespesas($userId);
        }
    }

    private function criarContasBancarias(int $userId): void
    {
        if (!Schema::hasTable('contas_bancarias')) {
            return;
        }

        $contas = [
            ['nome' => 'Conta Corrente Principal', 'banco' => 'Itaú', 'agencia' => '1234', 'numero_conta' => '56789-0', 'tipo' => 'corrente', 'saldo_atual' => 15000.00],
            ['nome' => 'Conta Poupança', 'banco' => 'Bradesco', 'agencia' => '5678', 'numero_conta' => '12345-6', 'tipo' => 'poupanca', 'saldo_atual' => 5000.00],
            ['nome' => 'Conta Digital', 'banco' => 'Nubank', 'agencia' => '0001', 'numero_conta' => '987654321-0', 'tipo' => 'digital', 'saldo_atual' => 2500.00],
        ];

        foreach ($contas as $conta) {
            DB::table('contas_bancarias')->updateOrInsert(
                ['numero_conta' => $conta['numero_conta'], 'user_id' => $userId],
                array_merge($conta, [
                    'user_id' => $userId,
                    'ativo' => true,
                    'created_at' => Carbon::now(),
                    'updated_at' => Carbon::now(),
                ])
            );
        }
    }

    private function criarContasPagar(int $userId): void
    {
        if (!Schema::hasTable('contas_pagar')) {
            return;
        }

        $contas = [
            ['descricao' => 'Aluguel do escritório', 'valor' => 3500.00, 'vencimento' => Carbon::now()->addDays(5), 'status' => 'pendente'],
            ['descricao' => 'Energia elétrica', 'valor' => 850.00, 'vencimento' => Carbon::now()->addDays(10), 'status' => 'pendente'],
            ['descricao' => 'Internet e telefone', 'valor' => 450.00, 'vencimento' => Carbon::now()->addDays(15), 'status' => 'pendente'],
            ['descricao' => 'Salários', 'valor' => 25000.00, 'vencimento' => Carbon::now()->addDays(1), 'status' => 'pendente'],
            ['descricao' => 'Fornecedor A - Materiais', 'valor' => 3200.00, 'vencimento' => Carbon::now()->subDays(5), 'status' => 'atrasada'],
            ['descricao' => 'Contador', 'valor' => 1200.00, 'vencimento' => Carbon::now()->subDays(10), 'status' => 'paga', 'data_pagamento' => Carbon::now()->subDays(8)],
        ];

        foreach ($contas as $conta) {
            DB::table('contas_pagar')->insert([
                'user_id' => $userId,
                'descricao' => $conta['descricao'],
                'valor' => $conta['valor'],
                'vencimento' => $conta['vencimento'],
                'data_pagamento' => $conta['data_pagamento'] ?? null,
                'status' => $conta['status'],
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ]);
        }
    }

    private function criarContasReceber(int $userId): void
    {
        if (!Schema::hasTable('contas_receber')) {
            return;
        }

        $contas = [
            ['descricao' => 'Serviços prestados - Cliente A', 'valor' => 15000.00, 'vencimento' => Carbon::now()->addDays(3), 'status' => 'pendente'],
            ['descricao' => 'Venda de produtos - Cliente B', 'valor' => 8500.00, 'vencimento' => Carbon::now()->addDays(7), 'status' => 'pendente'],
            ['descricao' => 'Consultoria mensal - Cliente C', 'valor' => 5000.00, 'vencimento' => Carbon::now()->addDays(12), 'status' => 'pendente'],
            ['descricao' => 'Projeto especial - Cliente D', 'valor' => 25000.00, 'vencimento' => Carbon::now()->subDays(3), 'status' => 'atrasada'],
            ['descricao' => 'Manutenção - Cliente E', 'valor' => 3200.00, 'vencimento' => Carbon::now()->subDays(7), 'status' => 'recebida', 'data_recebimento' => Carbon::now()->subDays(2)],
        ];

        foreach ($contas as $conta) {
            DB::table('contas_receber')->insert([
                'user_id' => $userId,
                'descricao' => $conta['descricao'],
                'valor' => $conta['valor'],
                'vencimento' => $conta['vencimento'],
                'data_recebimento' => $conta['data_recebimento'] ?? null,
                'status' => $conta['status'],
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ]);
        }
    }

    private function criarReceitasDespesas(int $userId): void
    {
        // Receitas
        if (Schema::hasTable('receitas')) {
            $receitas = [
                ['descricao' => 'Vendas de produtos', 'valor' => 25000.00, 'data' => Carbon::now()->subDays(15)],
                ['descricao' => 'Prestação de serviços', 'valor' => 18000.00, 'data' => Carbon::now()->subDays(10)],
                ['descricao' => 'Consultoria', 'valor' => 8000.00, 'data' => Carbon::now()->subDays(5)],
                ['descricao' => 'Vendas online', 'valor' => 12000.00, 'data' => Carbon::now()->subDays(2)],
            ];

            foreach ($receitas as $receita) {
                DB::table('receitas')->insert([
                    'user_id' => $userId,
                    'descricao' => $receita['descricao'],
                    'valor' => $receita['valor'],
                    'data' => $receita['data'],
                    'created_at' => Carbon::now(),
                    'updated_at' => Carbon::now(),
                ]);
            }
        }

        // Despesas
        if (Schema::hasTable('despesas')) {
            $despesas = [
                ['descricao' => 'Compra de materiais', 'valor' => 8000.00, 'data' => Carbon::now()->subDays(12)],
                ['descricao' => 'Marketing e publicidade', 'valor' => 3500.00, 'data' => Carbon::now()->subDays(8)],
                ['descricao' => 'Manutenção de equipamentos', 'valor' => 2500.00, 'data' => Carbon::now()->subDays(4)],
                ['descricao' => 'Despesas administrativas', 'valor' => 1800.00, 'data' => Carbon::now()->subDays(1)],
            ];

            foreach ($despesas as $despesa) {
                DB::table('despesas')->insert([
                    'user_id' => $userId,
                    'descricao' => $despesa['descricao'],
                    'valor' => $despesa['valor'],
                    'data' => $despesa['data'],
                    'created_at' => Carbon::now(),
                    'updated_at' => Carbon::now(),
                ]);
            }
        }
    }
}
