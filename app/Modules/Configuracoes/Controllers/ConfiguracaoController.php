<?php
namespace App\Modules\Configuracoes\Controllers;

use App\Http\Controllers\Controller;
use App\Modules\Configuracoes\Models\Configuracao;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Storage;

class ConfiguracaoController extends Controller
{
    public function index()
    {
        return view('admin.configuracoes.index');
    }

    public function salvar(Request $request): JsonResponse
    {
        try {
            $acao = $request->input('acao');

            // Testar SMTP
            if ($acao === 'testar_smtp') {
                return $this->testarSmtp($request);
            }

            // Campos de arquivo (logo, favicon)
            if ($request->hasFile('sistema_logo')) {
                $path = $request->file('sistema_logo')->store('configuracoes', 'public');
                Configuracao::definir('sistema_logo', $path, 'geral');
            }
            if ($request->hasFile('sistema_favicon')) {
                $path = $request->file('sistema_favicon')->store('configuracoes', 'public');
                Configuracao::definir('sistema_favicon', $path, 'geral');
            }

            // Campos de texto
            $camposGeral = [
                'sistema_nome', 'sistema_descricao', 'sistema_proprietario', 'sistema_moeda',
                'whatsapp_suporte',
                'landing_badge', 'landing_titulo', 'landing_subtitulo', 'landing_descricao',
                'landing_cta_primario', 'landing_cta_secundario',
            ];
            $camposSmtp  = ['mail_driver', 'mail_host', 'mail_port', 'mail_encryption', 'mail_username', 'mail_password', 'mail_from_name', 'mail_from_address'];
            $camposApar  = ['cor_primaria', 'cor_secundaria', 'tema_padrao'];

            foreach (array_merge($camposGeral, $camposSmtp, $camposApar) as $campo) {
                if ($request->has($campo)) {
                    $grupo = in_array($campo, $camposSmtp) ? 'smtp' : (in_array($campo, $camposApar) ? 'aparencia' : 'geral');
                    Configuracao::definir($campo, $request->input($campo), $grupo);
                }
            }

            // Limpar cache do helper configuracao()
            // O helper usa static $cache, que e por request — nao precisa limpar manualmente

            return response()->json(['sucesso' => true, 'mensagem' => 'Configuracoes salvas com sucesso!']);
        } catch (\Throwable $e) {
            return response()->json([
                'sucesso'  => false,
                'mensagem' => 'Erro ao salvar configuracoes.',
                'erro'     => config('app.debug') ? $e->getMessage() : null,
            ], 500);
        }
    }

    private function testarSmtp(Request $request): JsonResponse
    {
        $request->validate(['email_teste' => 'required|email']);
        try {
            \Illuminate\Support\Facades\Mail::raw('Teste de configuracao SMTP do FinanceiroSaaS.', function ($msg) use ($request) {
                $msg->to($request->email_teste)->subject('Teste SMTP - FinanceiroSaaS');
            });
            return response()->json(['sucesso' => true, 'mensagem' => 'E-mail de teste enviado com sucesso!']);
        } catch (\Throwable $e) {
            return response()->json(['sucesso' => false, 'mensagem' => 'Falha no envio: ' . $e->getMessage()], 422);
        }
    }
}
