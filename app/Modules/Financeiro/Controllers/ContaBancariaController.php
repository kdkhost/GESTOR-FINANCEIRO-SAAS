<?php

namespace App\Modules\Financeiro\Controllers;

use App\Http\Controllers\Controller;
use App\Modules\Financeiro\Models\ContaBancaria;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

class ContaBancariaController extends Controller
{
    public function __construct() { $this->middleware('auth'); }

    public function index()
    {
        return view('admin.cadastros.contas-bancarias.index');
    }

    public function listar(Request $request): JsonResponse
    {
        $dados = ContaBancaria::doUsuario(auth()->id())
            ->when($request->filled('ativo'), fn ($q) => $q->where('ativo', $request->ativo))
            ->orderBy('nome')
            ->get();
        return response()->json(['sucesso' => true, 'dados' => $dados, 'total' => $dados->count()]);
    }

    public function store(Request $request): JsonResponse
    {
        $request->validate([
            'nome'          => 'required|string|max:100',
            'tipo'          => 'required|in:corrente,poupanca,salario,investimento,carteira,outro',
            'saldo_inicial' => 'required',
        ]);

        try {
            $saldo = moeda_para_float($request->saldo_inicial);
            $conta = ContaBancaria::create([
                'user_id'          => auth()->id(),
                'nome'             => $request->nome,
                'tipo'             => $request->tipo,
                'banco_id'         => $request->banco_id,
                'agencia'          => $request->agencia,
                'numero_conta'     => $request->numero_conta,
                'digito'           => $request->digito,
                'saldo_inicial'    => $saldo,
                'saldo_atual'      => $saldo,
                'incluir_no_total' => $request->boolean('incluir_no_total', true),
                'ativo'            => true,
                'cor'              => $request->cor ?? '#0d6efd',
                'icone'            => $request->icone ?? 'bi-bank',
                'observacoes'      => $request->observacoes,
            ]);

            auditoria('criou', 'Financeiro', 'contas_bancarias', $conta->id, null, $request->all());
            return response()->json(['sucesso' => true, 'mensagem' => 'Conta bancária criada com sucesso!', 'dado' => $conta], 201);
        } catch (\Throwable $e) {
            return response()->json(['sucesso' => false, 'mensagem' => 'Erro ao criar conta bancária.', 'erro' => config('app.debug') ? $e->getMessage() : null], 500);
        }
    }

    public function show(int $id): JsonResponse
    {
        $conta = ContaBancaria::doUsuario(auth()->id())->findOrFail($id);
        return response()->json(['sucesso' => true, 'dado' => $conta]);
    }

    public function update(Request $request, int $id): JsonResponse
    {
        $request->validate(['nome' => 'required|string|max:100', 'tipo' => 'required']);
        $conta    = ContaBancaria::doUsuario(auth()->id())->findOrFail($id);
        $anterior = $conta->toArray();
        $dados    = $request->only(['nome', 'tipo', 'banco_id', 'agencia', 'numero_conta', 'digito', 'incluir_no_total', 'ativo', 'cor', 'icone', 'observacoes']);
        $conta->update($dados);
        auditoria('editou', 'Financeiro', 'contas_bancarias', $conta->id, $anterior, $dados);
        return response()->json(['sucesso' => true, 'mensagem' => 'Conta bancária atualizada!', 'dado' => $conta->fresh()]);
    }

    public function destroy(int $id): JsonResponse
    {
        $conta = ContaBancaria::doUsuario(auth()->id())->findOrFail($id);
        auditoria('excluiu', 'Financeiro', 'contas_bancarias', $conta->id, $conta->toArray(), null);
        $conta->delete();
        return response()->json(['sucesso' => true, 'mensagem' => 'Conta bancária excluída!']);
    }

    public function ajustarSaldo(Request $request, int $id): JsonResponse
    {
        $request->validate(['saldo_novo' => 'required', 'motivo' => 'nullable|string|max:255']);
        $conta      = ContaBancaria::doUsuario(auth()->id())->findOrFail($id);
        $saldoAntes = $conta->saldo_atual;
        $saldoNovo  = moeda_para_float($request->saldo_novo);
        $conta->update(['saldo_atual' => $saldoNovo]);
        auditoria('ajustou_saldo', 'Financeiro', 'contas_bancarias', $conta->id, ['saldo_antes' => $saldoAntes], ['saldo_novo' => $saldoNovo, 'motivo' => $request->motivo]);
        return response()->json(['sucesso' => true, 'mensagem' => 'Saldo ajustado com sucesso!', 'dado' => $conta->fresh()]);
    }
}