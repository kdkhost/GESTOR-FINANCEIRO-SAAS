<?php
namespace App\Modules\Financeiro\Controllers;
use App\Http\Controllers\Controller;
use App\Modules\Financeiro\Models\Categoria;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

class CategoriaController extends Controller
{
    public function index(Request $request)
    {
        // Se requisicao AJAX/JSON, retorna dados
        if ($request->expectsJson() || $request->ajax()) {
            $dados = Categoria::with('subcategorias')
                ->doUsuario(auth()->id())
                ->ativo()
                ->orderBy('nome')
                ->get();
            return response()->json(['sucesso' => true, 'dados' => $dados]);
        }
        // Requisicao normal: retorna view
        return view('admin.cadastros.categorias.index');
    }

    public function store(Request $request): JsonResponse
    {
        $request->validate([
            'nome' => 'required|string|max:100',
            'tipo' => 'required|in:receita,despesa,ambos',
        ]);
        $categoria = Categoria::create([
            'user_id' => auth()->id(),
            'nome'    => $request->nome,
            'tipo'    => $request->tipo,
            'icone'   => $request->icone ?? 'bi-tag',
            'cor'     => $request->cor ?? '#6c757d',
            'ativo'   => true,
        ]);
        auditoria('criou', 'Financeiro', 'categorias', $categoria->id, null, $request->all());
        return response()->json(['sucesso' => true, 'mensagem' => 'Categoria criada com sucesso!', 'dado' => $categoria], 201);
    }

    public function show(int $id): JsonResponse
    {
        $categoria = Categoria::with('subcategorias')->doUsuario(auth()->id())->findOrFail($id);
        return response()->json(['sucesso' => true, 'dado' => $categoria]);
    }

    public function update(Request $request, int $id): JsonResponse
    {
        $request->validate(['nome' => 'required|string|max:100', 'tipo' => 'required|in:receita,despesa,ambos']);
        $categoria = Categoria::doUsuario(auth()->id())->findOrFail($id);
        $anterior  = $categoria->toArray();
        $categoria->update($request->only(['nome', 'tipo', 'icone', 'cor', 'ativo']));
        auditoria('editou', 'Financeiro', 'categorias', $categoria->id, $anterior, $request->all());
        return response()->json(['sucesso' => true, 'mensagem' => 'Categoria atualizada!', 'dado' => $categoria->fresh()]);
    }

    public function destroy(int $id): JsonResponse
    {
        $categoria = Categoria::doUsuario(auth()->id())->findOrFail($id);
        auditoria('excluiu', 'Financeiro', 'categorias', $categoria->id, $categoria->toArray(), null);
        $categoria->delete();
        return response()->json(['sucesso' => true, 'mensagem' => 'Categoria excluida!']);
    }
}