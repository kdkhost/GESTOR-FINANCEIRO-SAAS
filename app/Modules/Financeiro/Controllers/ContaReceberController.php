<?php

namespace App\Modules\Financeiro\Controllers;

use App\Http\Controllers\Controller;
use App\Modules\Financeiro\Models\ContaReceber;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

class ContaReceberController extends Controller
{


    public function index()
    {
        return view('admin.financeiro.contas-receber.index');
    }

    public function listar(Request $request): JsonResponse
    {
        $query = ContaReceber::with(['categoria', 'cliente', 'contaBancaria'])
            ->doUsuario(auth()->id());

        if ($request->filled('status'))       $query->where('status', $request->status);
        if ($request->filled('categoria_id')) $query->where('categoria_id', $request->categoria_id);
        if ($request->filled('cliente_id'))   $query->where('cliente_id', $request->cliente_id);
        if ($request->filled('inicio'))       $query->whereDate('data_vencimento', '>=', data_banco($request->inicio));
        if ($request->filled('fim'))          $query->whereDate('data_vencimento', '<=', data_banco($request->fim));
        if ($request->filled('search'))       $query->where('descricao', 'like', '%'.$request->search.'%');

        $dados = $query->orderBy('data_vencimento')->paginate($request->get('per_page', 15));

        return response()->json([
            'sucesso' => true,
            'dados'   => $dados->items(),
            'total'   => $dados->total(),
            'paginas' => $dados->lastPage(),
        ]);
    }

    public function store(Request $request): JsonResponse
    {
        $request->validate([
            'descricao'       => 'required|string|max:255',
            'valor'           => 'required',
            'data_vencimento' => 'required',
        ]);

        try {
            $dados = $request->all();
            $dados['user_id']         = auth()->id();
            $dados['valor']           = moeda_para_float($dados['valor']);
            $dados['data_vencimento'] = data_banco($dados['data_vencimento']);

            $conta = ContaReceber::create($dados);
            auditoria('criou', 'Financeiro', 'contas_receber', $conta->id, null, $dados);

            return response()->json(['sucesso' => true, 'mensagem' => 'Conta a receber criada com sucesso!', 'dado' => $conta->load(['categoria', 'cliente'])], 201);
        } catch (\Throwable $e) {
            return response()->json(['sucesso' => false, 'mensagem' => 'Erro ao criar conta a receber.', 'erro' => config('app.debug') ? $e->getMessage() : null], 500);
        }
    }

    public function show(int $id): JsonResponse
    {
        $conta = ContaReceber::with(['categoria', 'subcategoria', 'cliente', 'contaBancaria', 'anexos'])
            ->doUsuario(auth()->id())->findOrFail($id);
        return response()->json(['sucesso' => true, 'dado' => $conta]);
    }

    public function update(Request $request, int $id): JsonResponse
    {
        $request->validate([
            'descricao'       => 'required|string|max:255',
            'valor'           => 'required',
            'data_vencimento' => 'required',
        ]);

        try {
            $conta    = ContaReceber::doUsuario(auth()->id())->findOrFail($id);
            $anterior = $conta->toArray();
            $dados    = $request->all();

            if (isset($dados['valor']))           $dados['valor']           = moeda_para_float($dados['valor']);
            if (isset($dados['data_vencimento'])) $dados['data_vencimento'] = data_banco($dados['data_vencimento']);

            $conta->update($dados);
            auditoria('editou', 'Financeiro', 'contas_receber', $conta->id, $anterior, $dados);

            return response()->json(['sucesso' => true, 'mensagem' => 'Conta atualizada com sucesso!', 'dado' => $conta->fresh(['categoria', 'cliente'])]);
        } catch (\Throwable $e) {
            return response()->json(['sucesso' => false, 'mensagem' => 'Erro ao atualizar.'], 500);
        }
    }

    public function destroy(int $id): JsonResponse
    {
        $conta = ContaReceber::doUsuario(auth()->id())->findOrFail($id);

        if ($conta->status === 'recebido') {
            return response()->json(['sucesso' => false, 'mensagem' => 'Não é possível excluir uma conta já recebida.'], 422);
        }

        auditoria('excluiu', 'Financeiro', 'contas_receber', $conta->id, $conta->toArray(), null);
        $conta->delete();

        return response()->json(['sucesso' => true, 'mensagem' => 'Conta excluída com sucesso!']);
    }

    public function receber(Request $request, int $id): JsonResponse
    {
        $request->validate([
            'data_recebimento'  => 'required|date_format:d/m/Y',
            'valor_recebido'    => 'required|string',
            'conta_bancaria_id' => 'nullable|integer',
        ]);

        try {
            $conta       = ContaReceber::doUsuario(auth()->id())->whereIn('status', ['pendente', 'vencido', 'parcialmente_recebido'])->findOrFail($id);
            $valorRecebido = moeda_para_float($request->valor_recebido);
            $status        = $valorRecebido >= $conta->valor ? 'recebido' : 'parcialmente_recebido';

            $conta->update([
                'valor_recebido'    => $valorRecebido,
                'data_recebimento'  => data_banco($request->data_recebimento),
                'status'            => $status,
                'conta_bancaria_id' => $request->conta_bancaria_id ?? $conta->conta_bancaria_id,
            ]);

            auditoria('recebeu', 'Financeiro', 'contas_receber', $conta->id, null, ['valor_recebido' => $valorRecebido, 'status' => $status]);

            return response()->json(['sucesso' => true, 'mensagem' => 'Recebimento registrado com sucesso!', 'dado' => $conta->fresh()]);
        } catch (\Throwable $e) {
            return response()->json(['sucesso' => false, 'mensagem' => 'Erro ao registrar recebimento.'], 500);
        }
    }
}