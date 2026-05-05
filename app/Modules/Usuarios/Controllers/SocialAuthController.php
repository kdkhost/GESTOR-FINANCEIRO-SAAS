<?php

namespace App\Modules\Usuarios\Controllers;

use App\Http\Controllers\Controller;
use App\Modules\Usuarios\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Laravel\Socialite\Facades\Socialite;

class SocialAuthController extends Controller
{
    /**
     * Redireciona para o provider OAuth
     */
    public function redirect(string $provider)
    {
        // Lista de providers permitidos
        $providers = ['google', 'facebook'];

        if (!in_array($provider, $providers)) {
            return redirect('/login')->with('erro', 'Provider inválido');
        }

        // Verifica se o provider está ativado
        if (!$this->isProviderAtivado($provider)) {
            return redirect('/login')->with('erro', 'Login social ' . $provider . ' não está ativado');
        }

        // Configura o Socialite dinamicamente
        $this->configurarProvider($provider);

        return Socialite::driver($provider)->redirect();
    }

    /**
     * Callback do provider OAuth
     */
    public function callback(string $provider)
    {
        try {
            // Recupera dados do usuário do provider
            $socialUser = Socialite::driver($provider)->user();

            // Busca usuário pelo email
            $user = User::where('email', $socialUser->getEmail())->first();

            // Se não existir, cria novo usuário
            if (!$user) {
                $user = $this->createUserFromSocial($socialUser, $provider);
            } else {
                // Atualiza avatar se não tiver
                if (!$user->avatar || $user->avatar === 'avatar-padrao.png') {
                    $avatarPath = $this->downloadAvatar($socialUser->getAvatar(), $provider);
                    if ($avatarPath) {
                        $user->update(['avatar' => $avatarPath]);
                    }
                }
            }

            // Verifica se usuário está ativo
            if ($user->status !== 'ativo') {
                return redirect('/login')->with('erro', 'Conta inativa. Contate o administrador.');
            }

            // Faz login
            Auth::login($user, true);
            $user->registrarAcesso(request()->ip(), request()->userAgent());

            auditoria('login', 'Usuarios', 'users', $user->id, null, null, "Login via {$provider}");

            return redirect('/admin/dashboard');

        } catch (\Exception $e) {
            return redirect('/login')->with('erro', 'Erro ao fazer login: ' . $e->getMessage());
        }
    }

    /**
     * Cria novo usuário a partir dos dados sociais
     */
    private function createUserFromSocial($socialUser, string $provider): User
    {
        // Baixa o avatar
        $avatarPath = $this->downloadAvatar($socialUser->getAvatar(), $provider);

        // Separa nome em partes
        $nameParts = explode(' ', $socialUser->getName());
        $firstName = $nameParts[0] ?? '';
        $lastName = $nameParts[1] ?? '';

        // Cria usuário
        $user = User::create([
            'name' => $socialUser->getName(),
            'email' => $socialUser->getEmail(),
            'password' => Hash::make(Str::random(24)), // Senha aleatória
            'tipo' => 'usuario',
            'status' => 'ativo',
            'email_verified_at' => now(),
            'avatar' => $avatarPath ?? 'avatar-padrao.png',
            // Dados opcionais que podem vir do provider
            'telefone' => $socialUser->user['phone'] ?? null,
        ]);

        auditoria('criou', 'Usuarios', 'users', $user->id, null, [
            'name' => $user->name,
            'email' => $user->email,
            'tipo' => $user->tipo,
            'provider' => $provider,
        ], 'Cadastro via ' . $provider);

        return $user;
    }

    /**
     * Baixa o avatar do usuário
     */
    private function downloadAvatar(?string $avatarUrl, string $provider): ?string
    {
        if (!$avatarUrl) {
            return null;
        }

        try {
            // Gera nome único para o arquivo
            $filename = 'avatars/' . $provider . '_' . time() . '_' . Str::random(10) . '.jpg';
            $path = public_path('storage/' . $filename);

            // Cria diretório se não existir
            if (!file_exists(dirname($path))) {
                mkdir(dirname($path), 0755, true);
            }

            // Baixa a imagem
            $imageContent = file_get_contents($avatarUrl);
            if ($imageContent === false) {
                return null;
            }

            // Salva a imagem
            file_put_contents($path, $imageContent);

            return $filename;

        } catch (\Exception $e) {
            return null;
        }
    }

    /**
     * Verifica se o provider está ativado no banco
     */
    private function isProviderAtivado(string $provider): bool
    {
        return configuracao("social_{$provider}_ativado", '0') === '1';
    }

    /**
     * Configura o Socialite dinamicamente com credenciais do banco
     */
    private function configurarProvider(string $provider): void
    {
        $clientId = $this->descriptografar(configuracao("social_{$provider}_client_id"));
        $clientSecret = $this->descriptografar(configuracao("social_{$provider}_client_secret"));
        $redirectUrl = configuracao("social_{$provider}_redirect_url", url("/auth/{$provider}/callback"));

        if (empty($clientId) || empty($clientSecret)) {
            throw new \Exception('Credenciais do ' . $provider . ' não configuradas');
        }

        config(["services.{$provider}.client_id" => $clientId]);
        config(["services.{$provider}.client_secret" => $clientSecret]);
        config(["services.{$provider}.redirect" => $redirectUrl]);
    }

    /**
     * Descriptografa valor
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
}
