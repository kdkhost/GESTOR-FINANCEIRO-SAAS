<?php
namespace App\Modules\Financeiro\Controllers;
use App\Http\Controllers\Controller;
use App\Modules\Financeiro\Models\Despesa;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

class DespesaController extends Controller
{


    public function index() { return view('admin.financeiro.despesas.index'); }

    public function listar(Request $request): JsonResponse
    {
        $query = Despesa::with(['categoria','contaBancaria','fornecedor'])->doUsuario(auth()->id());
        if ($request->filled('inicio'))       $query->whereDate('data_despesa', '>=', data_banco($request->inicio));
        if ($request->filled('fim'))          $query->whereDate('data_despesa', '<=', data_banco($request->fim));
        if ($request->filled('categoria_id')) $query->where('categoria_id', $request->categoria_id);
        if ($request->filled('search'))       $query->where('descricao', 'like', '%'.$request->search.'%');
        $dados = $query->orderByDesc('data_despesa')->paginate($request->get('per_page', 15));
        return response()->json(['sucesso'=>true,'dados'=>$dados->items(),'total'=>$dados->total(),'paginas'=>$dados->lastPage()]);
    }

    public function store(Request $request): JsonResponse
    {
        $request->validate(['descricao'=>'required|string|max:255','valor'=>'required','data_despesa'=>'required']);
        try {
            $dados = $request->all();
            $dados['user_id']      = auth()->id();
            $dados['valor']        = moeda_para_float($dados['valor']);
            $dados['data_despesa'] = data_banco($dados['data_despesa']);
            $despesa = Despesa::create($dados);
            auditoria('criou','Financeiro','despesas',$despesa->id,null,$dados);
            return response()->json(['sucesso'=>true,'mensagem'=>'Despesa criada com sucesso!','dado'=>$despesa->load('categoria')],201);
        } catch (\Throwable $e) {
            return response()->json(['sucesso'=>false,'mensagem'=>'Erro ao criar despesa.','erro'=>config('app.debug')?$e->getMessage():null],500);
        }
    }

    public function show(int $id): JsonResponse
    {
        $despesa = Despesa::with(['categoria','contaBancaria','fornecedor'])->doUsuario(auth()->id())->findOrFail($id);
        return response()->json(['sucesso'=>true,'dado'=>$despesa]);
    }

    public function update(Request $request, int $id): JsonResponse
    {
        $request->validate(['descricao'=>'required|string|max:255','valor'=>'required','data_despesa'=>'required']);
        $despesa  = Despesa::doUsuario(auth()->id())->findOrFail($id);
        $anterior = $despesa->toArray();
        $dados    = $request->all();
        if (isset($dados['valor']))        $dados['valor']        = moeda_para_float($dados['valor']);
        if (isset($dados['data_despesa'])) $dados['data_despesa'] = data_banco($dados['data_despesa']);
        $despesa->update($dados);
        auditoria('editou','Financeiro','despesas',$despesa->id,$anterior,$dados);
        return response()->json(['sucesso'=>true,'mensagem'=>'Despesa atualizada!','dado'=>$despesa->fresh()]);
    }

    public function destroy(int $id): JsonResponse
    {
        $despesa = Despesa::doUsuario(auth()->id())->findOrFail($id);
        auditoria('excluiu','Financeiro','despesas',$despesa->id,$despesa->toArray(),null);
        $despesa->delete();
        return response()->json(['sucesso'=>true,'mensagem'=>'Despesa excluída!']);
    }
}