<?php

namespace App\Modules\Financeiro\Controllers;

use App\Http\Controllers\Controller;
use App\Modules\Financeiro\Models\Fornecedor;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

class FornecedorController extends Controller
{
    public function __construct() { $this->middleware('auth'); }

    public function index()
    {
        return view('admin.cadastros.fornecedores.index');
    }

    public function listar(Request $request): JsonResponse
    {
        $query = Fornecedor::doUsuario(auth()->id());
        if ($request->filled('search')) $query->where('nome', 'like', '%'.$request->search.'%');
        if ($request->filled('ativo'))  $query->where('ativo', $request->ativo);
        $dados = $query->orderBy('nome')->paginate($request->get('per_page', 15));
        return response()->json(['sucesso' => true, 'dados' => $dados->items(), 'total' => $dados->total(), 'paginas' => $dados->lastPage()]);
    }

    public function store(Request $request): JsonResponse
    {
        $request->validate(['nome' => 'required|string|max:150', 'tipo_pessoa' => 'required|in:fisica,juridica']);
        try {
            $dados = $request->all();
            $dados['user_id'] = auth()->id();
            $dados['ativo']   = true;
            $fornecedor = Fornecedor::create($dados);
            auditoria('criou', 'Financeiro', 'fornecedores', $fornecedor->id, null, $dados);
            return response()->json(['sucesso' => true, 'mensagem' => 'Fornecedor criado com sucesso!', 'dado' => $fornecedor], 201);
        } catch (\Throwable $e) {
            return response()->json(['sucesso' => false, 'mensagem' => 'Erro ao criar fornecedor.', 'erro' => config('app.debug') ? $e->getMessage() : null], 500);
        }
    }

    public function show(int $id): JsonResponse
    {
        $fornecedor = Fornecedor::doUsuario(auth()->id())->findOrFail($id);
        return response()->json(['sucesso' => true, 'dado' => $fornecedor]);
    }

    public function update(Request $request, int $id): JsonResponse
    {
        $request->validate(['nome' => 'required|string|max:150']);
        $fornecedor = Fornecedor::doUsuario(auth()->id())->findOrFail($id);
        $anterior   = $fornecedor->toArray();
        $fornecedor->update($request->except(['user_id']));
        auditoria('editou', 'Financeiro', 'fornecedores', $fornecedor->id, $anterior, $request->all());
        return response()->json(['sucesso' => true, 'mensagem' => 'Fornecedor atualizado!', 'dado' => $fornecedor->fresh()]);
    }

    public function destroy(int $id): JsonResponse
    {
        $fornecedor = Fornecedor::doUsuario(auth()->id())->findOrFail($id);
        auditoria('excluiu', 'Financeiro', 'fornecedores', $fornecedor->id, $fornecedor->toArray(), null);
        $fornecedor->delete();
        return response()->json(['sucesso' => true, 'mensagem' => 'Fornecedor excluído!']);
    }

    public function buscar(Request $request): JsonResponse
    {
        $fornecedores = Fornecedor::doUsuario(auth()->id())
            ->ativo()
            ->where('nome', 'like', '%'.$request->get('q', '').'%')
            ->select('id', 'nome', 'cpf_cnpj', 'email')
            ->limit(20)
            ->get();
        return response()->json(['sucesso' => true, 'dados' => $fornecedores]);
    }
}