<?php
namespace App\Modules\Permissoes\Controllers;

use App\Http\Controllers\Controller;
use App\Modules\Permissoes\Support\PermissoesPadrao;
use App\Modules\Usuarios\Models\User;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Schema;
use Illuminate\Validation\Rule;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use Spatie\Permission\PermissionRegistrar;

class PermissaoController extends Controller
{
    public function index()
    {
        $tabelasOk = $this->tabelasPermissaoDisponiveis();

        if ($tabelasOk) {
            $this->sincronizarPadroes();
        }

        return view('admin.permissoes.index', [
            'gruposPermissoes' => PermissoesPadrao::grupos(),
            'tabelasOk' => $tabelasOk,
        ]);
    }

    public function listar(): JsonResponse
    {
        if (! $this->tabelasPermissaoDisponiveis()) {
            return $this->erroTabelasAusentes();
        }

        $this->sincronizarPadroes();

        $roles = Role::with('permissions:id,name')
            ->orderBy('name')
            ->get()
            ->map(fn (Role $role) => [
                'id' => $role->id,
                'name' => $role->name,
                'guard_name' => $role->guard_name,
                'permissions' => $role->permissions->pluck('name')->values(),
                'total_permissions' => $role->permissions->count(),
                'bloqueado' => $role->name === 'superadmin',
            ]);

        return response()->json([
            'sucesso' => true,
            'roles' => $roles,
            'grupos' => PermissoesPadrao::grupos(),
            'permissions' => Permission::orderBy('name')->pluck('name')->values(),
        ]);
    }

    public function store(Request $request): JsonResponse
    {
        if (! $this->tabelasPermissaoDisponiveis()) {
            return $this->erroTabelasAusentes();
        }

        $validated = $request->validate([
            'name' => ['required', 'string', 'max:80', 'alpha_dash:ascii', Rule::unique('roles', 'name')->where('guard_name', 'web')],
            'permissions' => ['array'],
            'permissions.*' => ['string', 'exists:permissions,name'],
        ]);

        $role = Role::create([
            'name' => str($validated['name'])->lower()->toString(),
            'guard_name' => 'web',
        ]);

        $role->syncPermissions($validated['permissions'] ?? []);
        app(PermissionRegistrar::class)->forgetCachedPermissions();

        auditoria('criou', 'Permissoes', 'roles', $role->id, null, $role->toArray(), 'Papel criado');

        return response()->json([
            'sucesso' => true,
            'mensagem' => 'Papel criado com sucesso!',
            'role' => $role->load('permissions'),
        ], 201);
    }

    public function show(Role $role): JsonResponse
    {
        if (! $this->tabelasPermissaoDisponiveis()) {
            return $this->erroTabelasAusentes();
        }

        return response()->json([
            'sucesso' => true,
            'role' => [
                'id' => $role->id,
                'name' => $role->name,
                'permissions' => $role->permissions()->pluck('name')->values(),
                'bloqueado' => $role->name === 'superadmin',
            ],
        ]);
    }

    public function update(Request $request, Role $role): JsonResponse
    {
        if (! $this->tabelasPermissaoDisponiveis()) {
            return $this->erroTabelasAusentes();
        }

        $validated = $request->validate([
            'name' => [
                'required',
                'string',
                'max:80',
                'alpha_dash:ascii',
                Rule::unique('roles', 'name')->where('guard_name', 'web')->ignore($role->id),
            ],
            'permissions' => ['array'],
            'permissions.*' => ['string', 'exists:permissions,name'],
        ]);

        $antes = $role->load('permissions')->toArray();

        if ($role->name !== 'superadmin') {
            $role->update(['name' => str($validated['name'])->lower()->toString()]);
            $role->syncPermissions($validated['permissions'] ?? []);
        } else {
            $role->syncPermissions(PermissoesPadrao::nomes());
        }

        app(PermissionRegistrar::class)->forgetCachedPermissions();

        auditoria('editou', 'Permissoes', 'roles', $role->id, $antes, $role->fresh('permissions')->toArray(), 'Papel atualizado');

        return response()->json([
            'sucesso' => true,
            'mensagem' => 'Papel atualizado com sucesso!',
            'role' => $role->fresh('permissions'),
        ]);
    }

    public function destroy(Role $role): JsonResponse
    {
        if (! $this->tabelasPermissaoDisponiveis()) {
            return $this->erroTabelasAusentes();
        }

        if ($role->name === 'superadmin') {
            return response()->json(['sucesso' => false, 'mensagem' => 'O papel superadmin nao pode ser excluido.'], 422);
        }

        $antes = $role->toArray();
        $role->delete();
        app(PermissionRegistrar::class)->forgetCachedPermissions();

        auditoria('excluiu', 'Permissoes', 'roles', $role->id, $antes, null, 'Papel excluido');

        return response()->json(['sucesso' => true, 'mensagem' => 'Papel excluido com sucesso!']);
    }

    public function usuarios(Request $request): JsonResponse
    {
        if (! $this->tabelasPermissaoDisponiveis()) {
            return $this->erroTabelasAusentes();
        }

        $usuarios = User::with('roles:id,name')
            ->when($request->filled('search'), function ($query) use ($request) {
                $busca = '%' . $request->search . '%';
                $query->where(fn ($q) => $q->where('name', 'like', $busca)->orWhere('email', 'like', $busca));
            })
            ->orderBy('name')
            ->paginate($request->integer('per_page', 10));

        return response()->json([
            'sucesso' => true,
            'dados' => collect($usuarios->items())->map(fn (User $user) => [
                'id' => $user->id,
                'name' => $user->name,
                'email' => $user->email,
                'tipo' => $user->tipo,
                'status' => $user->status,
                'roles' => $user->roles->pluck('name')->values(),
            ]),
            'total' => $usuarios->total(),
            'paginas' => $usuarios->lastPage(),
            'roles' => Role::orderBy('name')->pluck('name')->values(),
        ]);
    }

    public function sincronizarUsuario(Request $request, User $user): JsonResponse
    {
        if (! $this->tabelasPermissaoDisponiveis()) {
            return $this->erroTabelasAusentes();
        }

        $validated = $request->validate([
            'roles' => ['array'],
            'roles.*' => ['string', 'exists:roles,name'],
        ]);

        $roles = $validated['roles'] ?? [];
        if ($user->tipo === 'superadmin' && ! in_array('superadmin', $roles, true)) {
            $roles[] = 'superadmin';
        }

        $antes = $user->roles()->pluck('name')->values()->all();
        $user->syncRoles($roles);

        auditoria('editou', 'Permissoes', 'model_has_roles', $user->id, $antes, $roles, 'Papeis do usuario atualizados');

        return response()->json(['sucesso' => true, 'mensagem' => 'Papeis do usuario atualizados!']);
    }

    public static function sincronizarPadroesEstatico(): void
    {
        (new self())->sincronizarPadroes();
    }

    private function sincronizarPadroes(): void
    {
        if (! $this->tabelasPermissaoDisponiveis()) {
            return;
        }

        foreach (PermissoesPadrao::nomes() as $nome) {
            Permission::firstOrCreate(['name' => $nome, 'guard_name' => 'web']);
        }

        foreach (PermissoesPadrao::papeis() as $papel => $permissoes) {
            $role = Role::firstOrCreate(['name' => $papel, 'guard_name' => 'web']);
            $role->syncPermissions($permissoes);
        }

        app(PermissionRegistrar::class)->forgetCachedPermissions();
    }

    private function tabelasPermissaoDisponiveis(): bool
    {
        return Schema::hasTable('roles')
            && Schema::hasTable('permissions')
            && Schema::hasTable('role_has_permissions')
            && Schema::hasTable('model_has_roles');
    }

    private function erroTabelasAusentes(): JsonResponse
    {
        return response()->json([
            'sucesso' => false,
            'mensagem' => 'As tabelas de permissoes ainda nao foram criadas. Execute as migrations pelo instalador.',
        ], 503);
    }
}
