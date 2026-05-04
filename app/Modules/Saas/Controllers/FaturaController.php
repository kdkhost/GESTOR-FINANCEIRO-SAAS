<?php

namespace App\Modules\Saas\Controllers;

use App\Http\Controllers\Controller;
use App\Modules\Saas\Models\Assinatura;
use App\Modules\Saas\Models\Empresa;
use App\Modules\Saas\Models\Fatura;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class FaturaController extends Controller
{
    public function index()
    {
        return view('admin.saas.faturas.index');
    }

    public function listar(Request $request): JsonResponse
    {
        $q = Fatura::query()
            ->when($request->filled('search'), function ($query) use ($request) {
                $busca = '%' . $request->search . '%';
                $query->where(function ($q2) use ($busca) {
                    $q2->where('gateway_ref', 'like', $busca)
                        ->orWhere('gateway', 'like', $busca)
                        ->orWhere('competencia', 'like', $busca);
                });
            })
            ->when($request->filled('status'), fn ($query) => $query->where('status', $request->status))
            ->orderBy('id', 'desc');

        $p = $q->paginate($request->integer('per_page', 10));

        $empresas = Empresa::whereIn('id', collect($p->items())->pluck('empresa_id')->filter()->unique()->values())
            ->get(['id', 'nome_fantasia'])
            ->keyBy('id');

        return response()->json([
            'sucesso' => true,
            'dados' => collect($p->items())->map(function (Fatura $f) use ($empresas) {
                return [
                    'id' => $f->id,
                    'empresa' => $empresas[$f->empresa_id]->nome_fantasia ?? ('#' . $f->empresa_id),
                    'status' => $f->status,
                    'competencia' => $f->competencia,
                    'valor' => (string) $f->valor,
                    'vencimento_em' => optional($f->vencimento_em)->toDateTimeString(),
                    'pago_em' => optional($f->pago_em)->toDateTimeString(),
                    'gateway' => $f->gateway,
                    'gateway_ref' => $f->gateway_ref,
                ];
            }),
            'total' => $p->total(),
            'paginas' => $p->lastPage(),
            'lookups' => [
                'empresas' => Empresa::orderBy('nome_fantasia')->limit(500)->get(['id', 'nome_fantasia']),
                'assinaturas' => Assinatura::orderBy('id', 'desc')->limit(500)->get(['id', 'empresa_id', 'plano_id']),
            ],
        ]);
    }

    public function store(Request $request): JsonResponse
    {
        $v = $request->validate([
            'empresa_id' => ['required', 'integer', 'exists:saas_empresas,id'],
            'assinatura_id' => ['nullable', 'integer', 'exists:saas_assinaturas,id'],
            'status' => ['required', 'in:aberta,paga,vencida,cancelada'],
            'competencia' => ['required', 'string', 'max:10'],
            'valor' => ['required', 'numeric', 'min:0'],
            'vencimento_em' => ['required', 'date'],
            'pago_em' => ['nullable', 'date'],
            'gateway' => ['nullable', 'string', 'max:40'],
            'gateway_ref' => ['nullable', 'string', 'max:120'],
            'link_pagamento' => ['nullable', 'string', 'max:1000'],
            'pix_copia_e_cola' => ['nullable', 'string', 'max:4000'],
            'boleto_linha_digitavel' => ['nullable', 'string', 'max:4000'],
            'observacoes' => ['nullable', 'string', 'max:2000'],
        ]);

        $f = Fatura::create($v);
        return response()->json(['sucesso' => true, 'mensagem' => 'Fatura criada com sucesso!', 'dado' => $f], 201);
    }

    public function show(int $id): JsonResponse
    {
        $f = Fatura::findOrFail($id);
        return response()->json(['sucesso' => true, 'dado' => $f]);
    }

    public function update(Request $request, int $id): JsonResponse
    {
        $f = Fatura::findOrFail($id);
        $v = $request->validate([
            'empresa_id' => ['required', 'integer', 'exists:saas_empresas,id'],
            'assinatura_id' => ['nullable', 'integer', 'exists:saas_assinaturas,id'],
            'status' => ['required', 'in:aberta,paga,vencida,cancelada'],
            'competencia' => ['required', 'string', 'max:10'],
            'valor' => ['required', 'numeric', 'min:0'],
            'vencimento_em' => ['required', 'date'],
            'pago_em' => ['nullable', 'date'],
            'gateway' => ['nullable', 'string', 'max:40'],
            'gateway_ref' => ['nullable', 'string', 'max:120'],
            'link_pagamento' => ['nullable', 'string', 'max:1000'],
            'pix_copia_e_cola' => ['nullable', 'string', 'max:4000'],
            'boleto_linha_digitavel' => ['nullable', 'string', 'max:4000'],
            'observacoes' => ['nullable', 'string', 'max:2000'],
        ]);
        $f->update($v);
        return response()->json(['sucesso' => true, 'mensagem' => 'Fatura atualizada com sucesso!', 'dado' => $f]);
    }

    public function destroy(int $id): JsonResponse
    {
        $f = Fatura::findOrFail($id);
        $f->delete();
        return response()->json(['sucesso' => true, 'mensagem' => 'Fatura removida com sucesso!']);
    }
}

