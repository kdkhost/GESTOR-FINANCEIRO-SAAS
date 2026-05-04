<?php
namespace App\Modules\Financeiro\Controllers;
use App\Http\Controllers\Controller;
use App\Modules\Financeiro\Models\Recorrencia;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

class RecorrenciaController extends Controller
{
    public function index(Request $request)
    {
        if ($request->expectsJson() || $request->ajax()) {
            $dados = Recorrencia::with('categoria')->doUsuario(auth()->id())->orderBy('descricao')->get();
            return response()->json(['sucesso' => true, 'dados' => $dados]);
        }
        return view('admin.financeiro.recorrencias.index');
    }

    public function store(Request $request): JsonResponse
    {
        $request->validate(['descricao'=>'required|string|max:255','tipo'=>'required|in:pagar,receber','valor'=>'required','dia_vencimento'=>'required|integer|between:1,31','data_inicio'=>'required']);
        try {
            $dados = $request->all();
            $dados['user_id'] = auth()->id();
            $dados['valor']   = moeda_para_float($dados['valor']);
            $dados['ativo']   = isset($dados['ativo']) ? (bool)$dados['ativo'] : true;
            if (isset($dados['data_inicio']) && str_contains($dados['data_inicio'],'/')) $dados['data_inicio'] = data_banco($dados['data_inicio']);
            $rec = Recorrencia::create($dados);
            auditoria('criou','Financeiro','recorrencias',$rec->id,null,$dados);
            return response()->json(['sucesso'=>true,'mensagem'=>'Recorrencia criada com sucesso!','dado'=>$rec],201);
        } catch (\Throwable $e) {
            return response()->json(['sucesso'=>false,'mensagem'=>'Erro ao criar recorrencia.','erro'=>config('app.debug')?$e->getMessage():null],500);
        }
    }

    public function show(int $id): JsonResponse
    {
        $rec = Recorrencia::with('categoria')->doUsuario(auth()->id())->findOrFail($id);
        return response()->json(['sucesso'=>true,'dado'=>$rec]);
    }

    public function update(Request $request, int $id): JsonResponse
    {
        $rec      = Recorrencia::doUsuario(auth()->id())->findOrFail($id);
        $anterior = $rec->toArray();
        $dados    = $request->all();
        if (isset($dados['valor'])) $dados['valor'] = moeda_para_float($dados['valor']);
        if (isset($dados['ativo'])) $dados['ativo'] = (bool)$dados['ativo'];
        $rec->update($dados);
        auditoria('editou','Financeiro','recorrencias',$rec->id,$anterior,$dados);
        return response()->json(['sucesso'=>true,'mensagem'=>'Recorrencia atualizada!','dado'=>$rec->fresh()]);
    }

    public function destroy(int $id): JsonResponse
    {
        $rec = Recorrencia::doUsuario(auth()->id())->findOrFail($id);
        auditoria('excluiu','Financeiro','recorrencias',$rec->id,$rec->toArray(),null);
        $rec->delete();
        return response()->json(['sucesso'=>true,'mensagem'=>'Recorrencia excluida!']);
    }
}