<?php

namespace App\Modules\Configuracoes\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Crypt;

class SocialLoginController extends Controller
{
    private array $providers = ['google', 'facebook'];

    /**
     * Exibe a página de configurações
     */
    public function index()
    {
        $this->exigirSuperAdmin();
        return view('admin.configuracoes.social-login');
    }

    /**
     * Lista configurações atuais
     */
    public function listar(): JsonResponse
    {
        $this->exigirSuperAdmin();

        $configs = [];
        foreach ($this->providers as $provider) {
            $configs[$provider] = [
                'ativado' => configuracao("social_{$provider}_ativado", '0') === '1',
                'client_id' => $this->descriptografar(configuracao("social_{$provider}_client_id")),
                'client_secret' => $this->descriptografar(configuracao("social_{$provider}_client_secret")),
                'redirect_url' => configuracao("social_{$provider}_redirect_url", url("/auth/{$provider}/callback")),
            ];
        }

        return response()->json([
            'sucesso' => true,
            'dados' => $configs,
        ]);
    }

    /**
     * Salva configurações
     */
    public function salvar(Request $request): JsonResponse
    {
        $this->exigirSuperAdmin();

        $provider = $request->input('provider');
        if (!in_array($provider, $this->providers)) {
            return response()->json(['sucesso' => false, 'mensagem' => 'Provider invalido'], 400);
        }

        $ativado = $request->boolean('ativado', false);
        $clientId = $request->input('client_id');
        $clientSecret = $request->input('client_secret');
        $redirectUrl = $request->input('redirect_url', url("/auth/{$provider}/callback"));

        // Validações
        if ($ativado) {
            if (empty($clientId) || empty($clientSecret)) {
                return response()->json([
                    'sucesso' => false,
                    'mensagem' => 'Client ID e Client Secret sao obrigatorios para ativar o login social.'
                ], 422);
            }
        }

        // Salva configurações (criptografando credenciais sensíveis)
        salvar_configuracao("social_{$provider}_ativado", $ativado ? '1' : '0', 'booleano', "Login {$provider} - Ativado", 'geral', false, true);

        if (!empty($clientId)) {
            salvar_configuracao("social_{$provider}_client_id", $this->criptografar($clientId), 'texto', "Login {$provider} - Client ID", 'geral', true, true);
        }

        if (!empty($clientSecret)) {
            salvar_configuracao("social_{$provider}_client_secret", $this->criptografar($clientSecret), 'texto', "Login {$provider} - Client Secret", 'geral', true, true);
        }

        salvar_configuracao("social_{$provider}_redirect_url", $redirectUrl, 'texto', "Login {$provider} - Redirect URL", 'geral', false, true);

        auditoria('configurou', 'Configuracoes', 'configuracoes', null, null, [
            'provider' => $provider,
            'ativado' => $ativado,
        ], 'Configuracao de login social');

        return response()->json([
            'sucesso' => true,
            'mensagem' => "Configuracoes do {$provider} salvas com sucesso!",
        ]);
    }

    /**
     * Testa a conexão com o provider
     */
    public function testar(Request $request): JsonResponse
    {
        $this->exigirSuperAdmin();

        $provider = $request->input('provider');
        if (!in_array($provider, $this->providers)) {
            return response()->json(['sucesso' => false, 'mensagem' => 'Provider invalido'], 400);
        }

        $clientId = $this->descriptografar(configuracao("social_{$provider}_client_id"));
        $clientSecret = $this->descriptografar(configuracao("social_{$provider}_client_secret"));

        if (empty($clientId) || empty($clientSecret)) {
            return response()->json([
                'sucesso' => false,
                'mensagem' => 'Credenciais nao configuradas. Configure Client ID e Client Secret primeiro.'
            ], 422);
        }

        // Aqui poderia fazer uma requisição de teste à API do provider
        // Por enquanto, apenas verifica se as credenciais existem

        return response()->json([
            'sucesso' => true,
            'mensagem' => "Credenciais do {$provider} configuradas corretamente. Teste de conexao manual necessario.",
        ]);
    }

    /**
     * Criptografa valor sensível
     */
    private function criptografar(?string $valor): ?string
    {
        if (empty($valor)) {
            return null;
        }
        return Crypt::encryptString($valor);
    }

    /**
     * Descriptografa valor sensível
     */
    private function descriptografar(?string $valor): ?string
    {
        if (empty($valor)) {
            return null;
        }
        try {
            return Crypt::decryptString($valor);
        } catch (\Exception $e) {
            return $valor; // Se não estava criptografado, retorna como está
        }
    }

    /**
     * Exige que o usuário seja superadmin
     */
    private function exigirSuperAdmin(): void
    {
        abort_unless(auth()->check() && auth()->user()?->is_superadmin, 403, 'Apenas superadmins podem configurar login social.');
    }
}
