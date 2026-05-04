<?php

namespace App\Modules\Notificacoes\Controllers;

use App\Http\Controllers\Controller;
use App\Modules\Notificacoes\Models\TemplateNotificacao;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class TemplateNotificacaoController extends Controller
{
    public function index()
    {
        return view('admin.notificacoes.templates.index');
    }

    public function listar(Request $request): JsonResponse
    {
        $q = TemplateNotificacao::query()
            ->when($request->filled('search'), function ($query) use ($request) {
                $busca = '%' . $request->search . '%';
                $query->where(function ($q2) use ($busca) {
                    $q2->where('nome', 'like', $busca)
                        ->orWhere('chave', 'like', $busca)
                        ->orWhere('canal', 'like', $busca);
                });
            })
            ->orderBy('nome');

        $p = $q->paginate($request->integer('per_page', 10));

        return response()->json([
            'sucesso' => true,
            'dados' => collect($p->items())->map(fn (TemplateNotificacao $t) => [
                'id' => $t->id,
                'nome' => $t->nome,
                'chave' => $t->chave,
                'canal' => $t->canal,
                'ativo' => (bool) $t->ativo,
                'updated_at' => optional($t->updated_at)->toDateTimeString(),
            ]),
            'total' => $p->total(),
            'paginas' => $p->lastPage(),
        ]);
    }

    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'canal' => ['required', 'in:whatsapp,email,push'],
            'chave' => ['required', 'string', 'max:120', 'alpha_dash:ascii', Rule::unique('notificacao_templates', 'chave')],
            'nome' => ['required', 'string', 'max:160'],
            'assunto' => ['nullable', 'string', 'max:200'],
            'conteudo' => ['required', 'string', 'max:20000'],
            'variaveis' => ['nullable', 'string', 'max:4000'],
            'ativo' => ['nullable', 'in:0,1'],
        ]);

        $t = TemplateNotificacao::create([
            'canal' => $validated['canal'],
            'chave' => str($validated['chave'])->lower()->toString(),
            'nome' => $validated['nome'],
            'assunto' => $validated['assunto'] ?? null,
            'conteudo' => $validated['conteudo'],
            'variaveis' => $this->parseVariaveis($validated['variaveis'] ?? ''),
            'ativo' => ($validated['ativo'] ?? '1') === '1',
        ]);

        return response()->json(['sucesso' => true, 'mensagem' => 'Template criado com sucesso!', 'dado' => $t], 201);
    }

    public function show(int $id): JsonResponse
    {
        $t = TemplateNotificacao::findOrFail($id);
        return response()->json(['sucesso' => true, 'dado' => $t]);
    }

    public function update(Request $request, int $id): JsonResponse
    {
        $t = TemplateNotificacao::findOrFail($id);

        $validated = $request->validate([
            'canal' => ['required', 'in:whatsapp,email,push'],
            'chave' => ['required', 'string', 'max:120', 'alpha_dash:ascii', Rule::unique('notificacao_templates', 'chave')->ignore($t->id)],
            'nome' => ['required', 'string', 'max:160'],
            'assunto' => ['nullable', 'string', 'max:200'],
            'conteudo' => ['required', 'string', 'max:20000'],
            'variaveis' => ['nullable', 'string', 'max:4000'],
            'ativo' => ['nullable', 'in:0,1'],
        ]);

        $t->update([
            'canal' => $validated['canal'],
            'chave' => str($validated['chave'])->lower()->toString(),
            'nome' => $validated['nome'],
            'assunto' => $validated['assunto'] ?? null,
            'conteudo' => $validated['conteudo'],
            'variaveis' => $this->parseVariaveis($validated['variaveis'] ?? ''),
            'ativo' => ($validated['ativo'] ?? '1') === '1',
        ]);

        return response()->json(['sucesso' => true, 'mensagem' => 'Template atualizado com sucesso!', 'dado' => $t]);
    }

    public function destroy(int $id): JsonResponse
    {
        $t = TemplateNotificacao::findOrFail($id);
        $t->delete();
        return response()->json(['sucesso' => true, 'mensagem' => 'Template removido com sucesso!']);
    }

    private function parseVariaveis(string $raw): array
    {
        $raw = trim($raw);
        if ($raw === '') {
            return [];
        }
        $linhas = preg_split('/\\r\\n|\\r|\\n/', $raw) ?: [];
        $limpas = [];
        foreach ($linhas as $l) {
            $l = trim($l);
            if ($l === '') {
                continue;
            }
            $limpas[] = $l;
        }
        return array_values(array_unique($limpas));
    }
}

