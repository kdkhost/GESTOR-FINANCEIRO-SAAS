<?php

namespace App\Modules\Saas\Controllers;

use App\Http\Controllers\Controller;
use App\Modules\Saas\Models\Empresa;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class EmpresaController extends Controller
{
    public function index()
    {
        return view('admin.saas.empresas.index');
    }

    public function listar(Request $request): JsonResponse
    {
        $q = Empresa::query()
            ->when($request->filled('search'), function ($query) use ($request) {
                $busca = '%' . $request->search . '%';
                $query->where(function ($q2) use ($busca) {
                    $q2->where('nome_fantasia', 'like', $busca)
                        ->orWhere('razao_social', 'like', $busca)
                        ->orWhere('cnpj', 'like', $busca)
                        ->orWhere('email', 'like', $busca);
                });
            })
            ->when($request->filled('status'), fn ($query) => $query->where('status', $request->status))
            ->orderBy('id', 'desc');

        $p = $q->paginate($request->integer('per_page', 10));

        return response()->json([
            'sucesso' => true,
            'dados' => collect($p->items())->map(fn (Empresa $e) => [
                'id' => $e->id,
                'nome_fantasia' => $e->nome_fantasia,
                'razao_social' => $e->razao_social,
                'cnpj' => $e->cnpj,
                'email' => $e->email,
                'telefone' => $e->telefone,
                'cidade' => $e->cidade,
                'estado' => $e->estado,
                'status' => $e->status,
            ]),
            'total' => $p->total(),
            'paginas' => $p->lastPage(),
        ]);
    }

    public function store(Request $request): JsonResponse
    {
        $v = $request->validate([
            'nome_fantasia' => ['required', 'string', 'max:160'],
            'razao_social' => ['nullable', 'string', 'max:200'],
            'cnpj' => ['nullable', 'string', 'max:30'],
            'email' => ['nullable', 'email', 'max:160'],
            'telefone' => ['nullable', 'string', 'max:30'],
            'cep' => ['nullable', 'string', 'max:12'],
            'logradouro' => ['nullable', 'string', 'max:200'],
            'numero' => ['nullable', 'string', 'max:30'],
            'complemento' => ['nullable', 'string', 'max:120'],
            'bairro' => ['nullable', 'string', 'max:120'],
            'cidade' => ['nullable', 'string', 'max:120'],
            'estado' => ['nullable', 'string', 'max:20'],
            'status' => ['required', 'in:ativo,inativo,bloqueado'],
            'timezone' => ['nullable', 'string', 'max:80'],
            'locale' => ['nullable', 'string', 'max:20'],
            'observacoes' => ['nullable', 'string', 'max:2000'],
        ]);

        $e = Empresa::create($v);
        return response()->json(['sucesso' => true, 'mensagem' => 'Empresa criada com sucesso!', 'dado' => $e], 201);
    }

    public function show(int $id): JsonResponse
    {
        $e = Empresa::findOrFail($id);
        return response()->json(['sucesso' => true, 'dado' => $e]);
    }

    public function update(Request $request, int $id): JsonResponse
    {
        $e = Empresa::findOrFail($id);

        $v = $request->validate([
            'nome_fantasia' => ['required', 'string', 'max:160'],
            'razao_social' => ['nullable', 'string', 'max:200'],
            'cnpj' => ['nullable', 'string', 'max:30'],
            'email' => ['nullable', 'email', 'max:160'],
            'telefone' => ['nullable', 'string', 'max:30'],
            'cep' => ['nullable', 'string', 'max:12'],
            'logradouro' => ['nullable', 'string', 'max:200'],
            'numero' => ['nullable', 'string', 'max:30'],
            'complemento' => ['nullable', 'string', 'max:120'],
            'bairro' => ['nullable', 'string', 'max:120'],
            'cidade' => ['nullable', 'string', 'max:120'],
            'estado' => ['nullable', 'string', 'max:20'],
            'status' => ['required', 'in:ativo,inativo,bloqueado'],
            'timezone' => ['nullable', 'string', 'max:80'],
            'locale' => ['nullable', 'string', 'max:20'],
            'observacoes' => ['nullable', 'string', 'max:2000'],
        ]);

        $e->update($v);
        return response()->json(['sucesso' => true, 'mensagem' => 'Empresa atualizada com sucesso!', 'dado' => $e]);
    }

    public function destroy(int $id): JsonResponse
    {
        $e = Empresa::findOrFail($id);
        $e->delete();
        return response()->json(['sucesso' => true, 'mensagem' => 'Empresa removida com sucesso!']);
    }
}

