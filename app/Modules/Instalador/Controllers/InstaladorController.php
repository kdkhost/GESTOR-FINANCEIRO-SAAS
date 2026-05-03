<?php

namespace App\Modules\Instalador\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Schema;

class InstaladorController extends Controller
{
    // Middleware aplicado via rota (ver Routes/web.php)

    /**
     * Exibe a interface do instalador.
     * Garante que SESSION_DRIVER seja compatível com o estado atual
     * (antes das migrations, não pode usar driver "database").
     */
    public function index()
    {
        // Se o driver de sessão for "database" mas as tabelas ainda não existem,
        // corrige automaticamente para "file" para o instalador funcionar.
        if (config('session.driver') === 'database') {
            try {
                \Illuminate\Support\Facades\Schema::hasTable('sessions');
            } catch (\Throwable) {
                // Banco inacessível ou tabela inexistente — corrige o .env
                $envPath = base_path('.env');
                if (file_exists($envPath)) {
                    $env = file_get_contents($envPath);
                    $env = preg_replace('/^SESSION_DRIVER=.*/m', 'SESSION_DRIVER=file', $env);
                    $env = preg_replace('/^CACHE_STORE=.*/m',    'CACHE_STORE=file',    $env);
                    file_put_contents($envPath, $env);
                    \Illuminate\Support\Facades\Artisan::call('config:clear');
                }
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
            ['nome' => 'PHP >= 8.2',      'ok' => version_compare(PHP_VERSION, '8.2.0', '>='), 'valor' => PHP_VERSION],
            ['nome' => 'ext-pdo',         'ok' => extension_loaded('pdo'),          'valor' => extension_loaded('pdo') ? 'Ativo' : 'Inativo'],
            ['nome' => 'ext-pdo_mysql',   'ok' => extension_loaded('pdo_mysql'),    'valor' => extension_loaded('pdo_mysql') ? 'Ativo' : 'Inativo'],
            ['nome' => 'ext-mbstring',    'ok' => extension_loaded('mbstring'),     'valor' => extension_loaded('mbstring') ? 'Ativo' : 'Inativo'],
            ['nome' => 'ext-openssl',     'ok' => extension_loaded('openssl'),      'valor' => extension_loaded('openssl') ? 'Ativo' : 'Inativo'],
            ['nome' => 'ext-json',        'ok' => extension_loaded('json'),         'valor' => extension_loaded('json') ? 'Ativo' : 'Inativo'],
            ['nome' => 'ext-tokenizer',   'ok' => extension_loaded('tokenizer'),    'valor' => extension_loaded('tokenizer') ? 'Ativo' : 'Inativo'],
            ['nome' => 'ext-xml',         'ok' => extension_loaded('xml'),          'valor' => extension_loaded('xml') ? 'Ativo' : 'Inativo'],
            ['nome' => 'ext-fileinfo',    'ok' => extension_loaded('fileinfo'),     'valor' => extension_loaded('fileinfo') ? 'Ativo' : 'Inativo'],
            ['nome' => 'ext-ctype',       'ok' => extension_loaded('ctype'),        'valor' => extension_loaded('ctype') ? 'Ativo' : 'Inativo'],
            ['nome' => 'ext-curl',        'ok' => extension_loaded('curl'),         'valor' => extension_loaded('curl') ? 'Ativo' : 'Inativo'],
            ['nome' => 'ext-zip',         'ok' => extension_loaded('zip'),          'valor' => extension_loaded('zip') ? 'Ativo' : 'Inativo'],
            ['nome' => 'mod_rewrite',     'ok' => function_exists('apache_get_modules') ? in_array('mod_rewrite', apache_get_modules()) : true, 'valor' => 'Verificado'],
        ];

        $tudo_ok = collect($requisitos)->every(fn ($r) => $r['ok']);

        return response()->json(['sucesso' => true, 'requisitos' => $requisitos, 'tudo_ok' => $tudo_ok]);
    }

    /**
     * Etapa 2 — Verificar permissões de pastas.
     */
    public function verificarPermissoes(): JsonResponse
    {
        $pastas = [
            ['pasta' => 'storage/app',       'ok' => is_writable(storage_path('app'))],
            ['pasta' => 'storage/framework',  'ok' => is_writable(storage_path('framework'))],
            ['pasta' => 'storage/logs',       'ok' => is_writable(storage_path('logs'))],
            ['pasta' => 'bootstrap/cache',    'ok' => is_writable(base_path('bootstrap/cache'))],
            ['pasta' => 'public',             'ok' => is_writable(public_path())],
        ];

        $tudo_ok = collect($pastas)->every(fn ($p) => $p['ok']);

        return response()->json(['sucesso' => true, 'pastas' => $pastas, 'tudo_ok' => $tudo_ok]);
    }

    /**
     * Etapa 3 — Testar conexão com banco de dados.
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
                'mensagem' => 'Conexão com o banco estabelecida com sucesso!',
                'versao'   => $versao,
            ]);
        } catch (\PDOException $e) {
            return response()->json([
                'sucesso'  => false,
                'mensagem' => 'Falha na conexão: ' . $e->getMessage(),
            ]);
        }
    }

    /**
     * Etapa 4 — Salvar configurações do banco no .env
     * Também força SESSION_DRIVER=file para o instalador funcionar
     * antes das migrations criarem a tabela sessions.
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
            $envPath = base_path('.env');
            $env = file_get_contents($envPath);

            $substituicoes = [
                'DB_CONNECTION'  => 'mysql',
                'DB_HOST'        => $request->db_host,
                'DB_PORT'        => $request->db_port,
                'DB_DATABASE'    => $request->db_database,
                'DB_USERNAME'    => $request->db_username,
                'DB_PASSWORD'    => $request->db_password ?? '',
                // Garante que sessão use arquivo durante a instalação
                // (tabela sessions ainda não existe até as migrations rodarem)
                'SESSION_DRIVER' => 'file',
                'CACHE_STORE'    => 'file',
            ];

            foreach ($substituicoes as $chave => $valor) {
                if (preg_match("/^{$chave}=/m", $env)) {
                    $env = preg_replace("/^{$chave}=.*/m", "{$chave}={$valor}", $env);
                } else {
                    $env .= "\n{$chave}={$valor}";
                }
            }

            file_put_contents($envPath, $env);
            Artisan::call('config:clear');

            return response()->json(['sucesso' => true, 'mensagem' => 'Configurações salvas com sucesso!']);
        } catch (\Throwable $e) {
            return response()->json(['sucesso' => false, 'mensagem' => 'Erro ao salvar configurações: ' . $e->getMessage()]);
        }
    }

    /**
     * Etapa 5 — Executar migrations.
     */
    public function executarMigrations(): JsonResponse
    {
        try {
            $saida = Artisan::call('migrate', ['--force' => true]);
            $log   = Artisan::output();

            return response()->json([
                'sucesso'  => true,
                'mensagem' => 'Migrations executadas com sucesso!',
                'log'      => $log,
            ]);
        } catch (\Throwable $e) {
            return response()->json([
                'sucesso'  => false,
                'mensagem' => 'Erro nas migrations: ' . $e->getMessage(),
            ]);
        }
    }

    /**
     * Etapa 6 — Executar seeders.
     */
    public function executarSeeders(): JsonResponse
    {
        try {
            Artisan::call('db:seed', ['--force' => true]);
            return response()->json(['sucesso' => true, 'mensagem' => 'Dados iniciais inseridos com sucesso!']);
        } catch (\Throwable $e) {
            return response()->json(['sucesso' => false, 'mensagem' => 'Erro nos seeders: ' . $e->getMessage()]);
        }
    }

    /**
     * Etapa 7 — Publicar permissões Spatie.
     */
    public function publicarPermissoes(): JsonResponse
    {
        try {
            Artisan::call('vendor:publish', ['--provider' => 'Spatie\\Permission\\PermissionServiceProvider', '--force' => true]);
            Artisan::call('migrate', ['--force' => true]);
            return response()->json(['sucesso' => true, 'mensagem' => 'Permissões configuradas com sucesso!']);
        } catch (\Throwable $e) {
            return response()->json(['sucesso' => false, 'mensagem' => 'Erro nas permissões: ' . $e->getMessage()]);
        }
    }

    /**
     * Etapa 8 — Criar link simbólico do storage.
     */
    public function criarStorageLink(): JsonResponse
    {
        try {
            Artisan::call('storage:link');
            return response()->json(['sucesso' => true, 'mensagem' => 'Storage link criado com sucesso!']);
        } catch (\Throwable $e) {
            // No cPanel pode não funcionar via CLI; link manual é aceitável
            return response()->json(['sucesso' => true, 'mensagem' => 'Storage configurado. Verifique o link manualmente no cPanel se necessário.']);
        }
    }

    /**
     * Etapa 9 — Criar superadministrador inicial.
     * Cria o usuário sem atribuir role do Spatie neste momento,
     * pois as tabelas de permissões podem ainda não existir.
     * A role é atribuída na etapa de finalização.
     */
    public function criarSuperadmin(Request $request): JsonResponse
    {
        $request->validate([
            'name'     => 'required|string|max:100',
            'email'    => 'required|email|unique:users,email',
            'password' => 'required|min:8|confirmed',
        ]);

        try {
            $user = \App\Modules\Usuarios\Models\User::create([
                'name'     => $request->name,
                'email'    => $request->email,
                'password' => Hash::make($request->password),
                'tipo'     => 'superadmin',
                'status'   => 'ativo',
            ]);

            // Tenta atribuir role do Spatie apenas se a tabela já existir
            try {
                if (class_exists(\Spatie\Permission\Models\Role::class) && Schema::hasTable('roles')) {
                    $role = \Spatie\Permission\Models\Role::firstOrCreate([
                        'name'       => 'superadmin',
                        'guard_name' => 'web',
                    ]);
                    $user->assignRole($role);
                }
            } catch (\Throwable) {
                // Tabela roles ainda não existe — será configurada na etapa de permissões
            }

            return response()->json(['sucesso' => true, 'mensagem' => 'Superadministrador criado com sucesso!']);
        } catch (\Throwable $e) {
            return response()->json(['sucesso' => false, 'mensagem' => 'Erro ao criar usuário: ' . $e->getMessage()]);
        }
    }

    /**
     * Etapa 10 — Configurações iniciais do sistema.
     */
    public function salvarConfiguracaoInicial(Request $request): JsonResponse
    {
        $request->validate([
            'sistema_nome'        => 'required|string|max:100',
            'sistema_proprietario'=> 'required|string|max:100',
        ]);

        try {
            $configuracoes = [
                'sistema_nome'         => $request->sistema_nome,
                'sistema_proprietario' => $request->sistema_proprietario,
                'sistema_descricao'    => $request->sistema_descricao ?? '',
                'smtp_host'            => $request->smtp_host ?? '',
                'smtp_porta'           => $request->smtp_porta ?? '587',
                'smtp_usuario'         => $request->smtp_usuario ?? '',
                'smtp_senha'           => $request->smtp_senha ?? '',
                'smtp_remetente'       => $request->smtp_remetente ?? '',
            ];

            foreach ($configuracoes as $chave => $valor) {
                DB::table('configuracoes')->updateOrInsert(
                    ['chave' => $chave],
                    ['valor' => $valor, 'updated_at' => now()]
                );
            }

            return response()->json(['sucesso' => true, 'mensagem' => 'Configurações salvas com sucesso!']);
        } catch (\Throwable $e) {
            return response()->json(['sucesso' => false, 'mensagem' => 'Erro: ' . $e->getMessage()]);
        }
    }

    /**
     * Etapa 11 — Finalizar instalação e bloquear instalador.
     * Após as migrations, muda SESSION_DRIVER e CACHE_STORE para database.
     */
    public function finalizar(): JsonResponse
    {
        try {
            // Marca instalação como concluída
            file_put_contents(storage_path('installed'), date('Y-m-d H:i:s'));

            DB::table('configuracoes')->updateOrInsert(
                ['chave' => 'instalacao_concluida'],
                ['valor' => '1', 'updated_at' => now()]
            );

            // Atribui role superadmin ao primeiro usuário se a tabela roles existir
            try {
                if (class_exists(\Spatie\Permission\Models\Role::class) && Schema::hasTable('roles')) {
                    $user = \App\Modules\Usuarios\Models\User::where('tipo', 'superadmin')->first();
                    if ($user) {
                        $role = \Spatie\Permission\Models\Role::firstOrCreate([
                            'name'       => 'superadmin',
                            'guard_name' => 'web',
                        ]);
                        if (! $user->hasRole('superadmin')) {
                            $user->assignRole($role);
                        }
                    }
                }
            } catch (\Throwable) {
                // Não bloqueia a finalização se roles falhar
            }

            // Agora que as migrations rodaram, ativa drivers de banco
            $envPath = base_path('.env');
            $env     = file_get_contents($envPath);
            $env     = preg_replace('/^SESSION_DRIVER=.*/m', 'SESSION_DRIVER=database', $env);
            $env     = preg_replace('/^CACHE_STORE=.*/m',    'CACHE_STORE=database',    $env);
            file_put_contents($envPath, $env);

            // Limpa caches
            Artisan::call('config:clear');
            Artisan::call('cache:clear');
            Artisan::call('view:clear');

            return response()->json([
                'sucesso'   => true,
                'mensagem'  => 'Instalação concluída com sucesso! Redirecionando...',
                'redirect'  => '/admin/dashboard',
            ]);
        } catch (\Throwable $e) {
            return response()->json(['sucesso' => false, 'mensagem' => 'Erro na finalização: ' . $e->getMessage()]);
        }
    }
}
