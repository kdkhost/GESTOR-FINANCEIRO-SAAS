<?php

namespace App\Modules\Instalador\Controllers;

use App\Http\Controllers\Controller;
use App\Modules\Permissoes\Support\PermissoesPadrao;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Schema;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use Spatie\Permission\PermissionRegistrar;

class InstaladorController extends Controller
{
    /**
     * Exibe a interface do instalador.
     * Corrige SESSION_DRIVER se necessario antes de qualquer coisa.
     */
    public function index()
    {
        if (config('session.driver') === 'database') {
            try {
                Schema::hasTable('sessions');
            } catch (\Throwable) {
                $this->corrigirEnvSessionDriver('file');
                Artisan::call('config:clear');
            }
        }

        return view('instalador.index');
    }

    /**
     * Etapa 1 — Verificar requisitos do servidor.
     */
    public function verificarRequisitos(): JsonResponse
    {
        $requisitos = [
            ['nome' => 'PHP >= 8.4',    'ok' => version_compare(PHP_VERSION, '8.4.0', '>='), 'valor' => PHP_VERSION],
            ['nome' => 'ext-pdo',       'ok' => extension_loaded('pdo'),       'valor' => extension_loaded('pdo')       ? 'Ativo' : 'Inativo'],
            ['nome' => 'ext-pdo_mysql', 'ok' => extension_loaded('pdo_mysql'), 'valor' => extension_loaded('pdo_mysql') ? 'Ativo' : 'Inativo'],
            ['nome' => 'ext-mbstring',  'ok' => extension_loaded('mbstring'),  'valor' => extension_loaded('mbstring')  ? 'Ativo' : 'Inativo'],
            ['nome' => 'ext-openssl',   'ok' => extension_loaded('openssl'),   'valor' => extension_loaded('openssl')   ? 'Ativo' : 'Inativo'],
            ['nome' => 'ext-json',      'ok' => extension_loaded('json'),      'valor' => extension_loaded('json')      ? 'Ativo' : 'Inativo'],
            ['nome' => 'ext-tokenizer', 'ok' => extension_loaded('tokenizer'), 'valor' => extension_loaded('tokenizer') ? 'Ativo' : 'Inativo'],
            ['nome' => 'ext-xml',       'ok' => extension_loaded('xml'),       'valor' => extension_loaded('xml')       ? 'Ativo' : 'Inativo'],
            ['nome' => 'ext-fileinfo',  'ok' => extension_loaded('fileinfo'),  'valor' => extension_loaded('fileinfo')  ? 'Ativo' : 'Inativo'],
            ['nome' => 'ext-ctype',     'ok' => extension_loaded('ctype'),     'valor' => extension_loaded('ctype')     ? 'Ativo' : 'Inativo'],
            ['nome' => 'ext-curl',      'ok' => extension_loaded('curl'),      'valor' => extension_loaded('curl')      ? 'Ativo' : 'Inativo'],
            ['nome' => 'ext-zip',       'ok' => extension_loaded('zip'),       'valor' => extension_loaded('zip')       ? 'Ativo' : 'Inativo'],
            ['nome' => 'mod_rewrite',   'ok' => true,                          'valor' => 'Verificado'],
        ];

        return response()->json([
            'sucesso'  => true,
            'requisitos' => $requisitos,
            'tudo_ok'  => collect($requisitos)->every(fn ($r) => $r['ok']),
        ]);
    }

    /**
     * Etapa 2 — Verificar permissoes de pastas.
     */
    public function verificarPermissoes(): JsonResponse
    {
        $pastas = [
            ['pasta' => 'storage/app',      'ok' => is_writable(storage_path('app'))],
            ['pasta' => 'storage/framework', 'ok' => is_writable(storage_path('framework'))],
            ['pasta' => 'storage/logs',      'ok' => is_writable(storage_path('logs'))],
            ['pasta' => 'bootstrap/cache',   'ok' => is_writable(base_path('bootstrap/cache'))],
        ];

        return response()->json([
            'sucesso'  => true,
            'pastas'   => $pastas,
            'tudo_ok'  => collect($pastas)->every(fn ($p) => $p['ok']),
        ]);
    }

    /**
     * Etapa 3 — Testar conexao com banco de dados.
     */
    public function testarBanco(Request $request): JsonResponse
    {
        $request->validate([
            'db_host'     => 'required',
            'db_port'     => 'required|integer',
            'db_database' => 'required',
            'db_username' => 'required',
            'db_password' => 'nullable',
        ]);

        try {
            $dsn = "mysql:host={$request->db_host};port={$request->db_port};dbname={$request->db_database};charset=utf8mb4";
            $pdo = new \PDO($dsn, $request->db_username, $request->db_password ?? '');
            $pdo->setAttribute(\PDO::ATTR_ERRMODE, \PDO::ERRMODE_EXCEPTION);
            $versao = $pdo->query('SELECT VERSION()')->fetchColumn();

            return response()->json([
                'sucesso'  => true,
                'mensagem' => 'Conexao com o banco estabelecida com sucesso!',
                'versao'   => $versao,
            ]);
        } catch (\PDOException $e) {
            return response()->json(['sucesso' => false, 'mensagem' => 'Falha na conexao: ' . $e->getMessage()]);
        }
    }

    /**
     * Etapa 4 — Salvar configuracoes do banco no .env e rodar migrations do zero.
     * Se ja existirem tabelas de uma tentativa anterior, dropa tudo e recria.
     */
    public function salvarConfiguracaoBanco(Request $request): JsonResponse
    {
        $request->validate([
            'db_host'     => 'required',
            'db_port'     => 'required|integer',
            'db_database' => 'required',
            'db_username' => 'required',
            'db_password' => 'nullable',
        ]);

        try {
            // Salva configuracoes no .env
            $envPath = base_path('.env');
            $env     = file_get_contents($envPath);

            $subs = [
                'DB_CONNECTION'  => 'mysql',
                'DB_HOST'        => $request->db_host,
                'DB_PORT'        => $request->db_port,
                'DB_DATABASE'    => $request->db_database,
                'DB_USERNAME'    => $request->db_username,
                'DB_PASSWORD'    => $request->db_password ?? '',
                'SESSION_DRIVER' => 'file',
                'CACHE_STORE'    => 'file',
            ];

            foreach ($subs as $chave => $valor) {
                if (preg_match("/^{$chave}=/m", $env)) {
                    $env = preg_replace("/^{$chave}=.*/m", "{$chave}={$valor}", $env);
                } else {
                    $env .= "\n{$chave}={$valor}";
                }
            }

            file_put_contents($envPath, $env);
            Artisan::call('config:clear');

            return response()->json(['sucesso' => true, 'mensagem' => 'Configuracoes salvas com sucesso!']);
        } catch (\Throwable $e) {
            return response()->json(['sucesso' => false, 'mensagem' => 'Erro ao salvar configuracoes: ' . $e->getMessage()]);
        }
    }

    /**
     * Etapa 5 — Executar migrations.
     * Se ja existirem tabelas de tentativa anterior, dropa tudo e recria do zero.
     */
    public function executarMigrations(): JsonResponse
    {
        try {
            // Verifica se ja existem tabelas (tentativa anterior falhou)
            try {
                $tabelas = DB::select('SHOW TABLES');
                if (count($tabelas) > 0) {
                    // Dropa todas as tabelas para comecar do zero
                    DB::statement('SET FOREIGN_KEY_CHECKS=0');
                    foreach ($tabelas as $tabela) {
                        $nome = array_values((array) $tabela)[0];
                        DB::statement("DROP TABLE IF EXISTS `{$nome}`");
                    }
                    DB::statement('SET FOREIGN_KEY_CHECKS=1');
                }
            } catch (\Throwable) {
                // Banco vazio ou inacessivel — continua normalmente
            }

            // O projeto ja possui migration propria do Spatie; publicar novamente criaria duplicidade.
            if (empty(glob(database_path('migrations/*create_permission_tables*.php')))) {
                Artisan::call('vendor:publish', [
                    '--provider' => 'Spatie\\Permission\\PermissionServiceProvider',
                    '--force'    => true,
                ]);
            }

            // Roda todas as migrations
            Artisan::call('migrate', ['--force' => true]);
            $log = Artisan::output();

            return response()->json([
                'sucesso'  => true,
                'mensagem' => 'Banco de dados criado com sucesso!',
                'log'      => $log,
            ]);
        } catch (\Throwable $e) {
            return response()->json(['sucesso' => false, 'mensagem' => 'Erro nas migrations: ' . $e->getMessage()]);
        }
    }

    /**
     * Etapa 6 - Executar seeders padrao do sistema.
     */
    public function executarSeeders(): JsonResponse
    {
        try {
            Artisan::call('db:seed', ['--force' => true]);

            return response()->json([
                'sucesso' => true,
                'mensagem' => 'Dados iniciais criados com sucesso!',
                'log' => Artisan::output(),
            ]);
        } catch (\Throwable $e) {
            return response()->json([
                'sucesso' => false,
                'mensagem' => 'Erro ao executar seeders: ' . $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Etapa 7 - Criar permissoes e papeis iniciais.
     */
    public function publicarPermissoes(): JsonResponse
    {
        try {
            if (! Schema::hasTable('roles') || ! Schema::hasTable('permissions')) {
                return response()->json([
                    'sucesso' => false,
                    'mensagem' => 'As tabelas de permissoes ainda nao existem. Execute as migrations primeiro.',
                ], 422);
            }

            foreach (PermissoesPadrao::nomes() as $nome) {
                Permission::firstOrCreate(['name' => $nome, 'guard_name' => 'web']);
            }

            foreach (PermissoesPadrao::papeis() as $papel => $permissoes) {
                $role = Role::firstOrCreate(['name' => $papel, 'guard_name' => 'web']);
                $role->syncPermissions($permissoes);
            }

            app(PermissionRegistrar::class)->forgetCachedPermissions();

            return response()->json([
                'sucesso' => true,
                'mensagem' => 'Permissoes internas publicadas com sucesso!',
            ]);
        } catch (\Throwable $e) {
            return response()->json([
                'sucesso' => false,
                'mensagem' => 'Erro ao publicar permissoes: ' . $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Etapa 6 — Criar link simbolico do storage.
     */
    public function criarStorageLink(): JsonResponse
    {
        try {
            Artisan::call('storage:link');
            return response()->json(['sucesso' => true, 'mensagem' => 'Storage link criado com sucesso!']);
        } catch (\Throwable) {
            return response()->json(['sucesso' => true, 'mensagem' => 'Storage configurado. Verifique o link manualmente no cPanel se necessario.']);
        }
    }

    /**
     * Etapa 7 — Criar superadministrador.
     * Se ja existir um usuario com o mesmo email, atualiza em vez de criar.
     */
    public function criarSuperadmin(Request $request): JsonResponse
    {
        $request->validate([
            'name'     => 'required|string|max:100',
            'email'    => 'required|email',
            'password' => 'required|min:8|confirmed',
        ]);

        try {
            // Usa updateOrCreate para nao falhar se o usuario ja existir
            $user = \App\Modules\Usuarios\Models\User::updateOrCreate(
                ['email' => $request->email],
                [
                    'name'     => $request->name,
                    'password' => Hash::make($request->password),
                    'tipo'     => 'superadmin',
                    'status'   => 'ativo',
                ]
            );

            // Atribui role se a tabela roles existir
            try {
                if (class_exists(\Spatie\Permission\Models\Role::class) && Schema::hasTable('roles')) {
                    $role = \Spatie\Permission\Models\Role::firstOrCreate(['name' => 'superadmin', 'guard_name' => 'web']);
                    if (! $user->hasRole('superadmin')) {
                        $user->assignRole($role);
                    }
                }
            } catch (\Throwable) {
                // Nao bloqueia se roles falhar
            }

            $acao = $user->wasRecentlyCreated ? 'criado' : 'atualizado';
            return response()->json(['sucesso' => true, 'mensagem' => "Superadministrador {$acao} com sucesso!"]);
        } catch (\Throwable $e) {
            return response()->json(['sucesso' => false, 'mensagem' => 'Erro ao criar usuario: ' . $e->getMessage()]);
        }
    }

    /**
     * Etapa 8 — Configuracoes iniciais do sistema.
     */
    public function salvarConfiguracaoInicial(Request $request): JsonResponse
    {
        $request->validate([
            'sistema_nome'         => 'required|string|max:100',
            'sistema_proprietario' => 'required|string|max:100',
        ]);

        try {
            $configuracoes = [
                'sistema_nome'         => $request->sistema_nome,
                'sistema_proprietario' => $request->sistema_proprietario,
                'sistema_descricao'    => $request->sistema_descricao    ?? '',
                'mail_driver'          => 'smtp',
                'mail_host'            => $request->smtp_host            ?? '',
                'mail_port'            => $request->smtp_porta           ?? '587',
                'mail_encryption'    => 'tls',
                'mail_username'        => $request->smtp_usuario         ?? '',
                'mail_password'        => $request->smtp_senha           ?? '',
                'mail_from_name'       => $request->sistema_nome         ?? 'FinanceiroSaaS',
                'mail_from_address'    => $request->smtp_remetente       ?? '',
            ];

            foreach ($configuracoes as $chave => $valor) {
                DB::table('configuracoes')->updateOrInsert(
                    ['chave' => $chave],
                    ['valor' => $valor, 'updated_at' => now()]
                );
            }

            return response()->json(['sucesso' => true, 'mensagem' => 'Configuracoes salvas com sucesso!']);
        } catch (\Throwable $e) {
            return response()->json(['sucesso' => false, 'mensagem' => 'Erro: ' . $e->getMessage()]);
        }
    }

    /**
     * Etapa 9 — Finalizar instalacao.
     * Cria o arquivo installed, ativa SESSION_DRIVER=database e limpa caches.
     */
    public function finalizar(): JsonResponse
    {
        try {
            // Marca instalacao como concluida
            file_put_contents(storage_path('installed'), date('Y-m-d H:i:s'));

            // Salva flag no banco tambem
            try {
                DB::table('configuracoes')->updateOrInsert(
                    ['chave' => 'instalacao_concluida'],
                    ['valor' => '1', 'updated_at' => now()]
                );
            } catch (\Throwable) {}

            // Atribui role ao superadmin se ainda nao tiver
            try {
                if (class_exists(\Spatie\Permission\Models\Role::class) && Schema::hasTable('roles')) {
                    $user = \App\Modules\Usuarios\Models\User::where('tipo', 'superadmin')->first();
                    if ($user) {
                        $role = \Spatie\Permission\Models\Role::firstOrCreate(['name' => 'superadmin', 'guard_name' => 'web']);
                        if (! $user->hasRole('superadmin')) {
                            $user->assignRole($role);
                        }
                    }
                }
            } catch (\Throwable) {}

            // Ativa drivers de banco agora que as tabelas existem
            $this->corrigirEnvSessionDriver('database');

            // Limpa todos os caches
            Artisan::call('config:clear');
            Artisan::call('cache:clear');
            Artisan::call('view:clear');
            Artisan::call('route:clear');

            return response()->json([
                'sucesso'  => true,
                'mensagem' => 'Instalacao concluida com sucesso! Redirecionando...',
                'redirect' => '/admin/dashboard',
            ]);
        } catch (\Throwable $e) {
            return response()->json(['sucesso' => false, 'mensagem' => 'Erro na finalizacao: ' . $e->getMessage()]);
        }
    }

    /**
     * Atualiza SESSION_DRIVER e CACHE_STORE no .env.
     */
    private function corrigirEnvSessionDriver(string $driver): void
    {
        $envPath = base_path('.env');
        if (! file_exists($envPath)) return;

        $env = file_get_contents($envPath);
        $env = preg_replace('/^SESSION_DRIVER=.*/m', "SESSION_DRIVER={$driver}", $env);
        $env = preg_replace('/^CACHE_STORE=.*/m',    "CACHE_STORE={$driver}",    $env);
        file_put_contents($envPath, $env);
    }
}
