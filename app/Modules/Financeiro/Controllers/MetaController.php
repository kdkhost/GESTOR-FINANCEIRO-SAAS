<?php
namespace App\Modules\Financeiro\Controllers;
use App\Http\Controllers\Controller;
use App\Modules\Financeiro\Models\Meta;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

class MetaController extends Controller
{
    public function __construct() { $this->middleware('auth'); }

    public function index(): JsonResponse
    {
        $dados = Meta::doUsuario(auth()->id())->orderBy('data_fim')->get()->map(fn($m) => array_merge($m->toArray(), ['percentual' => $m->percentual]));
        return response()->json(['sucesso'=>true,'dados'=>$dados]);
    }

    public function store(Request $request): JsonResponse
    {
        $request->validate(['nome'=>'required|string|max:150','valor_meta'=>'required','data_inicio'=>'required','data_fim'=>'required|after:data_inicio']);
        try {
            $dados = $request->all();
            $dados['user_id']    = auth()->id();
            $dados['valor_meta'] = moeda_para_float($dados['valor_meta']);
            $dados['valor_atual']= 0;
            $dados['status']     = 'em_andamento';
            $meta = Meta::create($dados);
            auditoria('criou','Financeiro','metas',$meta->id,null,$dados);
            return response()->json(['sucesso'=>true,'mensagem'=>'Meta criada com sucesso!','dado'=>$meta],201);
        } catch (\Throwable $e) {
            return response()->json(['sucesso'=>false,'mensagem'=>'Erro ao criar meta.','erro'=>config('app.debug')?$e->getMessage():null],500);
        }
    }

    public function show(int $id): JsonResponse
    {
        $meta = Meta::doUsuario(auth()->id())->findOrFail($id);
        return response()->json(['sucesso'=>true,'dado'=>array_merge($meta->toArray(),['percentual'=>$meta->percentual])]);
    }

    public function update(Request $request, int $id): JsonResponse
    {
        $meta     = Meta::doUsuario(auth()->id())->findOrFail($id);
        $anterior = $meta->toArray();
        $dados    = $request->all();
        if (isset($dados['valor_meta']))  $dados['valor_meta']  = moeda_para_float($dados['valor_meta']);
        if (isset($dados['valor_atual'])) $dados['valor_atual'] = moeda_para_float($dados['valor_atual']);
        $meta->update($dados);
        auditoria('editou','Financeiro','metas',$meta->id,$anterior,$dados);
        return response()->json(['sucesso'=>true,'mensagem'=>'Meta atualizada!','dado'=>$meta->fresh()]);
    }

    public function destroy(int $id): JsonResponse
    {
        $meta = Meta::doUsuario(auth()->id())->findOrFail($id);
        auditoria('excluiu','Financeiro','metas',$meta->id,$meta->toArray(),null);
        $meta->delete();
        return response()->json(['sucesso'=>true,'mensagem'=>'Meta excluída!']);
    }
}