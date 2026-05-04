<?php

namespace App\Modules\Saas\Controllers;

use App\Http\Controllers\Controller;
use App\Modules\Saas\Models\Plano;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class PlanoController extends Controller
{
    public function index()
    {
        return view('admin.saas.planos.index');
    }

    public function listar(Request $request): JsonResponse
    {
        $q = Plano::query()
            ->when($request->filled('search'), function ($query) use ($request) {
                $busca = '%' . $request->search . '%';
                $query->where(function ($q2) use ($busca) {
                    $q2->where('nome', 'like', $busca)
                        ->orWhere('slug', 'like', $busca);
                });
            })
            ->orderBy('ordem')
            ->orderBy('nome');

        $p = $q->paginate($request->integer('per_page', 10));

        return response()->json([
            'sucesso' => true,
            'dados' => collect($p->items())->map(fn (Plano $pl) => [
                'id' => $pl->id,
                'nome' => $pl->nome,
                'slug' => $pl->slug,
                'valor_mensal' => (string) $pl->valor_mensal,
                'valor_anual' => (string) $pl->valor_anual,
                'ativo' => (bool) $pl->ativo,
                'ordem' => (int) $pl->ordem,
            ]),
            'total' => $p->total(),
            'paginas' => $p->lastPage(),
        ]);
    }

    public function store(Request $request): JsonResponse
    {
        $v = $request->validate([
            'nome' => ['required', 'string', 'max:160'],
            'slug' => ['required', 'string', 'max:120', 'alpha_dash:ascii', Rule::unique('saas_planos', 'slug')],
            'descricao' => ['nullable', 'string', 'max:2000'],
            'valor_mensal' => ['required', 'numeric', 'min:0'],
            'valor_anual' => ['nullable', 'numeric', 'min:0'],
            'limites' => ['nullable', 'string', 'max:4000'],
            'ativo' => ['nullable', 'in:0,1'],
            'ordem' => ['nullable', 'integer', 'min:0', 'max:9999'],
        ]);

        $pl = Plano::create([
            'nome' => $v['nome'],
            'slug' => str($v['slug'])->lower()->toString(),
            'descricao' => $v['descricao'] ?? null,
            'valor_mensal' => $v['valor_mensal'],
            'valor_anual' => $v['valor_anual'] ?? null,
            'limites' => $this->parseLimites($v['limites'] ?? ''),
            'ativo' => ($v['ativo'] ?? '1') === '1',
            'ordem' => (int) ($v['ordem'] ?? 0),
        ]);

        return response()->json(['sucesso' => true, 'mensagem' => 'Plano criado com sucesso!', 'dado' => $pl], 201);
    }

    public function show(int $id): JsonResponse
    {
        $pl = Plano::findOrFail($id);
        return response()->json(['sucesso' => true, 'dado' => $pl]);
    }

    public function update(Request $request, int $id): JsonResponse
    {
        $pl = Plano::findOrFail($id);
        $v = $request->validate([
            'nome' => ['required', 'string', 'max:160'],
            'slug' => ['required', 'string', 'max:120', 'alpha_dash:ascii', Rule::unique('saas_planos', 'slug')->ignore($pl->id)],
            'descricao' => ['nullable', 'string', 'max:2000'],
            'valor_mensal' => ['required', 'numeric', 'min:0'],
            'valor_anual' => ['nullable', 'numeric', 'min:0'],
            'limites' => ['nullable', 'string', 'max:4000'],
            'ativo' => ['nullable', 'in:0,1'],
            'ordem' => ['nullable', 'integer', 'min:0', 'max:9999'],
        ]);

        $pl->update([
            'nome' => $v['nome'],
            'slug' => str($v['slug'])->lower()->toString(),
            'descricao' => $v['descricao'] ?? null,
            'valor_mensal' => $v['valor_mensal'],
            'valor_anual' => $v['valor_anual'] ?? null,
            'limites' => $this->parseLimites($v['limites'] ?? ''),
            'ativo' => ($v['ativo'] ?? '1') === '1',
            'ordem' => (int) ($v['ordem'] ?? 0),
        ]);

        return response()->json(['sucesso' => true, 'mensagem' => 'Plano atualizado com sucesso!', 'dado' => $pl]);
    }

    public function destroy(int $id): JsonResponse
    {
        $pl = Plano::findOrFail($id);
        $pl->delete();
        return response()->json(['sucesso' => true, 'mensagem' => 'Plano removido com sucesso!']);
    }

    private function parseLimites(string $raw): array
    {
        $raw = trim($raw);
        if ($raw === '') {
            return [];
        }
        // Esperado: uma linha por limite no formato chave=valor
        $linhas = preg_split('/\\r\\n|\\r|\\n/', $raw) ?: [];
        $out = [];
        foreach ($linhas as $l) {
            $l = trim($l);
            if ($l === '') continue;
            if (! str_contains($l, '=')) continue;
            [$k, $v] = array_map('trim', explode('=', $l, 2));
            if ($k === '') continue;
            $out[$k] = is_numeric($v) ? (float) $v : $v;
        }
        return $out;
    }
}

