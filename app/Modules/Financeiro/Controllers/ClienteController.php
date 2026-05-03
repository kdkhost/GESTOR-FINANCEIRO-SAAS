<?php

namespace App\Modules\Financeiro\Controllers;

use App\Http\Controllers\Controller;
use App\Modules\Financeiro\Models\Cliente;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

class ClienteController extends Controller
{
    public function __construct() { $this->middleware('auth'); }

    public function index()
    {
        return view('admin.cadastros.clientes.index');
    }

    public function listar(Request $request): JsonResponse
    {
        $query = Cliente::doUsuario(auth()->id());
        if ($request->filled('search')) $query->where('nome', 'like', '%'.$request->search.'%');
        if ($request->filled('ativo'))  $query->where('ativo', $request->ativo);
        $dados = $query->orderBy('nome')->paginate($request->get('per_page', 15));
        return response()->json(['sucesso' => true, 'dados' => $dados->items(), 'total' => $dados->total(), 'paginas' => $dados->lastPage()]);
    }

    public function store(Request $request): JsonResponse
    {
        $request->validate([
            'nome'        => 'required|string|max:150',
            'tipo_pessoa' => 'required|in:fisica,juridica',
        ]);

        try {
            $dados = $request->all();
            $dados['user_id'] = auth()->id();
            $dados['ativo']   = true;
            $cliente = Cliente::create($dados);
            auditoria('criou', 'Financeiro', 'clientes', $cliente->id, null, $dados);
            return response()->json(['sucesso' => true, 'mensagem' => 'Cliente criado com sucesso!', 'dado' => $cliente], 201);
        } catch (\Throwable $e) {
            return response()->json(['sucesso' => false, 'mensagem' => 'Erro ao criar cliente.', 'erro' => config('app.debug') ? $e->getMessage() : null], 500);
        }
    }

    public function show(int $id): JsonResponse
    {
        $cliente = Cliente::doUsuario(auth()->id())->findOrFail($id);
        return response()->json(['sucesso' => true, 'dado' => $cliente]);
    }

    public function update(Request $request, int $id): JsonResponse
    {
        $request->validate(['nome' => 'required|string|max:150']);
        $cliente  = Cliente::doUsuario(auth()->id())->findOrFail($id);
        $anterior = $cliente->toArray();
        $cliente->update($request->except(['user_id']));
        auditoria('editou', 'Financeiro', 'clientes', $cliente->id, $anterior, $request->all());
        return response()->json(['sucesso' => true, 'mensagem' => 'Cliente atualizado!', 'dado' => $cliente->fresh()]);
    }

    public function destroy(int $id): JsonResponse
    {
        $cliente = Cliente::doUsuario(auth()->id())->findOrFail($id);
        auditoria('excluiu', 'Financeiro', 'clientes', $cliente->id, $cliente->toArray(), null);
        $cliente->delete();
        return response()->json(['sucesso' => true, 'mensagem' => 'Cliente excluído!']);
    }

    public function buscar(Request $request): JsonResponse
    {
        $clientes = Cliente::doUsuario(auth()->id())
            ->ativo()
            ->where('nome', 'like', '%'.$request->get('q', '').'%')
            ->select('id', 'nome', 'cpf_cnpj', 'email')
            ->limit(20)
            ->get();
        return response()->json(['sucesso' => true, 'dados' => $clientes]);
    }
}