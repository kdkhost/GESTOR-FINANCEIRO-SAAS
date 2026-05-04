<?php
namespace App\Modules\Financeiro\Controllers;
use App\Http\Controllers\Controller;
use App\Modules\Financeiro\Models\Subcategoria;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

class SubcategoriaController extends Controller
{
    public function index(Request $request)
    {
        if ($request->expectsJson() || $request->ajax()) {
            $dados = Subcategoria::with('categoria')
                ->where('user_id', auth()->id())
                ->ativo()
                ->orderBy('nome')
                ->get();
            return response()->json(['sucesso' => true, 'dados' => $dados]);
        }
        return view('admin.cadastros.categorias.index');
    }

    public function store(Request $request): JsonResponse
    {
        $request->validate(['categoria_id' => 'required|integer', 'nome' => 'required|string|max:100']);
        $sub = Subcategoria::create(['user_id'=>auth()->id(),'categoria_id'=>$request->categoria_id,'nome'=>$request->nome,'ativo'=>true]);
        auditoria('criou','Financeiro','subcategorias',$sub->id,null,$request->all());
        return response()->json(['sucesso'=>true,'mensagem'=>'Subcategoria criada!','dado'=>$sub->load('categoria')],201);
    }

    public function show(int $id): JsonResponse
    {
        $sub = Subcategoria::with('categoria')->where('user_id',auth()->id())->findOrFail($id);
        return response()->json(['sucesso'=>true,'dado'=>$sub]);
    }

    public function update(Request $request, int $id): JsonResponse
    {
        $request->validate(['nome'=>'required|string|max:100']);
        $sub=$sub=Subcategoria::where('user_id',auth()->id())->findOrFail($id);
        $anterior=$sub->toArray();
        $sub->update($request->only(['categoria_id','nome','ativo']));
        auditoria('editou','Financeiro','subcategorias',$sub->id,$anterior,$request->all());
        return response()->json(['sucesso'=>true,'mensagem'=>'Subcategoria atualizada!','dado'=>$sub->fresh()]);
    }

    public function destroy(int $id): JsonResponse
    {
        $sub=Subcategoria::where('user_id',auth()->id())->findOrFail($id);
        auditoria('excluiu','Financeiro','subcategorias',$sub->id,$sub->toArray(),null);
        $sub->delete();
        return response()->json(['sucesso'=>true,'mensagem'=>'Subcategoria excluida!']);
    }
}