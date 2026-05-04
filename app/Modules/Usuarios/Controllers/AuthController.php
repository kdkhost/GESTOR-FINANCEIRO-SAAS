<?php

namespace App\Modules\Usuarios\Controllers;

use App\Http\Controllers\Controller;
use App\Modules\Usuarios\Models\User;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Str;

class AuthController extends Controller
{
    /**
     * Exibe a tela de login.
     */
    public function showLogin()
    {
        return view('auth.login');
    }

    /**
     * Processa o login via AJAX ou form.
     */
    public function login(Request $request): JsonResponse
    {
        $request->validate([
            'email'    => 'required|email',
            'password' => 'required|string',
        ]);

        // Rate limiting por IP
        $chaveRateLimit = 'login:' . $request->ip();
        $maxTentativas  = (int) config('app.max_login_attempts', 5);

        if (RateLimiter::tooManyAttempts($chaveRateLimit, $maxTentativas)) {
            $segundos = RateLimiter::availableIn($chaveRateLimit);
            return response()->json([
                'sucesso'  => false,
                'mensagem' => "Muitas tentativas. Aguarde {$segundos} segundos.",
            ], 429);
        }

        $usuario = User::where('email', $request->email)->first();

        // Usuário não encontrado
        if (! $usuario) {
            RateLimiter::hit($chaveRateLimit, 60 * 15);
            return response()->json(['sucesso' => false, 'mensagem' => 'Credenciais inválidas.'], 401);
        }

        // Usuário bloqueado por tentativas
        if ($usuario->esta_bloqueado) {
            return response()->json([
                'sucesso'  => false,
                'mensagem' => 'Conta bloqueada temporariamente. Tente novamente mais tarde.',
            ], 423);
        }

        // Usuário inativo
        if ($usuario->status !== 'ativo') {
            return response()->json([
                'sucesso'  => false,
                'mensagem' => 'Conta inativa. Contate o administrador.',
            ], 403);
        }

        // Senha incorreta
        if (! Hash::check($request->password, $usuario->password)) {
            $usuario->incrementarTentativasLogin();
            RateLimiter::hit($chaveRateLimit, 60 * 15);
            return response()->json(['sucesso' => false, 'mensagem' => 'Credenciais inválidas.'], 401);
        }

        // Login bem-sucedido
        RateLimiter::clear($chaveRateLimit);
        $usuario->registrarAcesso($request->ip(), $request->userAgent());

        Auth::login($usuario, $request->boolean('remember'));

        auditoria('login', 'Usuarios', 'users', $usuario->id, null, null, 'Login realizado');

        return response()->json([
            'sucesso'  => true,
            'mensagem' => 'Bem-vindo, ' . $usuario->name . '!',
            'redirect' => route('admin.dashboard.index'),
        ]);
    }

    /**
     * Logout.
     */
    public function logout(Request $request): \Illuminate\Http\RedirectResponse
    {
        auditoria('logout', 'Usuarios', 'users', auth()->id(), null, null);
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        return redirect()->route('login');
    }

    /**
     * Exibe o formulário de esqueci a senha.
     */
    public function showEsqueciSenha()
    {
        return view('auth.esqueci-senha');
    }

    /**
     * Envia o link de redefinição via PHPMailer.
     */
    public function enviarLinkRedefinicao(Request $request): JsonResponse
    {
        $request->validate(['email' => 'required|email|exists:users,email']);

        $token   = Str::random(64);
        $usuario = User::where('email', $request->email)->first();

        \DB::table('password_reset_tokens')->updateOrInsert(
            ['email' => $request->email],
            ['token' => Hash::make($token), 'created_at' => now()]
        );

        // Envia e-mail via PHPMailer
        try {
            $mail = new \PHPMailer\PHPMailer\PHPMailer(true);
            $mail->isSMTP();
            $mail->Host       = configuracao('smtp_host', config('mail.mailers.smtp.host'));
            $mail->SMTPAuth   = true;
            $mail->Username   = configuracao('smtp_usuario', config('mail.username'));
            $mail->Password   = configuracao('smtp_senha', config('mail.password'));
            $mail->SMTPSecure = configuracao('smtp_criptografia', 'tls');
            $mail->Port       = (int) configuracao('smtp_porta', 587);
            $mail->CharSet    = 'UTF-8';
            $mail->setFrom(configuracao('smtp_remetente', config('mail.from.address')), configuracao('smtp_nome_remetente', 'FinanceiroSaaS'));
            $mail->addAddress($usuario->email, $usuario->name);
            $mail->isHTML(true);
            $mail->Subject = 'Redefinição de senha — ' . configuracao('sistema_nome', 'FinanceiroSaaS');
            $url = route('auth.redefinir-senha', $token);
            $mail->Body = "<p>Olá, {$usuario->name}.</p><p>Clique no link para redefinir sua senha:</p><p><a href='{$url}'>{$url}</a></p><p>O link expira em 60 minutos.</p>";
            $mail->send();
        } catch (\Throwable $e) {
            // Em desenvolvimento apenas loga
            \Log::error('Erro ao enviar e-mail de redefinição: ' . $e->getMessage());
        }

        return response()->json(['sucesso' => true, 'mensagem' => 'Se o e-mail estiver cadastrado, você receberá as instruções em breve.']);
    }

    /**
     * Exibe o formulário de redefinição de senha.
     */
    public function showRedefinirSenha(string $token)
    {
        return view('auth.redefinir-senha', compact('token'));
    }

    /**
     * Processa a redefinição de senha.
     */
    public function redefinirSenha(Request $request): JsonResponse
    {
        $request->validate([
            'token'    => 'required',
            'email'    => 'required|email|exists:users,email',
            'password' => 'required|min:8|confirmed',
        ]);

        $registro = \DB::table('password_reset_tokens')->where('email', $request->email)->first();

        if (! $registro || ! Hash::check($request->token, $registro->token)) {
            return response()->json(['sucesso' => false, 'mensagem' => 'Token inválido ou expirado.'], 422);
        }

        if (now()->diffInMinutes($registro->created_at) > 60) {
            return response()->json(['sucesso' => false, 'mensagem' => 'Token expirado. Solicite um novo link.'], 422);
        }

        User::where('email', $request->email)->update([
            'password'         => Hash::make($request->password),
            'tentativas_login' => 0,
            'bloqueado_ate'    => null,
        ]);

        \DB::table('password_reset_tokens')->where('email', $request->email)->delete();

        return response()->json(['sucesso' => true, 'mensagem' => 'Senha redefinida com sucesso! Faça login.', 'redirect' => route('login')]);
    }
}
