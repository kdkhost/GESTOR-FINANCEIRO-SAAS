<?php
namespace App\Modules\Financeiro\Controllers;
use App\Http\Controllers\Controller;
use App\Modules\Financeiro\Models\Orcamento;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

class OrcamentoController extends Controller
{
    public function __construct() { $this->middleware('auth'); }

    public function index(): JsonResponse
    {
        $dados = Orcamento::with('categoria')->doUsuario(auth()->id())->orderBy('mes')->orderBy('ano')->get();
        return response()->json(['sucesso'=>true,'dados'=>$dados]);
    }

    public function store(Request $request): JsonResponse
    {
        $request->validate(['categoria_id'=>'required|integer','valor_limite'=>'required','mes'=>'required|integer|between:1,12','ano'=>'required|integer']);
        try {
            $dados = $request->all();
            $dados['user_id']      = auth()->id();
            $dados['valor_limite'] = moeda_para_float($dados['valor_limite']);
            $orc = Orcamento::create($dados);
            auditoria('criou','Financeiro','orcamentos',$orc->id,null,$dados);
            return response()->json(['sucesso'=>true,'mensagem'=>'Orçamento criado com sucesso!','dado'=>$orc->load('categoria')],201);
        } catch (\Throwable $e) {
            return response()->json(['sucesso'=>false,'mensagem'=>'Erro ao criar orçamento.','erro'=>config('app.debug')?$e->getMessage():null],500);
        }
    }

    public function show(int $id): JsonResponse
    {
        $orc = Orcamento::with('categoria')->doUsuario(auth()->id())->findOrFail($id);
        return response()->json(['sucesso'=>true,'dado'=>$orc]);
    }

    public function update(Request $request, int $id): JsonResponse
    {
        $orc      = Orcamento::doUsuario(auth()->id())->findOrFail($id);
        $anterior = $orc->toArray();
        $dados    = $request->all();
        if (isset($dados['valor_limite'])) $dados['valor_limite'] = moeda_para_float($dados['valor_limite']);
        $orc->update($dados);
        auditoria('editou','Financeiro','orcamentos',$orc->id,$anterior,$dados);
        return response()->json(['sucesso'=>true,'mensagem'=>'Orçamento atualizado!','dado'=>$orc->fresh()]);
    }

    public function destroy(int $id): JsonResponse
    {
        $orc = Orcamento::doUsuario(auth()->id())->findOrFail($id);
        auditoria('excluiu','Financeiro','orcamentos',$orc->id,$orc->toArray(),null);
        $orc->delete();
        return response()->json(['sucesso'=>true,'mensagem'=>'Orçamento excluído!']);
    }
}