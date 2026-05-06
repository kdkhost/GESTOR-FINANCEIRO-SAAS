<?php
namespace App\Modules\Financeiro\Controllers;
use App\Http\Controllers\Controller;
use App\Modules\Financeiro\Models\Meta;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

class MetaController extends Controller
{
    public function index(Request $request)
    {
        if ($request->expectsJson() || $request->ajax()) {
            $dados = Meta::doUsuario(auth()->id())
                ->orderBy('data_prazo')
                ->get()
                ->map(fn($m) => array_merge($m->toArray(), [
                    'percentual' => round($m->percentual, 1),
                    'data_inicio' => $m->data_inicio ? $m->data_inicio->format('d/m/Y') : null,
                    'data_prazo' => $m->data_prazo ? $m->data_prazo->format('d/m/Y') : null,
                    'created_at' => $m->created_at ? $m->created_at->format('d/m/Y H:i') : null,
                    'updated_at' => $m->updated_at ? $m->updated_at->format('d/m/Y H:i') : null,
                ]));
            return response()->json(['sucesso' => true, 'dados' => $dados]);
        }
        return view('admin.financeiro.metas.index');
    }

    public function store(Request $request): JsonResponse
    {
        $request->validate([
            'titulo'      => 'required|string|max:150',
            'valor_alvo'  => 'required',
            'data_inicio' => 'required',
            'data_prazo'  => 'required',
        ]);
        try {
            $dados = $request->all();
            $dados['user_id']    = auth()->id();
            $dados['valor_alvo'] = moeda_para_float($dados['valor_alvo']);
            $dados['valor_atual']= isset($dados['valor_atual']) ? moeda_para_float($dados['valor_atual']) : 0;
            $dados['status']     = $dados['status'] ?? 'ativa';
            if (isset($dados['data_inicio']) && str_contains($dados['data_inicio'], '/')) $dados['data_inicio'] = data_banco($dados['data_inicio']);
            if (isset($dados['data_prazo'])  && str_contains($dados['data_prazo'],  '/')) $dados['data_prazo']  = data_banco($dados['data_prazo']);
            $meta = Meta::create($dados);
            auditoria('criou','Financeiro','metas_financeiras',$meta->id,null,$dados);
            return response()->json(['sucesso'=>true,'mensagem'=>'Meta criada com sucesso!','dado'=>$meta],201);
        } catch (\Throwable $e) {
            return response()->json(['sucesso'=>false,'mensagem'=>'Erro ao criar meta.','erro'=>config('app.debug')?$e->getMessage():null],500);
        }
    }

    public function show(int $id): JsonResponse
    {
        $meta = Meta::doUsuario(auth()->id())->findOrFail($id);
        $dado = array_merge($meta->toArray(),[
            'percentual' => round($meta->percentual,1),
            'data_inicio' => $meta->data_inicio ? $meta->data_inicio->format('d/m/Y') : null,
            'data_prazo' => $meta->data_prazo ? $meta->data_prazo->format('d/m/Y') : null,
            'created_at' => $meta->created_at ? $meta->created_at->format('d/m/Y H:i') : null,
            'updated_at' => $meta->updated_at ? $meta->updated_at->format('d/m/Y H:i') : null,
        ]);
        return response()->json(['sucesso'=>true,'dado'=>$dado]);
    }

    public function update(Request $request, int $id): JsonResponse
    {
        $meta     = Meta::doUsuario(auth()->id())->findOrFail($id);
        $anterior = $meta->toArray();
        $dados    = $request->all();
        if (isset($dados['valor_alvo']))  $dados['valor_alvo']  = moeda_para_float($dados['valor_alvo']);
        if (isset($dados['valor_atual'])) $dados['valor_atual'] = moeda_para_float($dados['valor_atual']);
        if (isset($dados['data_inicio']) && str_contains($dados['data_inicio'],'/')) $dados['data_inicio'] = data_banco($dados['data_inicio']);
        if (isset($dados['data_prazo'])  && str_contains($dados['data_prazo'], '/')) $dados['data_prazo']  = data_banco($dados['data_prazo']);
        $meta->update($dados);
        auditoria('editou','Financeiro','metas_financeiras',$meta->id,$anterior,$dados);
        return response()->json(['sucesso'=>true,'mensagem'=>'Meta atualizada!','dado'=>$meta->fresh()]);
    }

    public function destroy(int $id): JsonResponse
    {
        $meta = Meta::doUsuario(auth()->id())->findOrFail($id);
        auditoria('excluiu','Financeiro','metas_financeiras',$meta->id,$meta->toArray(),null);
        $meta->delete();
        return response()->json(['sucesso'=>true,'mensagem'=>'Meta excluida!']);
    }
}