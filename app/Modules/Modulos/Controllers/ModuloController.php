<?php

namespace App\Modules\Modulos\Controllers;

use App\Http\Controllers\Controller;
use App\Modules\Modulos\Models\Modulo;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Artisan;

class ModuloController extends Controller
{
    public function index()
    {
        abort_unless(auth()->user()?->is_admin, 403);
        return view('admin.modulos.index');
    }

    public function listar(Request $request): JsonResponse
    {
        abort_unless(auth()->user()?->is_admin, 403);

        $query = Modulo::query()
            ->when($request->filled('search'), function ($q) use ($request) {
                $busca = '%' . $request->search . '%';
                $q->where(fn ($qq) => $qq
                    ->where('nome', 'like', $busca)
                    ->orWhere('slug', 'like', $busca)
                    ->orWhere('descricao', 'like', $busca));
            })
            ->when($request->filled('status'), fn ($q) => $q->where('ativo', $request->status === 'ativo'))
            ->orderByDesc('nativo')
            ->orderBy('nome');

        $dados = $query->paginate($request->integer('per_page', 10));

        return response()->json([
            'sucesso' => true,
            'dados' => $dados->items(),
            'total' => $dados->total(),
            'paginas' => $dados->lastPage(),
        ]);
    }

    public function store(Request $request): JsonResponse
    {
        abort_unless(auth()->user()?->is_admin, 403);

        $validated = $request->validate([
            'nome' => ['required', 'string', 'max:120'],
            'slug' => ['required', 'string', 'max:120', 'alpha_dash:ascii', 'unique:modulos,slug'],
            'versao' => ['nullable', 'string', 'max:40'],
            'provider' => ['nullable', 'string', 'max:255'],
            'diretorio' => ['nullable', 'string', 'max:160'],
            'descricao' => ['nullable', 'string'],
            'ativo' => ['nullable', 'boolean'],
        ]);

        $modulo = Modulo::create([
            ...$validated,
            'ativo' => (bool) ($validated['ativo'] ?? true),
            'nativo' => false,
        ]);

        auditoria('criou', 'Modulos', 'modulos', $modulo->id, null, $modulo->toArray(), 'Modulo adicional criado');

        return response()->json([
            'sucesso' => true,
            'mensagem' => 'Modulo adicional criado com sucesso.',
            'dado' => $modulo,
        ], 201);
    }

    public function show(int $id): JsonResponse
    {
        abort_unless(auth()->user()?->is_admin, 403);
        $modulo = Modulo::findOrFail($id);
        return response()->json(['sucesso' => true, 'dado' => $modulo]);
    }

    public function update(Request $request, int $id): JsonResponse
    {
        abort_unless(auth()->user()?->is_admin, 403);

        $modulo = Modulo::findOrFail($id);
        if ($modulo->nativo) {
            return response()->json(['sucesso' => false, 'mensagem' => 'Modulos nativos nao podem ser editados por esta tela.'], 422);
        }

        $validated = $request->validate([
            'nome' => ['required', 'string', 'max:120'],
            'slug' => ['required', 'string', 'max:120', 'alpha_dash:ascii', 'unique:modulos,slug,' . $modulo->id],
            'versao' => ['nullable', 'string', 'max:40'],
            'provider' => ['nullable', 'string', 'max:255'],
            'diretorio' => ['nullable', 'string', 'max:160'],
            'descricao' => ['nullable', 'string'],
            'ativo' => ['nullable', 'boolean'],
        ]);

        $antes = $modulo->toArray();
        $modulo->update([
            ...$validated,
            'ativo' => (bool) ($validated['ativo'] ?? $modulo->ativo),
        ]);

        auditoria('editou', 'Modulos', 'modulos', $modulo->id, $antes, $modulo->toArray(), 'Modulo adicional atualizado');

        return response()->json([
            'sucesso' => true,
            'mensagem' => 'Modulo atualizado com sucesso.',
            'dado' => $modulo->fresh(),
        ]);
    }

    public function destroy(int $id): JsonResponse
    {
        abort_unless(auth()->user()?->is_admin, 403);

        $modulo = Modulo::findOrFail($id);
        if ($modulo->nativo) {
            return response()->json(['sucesso' => false, 'mensagem' => 'Modulos nativos nao podem ser removidos.'], 422);
        }

        $antes = $modulo->toArray();
        $modulo->delete();

        auditoria('excluiu', 'Modulos', 'modulos', $id, $antes, null, 'Modulo adicional removido');

        return response()->json(['sucesso' => true, 'mensagem' => 'Modulo removido com sucesso.']);
    }

    public function alternarStatus(int $id): JsonResponse
    {
        abort_unless(auth()->user()?->is_admin, 403);

        $modulo = Modulo::findOrFail($id);
        $antes = $modulo->ativo;
        $modulo->update(['ativo' => ! $modulo->ativo]);

        auditoria(
            'editou',
            'Modulos',
            'modulos',
            $modulo->id,
            ['ativo' => $antes],
            ['ativo' => $modulo->ativo],
            $modulo->ativo ? 'Modulo ativado' : 'Modulo desativado'
        );

        Artisan::call('optimize:clear');

        return response()->json([
            'sucesso' => true,
            'mensagem' => $modulo->ativo ? 'Modulo ativado com sucesso.' : 'Modulo desativado com sucesso.',
            'dado' => $modulo->fresh(),
        ]);
    }
}

