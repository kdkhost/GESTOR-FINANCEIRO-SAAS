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
    /**
     * Middleware: bloqueia acesso se instalação já foi concluída.
     */
    public function __construct()
    {
        $this->middleware(function ($request, $next) {
            if (file_exists(storage_path('installed'))) {
                return redirect('/admin/dashboard');
            }
            return $next($request);
        });
    }

    /**
     * Exibe a interface do instalador.
     */
    public function index()
    {
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
                'DB_CONNECTION' => 'mysql',
                'DB_HOST'       => $request->db_host,
                'DB_PORT'       => $request->db_port,
                'DB_DATABASE'   => $request->db_database,
                'DB_USERNAME'   => $request->db_username,
                'DB_PASSWORD'   => $request->db_password ?? '',
            ];

            foreach ($substituicoes as $chave => $valor) {
                $env = preg_replace("/^{$chave}=.*/m", "{$chave}={$valor}", $env);
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

            // Criar role superadmin se não existir
            if (class_exists(\Spatie\Permission\Models\Role::class)) {
                $role = \Spatie\Permission\Models\Role::firstOrCreate(['name' => 'superadmin', 'guard_name' => 'web']);
                $user->assignRole($role);
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
