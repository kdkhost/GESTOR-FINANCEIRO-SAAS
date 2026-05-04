<?php
namespace App\Modules\Financeiro\Controllers;
use App\Http\Controllers\Controller;
use App\Modules\Financeiro\Models\Receita;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

class ReceitaController extends Controller
{


    public function index() { return view('admin.financeiro.receitas.index'); }

    public function listar(Request $request): JsonResponse
    {
        $query = Receita::with(['categoria','contaBancaria','cliente'])->doUsuario(auth()->id());
        if ($request->filled('inicio'))       $query->whereDate('data_receita', '>=', data_banco($request->inicio));
        if ($request->filled('fim'))          $query->whereDate('data_receita', '<=', data_banco($request->fim));
        if ($request->filled('categoria_id')) $query->where('categoria_id', $request->categoria_id);
        if ($request->filled('search'))       $query->where('descricao', 'like', '%'.$request->search.'%');
        $dados = $query->orderByDesc('data_receita')->paginate($request->get('per_page', 15));
        return response()->json(['sucesso'=>true,'dados'=>$dados->items(),'total'=>$dados->total(),'paginas'=>$dados->lastPage()]);
    }

    public function store(Request $request): JsonResponse
    {
        $request->validate(['descricao'=>'required|string|max:255','valor'=>'required','data_receita'=>'required']);
        try {
            $dados = $request->all();
            $dados['user_id']      = auth()->id();
            $dados['valor']        = moeda_para_float($dados['valor']);
            $dados['data_receita'] = data_banco($dados['data_receita']);
            $receita = Receita::create($dados);
            auditoria('criou','Financeiro','receitas',$receita->id,null,$dados);
            return response()->json(['sucesso'=>true,'mensagem'=>'Receita criada com sucesso!','dado'=>$receita->load('categoria')],201);
        } catch (\Throwable $e) {
            return response()->json(['sucesso'=>false,'mensagem'=>'Erro ao criar receita.','erro'=>config('app.debug')?$e->getMessage():null],500);
        }
    }

    public function show(int $id): JsonResponse
    {
        $receita = Receita::with(['categoria','contaBancaria','cliente'])->doUsuario(auth()->id())->findOrFail($id);
        return response()->json(['sucesso'=>true,'dado'=>$receita]);
    }

    public function update(Request $request, int $id): JsonResponse
    {
        $request->validate(['descricao'=>'required|string|max:255','valor'=>'required','data_receita'=>'required']);
        $receita  = Receita::doUsuario(auth()->id())->findOrFail($id);
        $anterior = $receita->toArray();
        $dados    = $request->all();
        if (isset($dados['valor']))        $dados['valor']        = moeda_para_float($dados['valor']);
        if (isset($dados['data_receita'])) $dados['data_receita'] = data_banco($dados['data_receita']);
        $receita->update($dados);
        auditoria('editou','Financeiro','receitas',$receita->id,$anterior,$dados);
        return response()->json(['sucesso'=>true,'mensagem'=>'Receita atualizada!','dado'=>$receita->fresh()]);
    }

    public function destroy(int $id): JsonResponse
    {
        $receita = Receita::doUsuario(auth()->id())->findOrFail($id);
        auditoria('excluiu','Financeiro','receitas',$receita->id,$receita->toArray(),null);
        $receita->delete();
        return response()->json(['sucesso'=>true,'mensagem'=>'Receita excluída!']);
    }
}