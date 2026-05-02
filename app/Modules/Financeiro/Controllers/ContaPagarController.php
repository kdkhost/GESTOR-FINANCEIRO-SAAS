<?php

namespace App\Modules\Financeiro\Controllers;

use App\Http\Controllers\Controller;
use App\Modules\Financeiro\Models\ContaPagar;
use App\Modules\Financeiro\Requests\ContaPagarRequest;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

class ContaPagarController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Exibe a tela de listagem (view).
     */
    public function index()
    {
        return view('admin.financeiro.contas-pagar.index');
    }

    /**
     * Retorna dados paginados para DataTable via AJAX.
     */
    public function listar(Request $request): JsonResponse
    {
        $query = ContaPagar::with(['categoria', 'fornecedor', 'contaBancaria'])
            ->doUsuario(auth()->id());

        // Filtros
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }
        if ($request->filled('categoria_id')) {
            $query->where('categoria_id', $request->categoria_id);
        }
        if ($request->filled('inicio')) {
            $query->whereDate('data_vencimento', '>=', data_banco($request->inicio));
        }
        if ($request->filled('fim')) {
            $query->whereDate('data_vencimento', '<=', data_banco($request->fim));
        }
        if ($request->filled('search')) {
            $query->where('descricao', 'like', '%' . $request->search . '%');
        }

        $dados = $query->orderBy('data_vencimento')->paginate($request->get('per_page', 15));

        return response()->json([
            'sucesso' => true,
            'dados'   => $dados->items(),
            'total'   => $dados->total(),
            'paginas' => $dados->lastPage(),
        ]);
    }

    /**
     * Cria uma nova conta a pagar.
     */
    public function store(ContaPagarRequest $request): JsonResponse
    {
        try {
            $dados = $request->validated();
            $dados['user_id'] = auth()->id();

            // Converte valores
            if (isset($dados['valor'])) {
                $dados['valor'] = moeda_para_float($dados['valor']);
            }
            if (isset($dados['data_vencimento'])) {
                $dados['data_vencimento'] = data_banco($dados['data_vencimento']);
            }

            $conta = ContaPagar::create($dados);

            auditoria('criou', 'Financeiro', 'contas_pagar', $conta->id, null, $dados);

            return response()->json([
                'sucesso'  => true,
                'mensagem' => 'Conta a pagar criada com sucesso!',
                'dado'     => $conta->load(['categoria', 'fornecedor']),
            ], 201);
        } catch (\Throwable $e) {
            return response()->json([
                'sucesso'  => false,
                'mensagem' => 'Erro ao criar conta a pagar.',
                'erro'     => config('app.debug') ? $e->getMessage() : null,
            ], 500);
        }
    }

    /**
     * Retorna dados de uma conta para edição.
     */
    public function show(int $id): JsonResponse
    {
        $conta = ContaPagar::with(['categoria', 'subcategoria', 'fornecedor', 'contaBancaria', 'centroCusto', 'anexos'])
            ->doUsuario(auth()->id())
            ->findOrFail($id);

        return response()->json(['sucesso' => true, 'dado' => $conta]);
    }

    /**
     * Atualiza uma conta existente.
     */
    public function update(ContaPagarRequest $request, int $id): JsonResponse
    {
        try {
            $conta = ContaPagar::doUsuario(auth()->id())->findOrFail($id);
            $anterior = $conta->toArray();

            $dados = $request->validated();
            if (isset($dados['valor'])) {
                $dados['valor'] = moeda_para_float($dados['valor']);
            }
            if (isset($dados['data_vencimento'])) {
                $dados['data_vencimento'] = data_banco($dados['data_vencimento']);
            }

            $conta->update($dados);
            auditoria('editou', 'Financeiro', 'contas_pagar', $conta->id, $anterior, $dados);

            return response()->json([
                'sucesso'  => true,
                'mensagem' => 'Conta atualizada com sucesso!',
                'dado'     => $conta->fresh(['categoria', 'fornecedor']),
            ]);
        } catch (\Throwable $e) {
            return response()->json(['sucesso' => false, 'mensagem' => 'Erro ao atualizar.'], 500);
        }
    }

    /**
     * Remove (soft delete) uma conta.
     */
    public function destroy(int $id): JsonResponse
    {
        $conta = ContaPagar::doUsuario(auth()->id())->findOrFail($id);

        if ($conta->status === 'pago') {
            return response()->json([
                'sucesso'  => false,
                'mensagem' => 'Não é possível excluir uma conta já paga.',
            ], 422);
        }

        auditoria('excluiu', 'Financeiro', 'contas_pagar', $conta->id, $conta->toArray(), null);
        $conta->delete();

        return response()->json(['sucesso' => true, 'mensagem' => 'Conta excluída com sucesso!']);
    }

    /**
     * Registra o pagamento de uma conta.
     */
    public function pagar(Request $request, int $id): JsonResponse
    {
        $request->validate([
            'data_pagamento'    => 'required|date_format:d/m/Y',
            'valor_pago'        => 'required|string',
            'conta_bancaria_id' => 'nullable|integer',
            'forma_pagamento_id'=> 'nullable|integer',
        ]);

        try {
            $conta = ContaPagar::doUsuario(auth()->id())
                ->whereIn('status', ['pendente', 'vencido', 'parcialmente_pago'])
                ->findOrFail($id);

            $valorPago = moeda_para_float($request->valor_pago);
            $status    = $valorPago >= $conta->valor ? 'pago' : 'parcialmente_pago';

            $conta->update([
                'valor_pago'         => $valorPago,
                'data_pagamento'     => data_banco($request->data_pagamento),
                'status'             => $status,
                'conta_bancaria_id'  => $request->conta_bancaria_id ?? $conta->conta_bancaria_id,
                'forma_pagamento_id' => $request->forma_pagamento_id ?? $conta->forma_pagamento_id,
            ]);

            auditoria('pagou', 'Financeiro', 'contas_pagar', $conta->id, null, ['valor_pago' => $valorPago, 'status' => $status]);

            return response()->json([
                'sucesso'  => true,
                'mensagem' => 'Pagamento registrado com sucesso!',
                'dado'     => $conta->fresh(),
            ]);
        } catch (\Throwable $e) {
            return response()->json(['sucesso' => false, 'mensagem' => 'Erro ao registrar pagamento.'], 500);
        }
    }

    /**
     * Cancela uma conta a pagar.
     */
    public function cancelar(int $id): JsonResponse
    {
        $conta = ContaPagar::doUsuario(auth()->id())
            ->whereNotIn('status', ['pago', 'cancelado'])
            ->findOrFail($id);

        $conta->update(['status' => 'cancelado']);
        auditoria('cancelou', 'Financeiro', 'contas_pagar', $conta->id, null, null);

        return response()->json(['sucesso' => true, 'mensagem' => 'Conta cancelada com sucesso!']);
    }
}
