<?php

namespace App\Modules\Saas\Controllers;

use App\Http\Controllers\Controller;
use App\Modules\Saas\Models\Assinatura;
use App\Modules\Saas\Models\Empresa;
use App\Modules\Saas\Models\Plano;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class AssinaturaController extends Controller
{
    public function index()
    {
        return view('admin.saas.assinaturas.index');
    }

    public function listar(Request $request): JsonResponse
    {
        $q = Assinatura::query()
            ->when($request->filled('search'), function ($query) use ($request) {
                $busca = '%' . $request->search . '%';
                $query->where(function ($q2) use ($busca) {
                    $q2->where('gateway_ref', 'like', $busca)
                        ->orWhere('gateway', 'like', $busca);
                });
            })
            ->when($request->filled('status'), fn ($query) => $query->where('status', $request->status))
            ->orderBy('id', 'desc');

        $p = $q->paginate($request->integer('per_page', 10));

        $empresas = Empresa::whereIn('id', collect($p->items())->pluck('empresa_id')->filter()->unique()->values())->get(['id', 'nome_fantasia'])->keyBy('id');
        $planos = Plano::whereIn('id', collect($p->items())->pluck('plano_id')->filter()->unique()->values())->get(['id', 'nome'])->keyBy('id');

        return response()->json([
            'sucesso' => true,
            'dados' => collect($p->items())->map(function (Assinatura $a) use ($empresas, $planos) {
                return [
                    'id' => $a->id,
                    'empresa' => $empresas[$a->empresa_id]->nome_fantasia ?? ('#' . $a->empresa_id),
                    'plano' => $planos[$a->plano_id]->nome ?? ('#' . $a->plano_id),
                    'status' => $a->status,
                    'inicio_em' => optional($a->inicio_em)->toDateTimeString(),
                    'proxima_cobranca_em' => optional($a->proxima_cobranca_em)->toDateTimeString(),
                    'gateway' => $a->gateway,
                    'gateway_ref' => $a->gateway_ref,
                ];
            }),
            'total' => $p->total(),
            'paginas' => $p->lastPage(),
            'lookups' => [
                'empresas' => Empresa::orderBy('nome_fantasia')->limit(500)->get(['id', 'nome_fantasia']),
                'planos' => Plano::orderBy('ordem')->orderBy('nome')->get(['id', 'nome']),
            ],
        ]);
    }

    public function store(Request $request): JsonResponse
    {
        $v = $request->validate([
            'empresa_id' => ['required', 'integer', 'exists:saas_empresas,id'],
            'plano_id' => ['required', 'integer', 'exists:saas_planos,id'],
            'status' => ['required', 'in:trial,ativa,suspensa,cancelada'],
            'inicio_em' => ['nullable', 'date'],
            'fim_em' => ['nullable', 'date'],
            'proxima_cobranca_em' => ['nullable', 'date'],
            'gateway' => ['nullable', 'string', 'max:40'],
            'gateway_ref' => ['nullable', 'string', 'max:120'],
            'trial_ate' => ['nullable', 'date'],
            'cancelada_em' => ['nullable', 'date'],
            'cancelamento_motivo' => ['nullable', 'string', 'max:500'],
        ]);

        $a = Assinatura::create($v);
        return response()->json(['sucesso' => true, 'mensagem' => 'Assinatura criada com sucesso!', 'dado' => $a], 201);
    }

    public function show(int $id): JsonResponse
    {
        $a = Assinatura::findOrFail($id);
        return response()->json(['sucesso' => true, 'dado' => $a]);
    }

    public function update(Request $request, int $id): JsonResponse
    {
        $a = Assinatura::findOrFail($id);
        $v = $request->validate([
            'empresa_id' => ['required', 'integer', 'exists:saas_empresas,id'],
            'plano_id' => ['required', 'integer', 'exists:saas_planos,id'],
            'status' => ['required', 'in:trial,ativa,suspensa,cancelada'],
            'inicio_em' => ['nullable', 'date'],
            'fim_em' => ['nullable', 'date'],
            'proxima_cobranca_em' => ['nullable', 'date'],
            'gateway' => ['nullable', 'string', 'max:40'],
            'gateway_ref' => ['nullable', 'string', 'max:120'],
            'trial_ate' => ['nullable', 'date'],
            'cancelada_em' => ['nullable', 'date'],
            'cancelamento_motivo' => ['nullable', 'string', 'max:500'],
        ]);
        $a->update($v);
        return response()->json(['sucesso' => true, 'mensagem' => 'Assinatura atualizada com sucesso!', 'dado' => $a]);
    }

    public function destroy(int $id): JsonResponse
    {
        $a = Assinatura::findOrFail($id);
        $a->delete();
        return response()->json(['sucesso' => true, 'mensagem' => 'Assinatura removida com sucesso!']);
    }
}

