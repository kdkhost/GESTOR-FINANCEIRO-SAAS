<?php
namespace App\Modules\Usuarios\Controllers;
use App\Http\Controllers\Controller;
use App\Modules\Usuarios\Models\User;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Hash;

class UsuarioController extends Controller
{
    private function exigirAdmin(): void
    {
        abort_unless(auth()->check() && auth()->user()?->is_admin, 403);
    }

    public function index() { $this->exigirAdmin(); return view('admin.usuarios.index'); }

    public function listar(Request $request): JsonResponse
    {
        $this->exigirAdmin();
        $query = User::query();
        if ($request->filled('search')) {
            $query->where(function($q) use ($request) {
                $q->where('name','like','%'.$request->search.'%')
                  ->orWhere('email','like','%'.$request->search.'%');
            });
        }
        if ($request->filled('status')) $query->where('status', $request->status);
        $dados = $query->orderBy('name')->paginate($request->get('per_page', 10));

        // Adiciona avatar_url e formata datas aos itens
        $items = $dados->items();
        foreach ($items as $item) {
            $item->avatar_url = $item->avatar_url;
            $item->ultimo_acesso_em_formatado = $item->ultimo_acesso_em ? $item->ultimo_acesso_em->format('d/m/Y H:i') : null;
        }

        return response()->json(['sucesso'=>true,'dados'=>$items,'total'=>$dados->total(),'paginas'=>$dados->lastPage()]);
    }

    public function store(Request $request): JsonResponse
    {
        $this->exigirAdmin();
        $request->validate([
            'name'     => 'required|string|max:100',
            'email'    => 'required|email|unique:users,email',
            'password' => 'required|min:8',
            'tipo'     => 'nullable|in:usuario,admin,superadmin',
            'status'   => 'nullable|in:ativo,inativo,bloqueado',
            'avatar'   => 'nullable|image|max:2048',
            'cpf'      => 'nullable|string|max:20',
            'telefone' => 'nullable|string|max:20',
            'cep'      => 'nullable|string|max:10',
            'logradouro' => 'nullable|string|max:200',
            'numero'   => 'nullable|string|max:20',
            'complemento' => 'nullable|string|max:100',
            'bairro'   => 'nullable|string|max:100',
            'cidade'   => 'nullable|string|max:100',
            'estado'   => 'nullable|string|max:2',
            'dois_fatores' => 'nullable|boolean',
        ]);
        try {
            $dados = [
                'name'     => $request->name,
                'email'    => $request->email,
                'password' => Hash::make($request->password),
                'tipo'     => $request->tipo ?? 'usuario',
                'status'   => $request->status ?? 'ativo',
                'cpf'      => $request->cpf,
                'telefone' => $request->telefone,
                'cep'      => $request->cep,
                'logradouro' => $request->logradouro,
                'numero'   => $request->numero,
                'complemento' => $request->complemento,
                'bairro'   => $request->bairro,
                'cidade'   => $request->cidade,
                'estado'   => $request->estado,
                'dois_fatores' => $request->boolean('dois_fatores', false),
            ];

            // Upload avatar
            if ($request->hasFile('avatar')) {
                $path = $request->file('avatar')->store('avatars', 'public');
                $dados['avatar'] = $path;
            }

            $user = User::create($dados);

            // Adicionar avatar_url ao retorno
            $user->append('avatar_url');

            auditoria('criou','Usuarios','users',$user->id,null,['name'=>$user->name,'email'=>$user->email]);
            return response()->json(['sucesso'=>true,'mensagem'=>'Usuario criado com sucesso!','dado'=>$user],201);
        } catch (\Throwable $e) {
            return response()->json(['sucesso'=>false,'mensagem'=>'Erro ao criar usuario.','erro'=>config('app.debug')?$e->getMessage():null],500);
        }
    }

    public function show(int $id): JsonResponse
    {
        $this->exigirAdmin();
        $user = User::findOrFail($id);
        return response()->json(['sucesso'=>true,'dado'=>$user]);
    }

    public function update(Request $request, int $id): JsonResponse
    {
        $this->exigirAdmin();
        $user = User::findOrFail($id);

        // Verificar permissao para editar superadmin
        if ($user->tipo === 'superadmin' && !auth()->user()->is_superadmin) {
            return response()->json(['sucesso' => false, 'mensagem' => 'Apenas superadmins podem editar outros superadmins.'], 403);
        }

        $request->validate([
            'name'     => 'required|string|max:100',
            'email'    => 'required|email|unique:users,email,'.$id,
            'password' => 'nullable|min:8',
            'tipo'     => 'nullable|in:usuario,admin,superadmin',
            'status'   => 'nullable|in:ativo,inativo,bloqueado',
            'avatar'   => 'nullable|image|max:2048',
            'cpf'      => 'nullable|string|max:20',
            'telefone' => 'nullable|string|max:20',
            'cep'      => 'nullable|string|max:10',
            'logradouro' => 'nullable|string|max:200',
            'numero'   => 'nullable|string|max:20',
            'complemento' => 'nullable|string|max:100',
            'bairro'   => 'nullable|string|max:100',
            'cidade'   => 'nullable|string|max:100',
            'estado'   => 'nullable|string|max:2',
            'dois_fatores' => 'nullable|boolean',
        ]);

        $dados = [
            'name'     => $request->name,
            'email'    => $request->email,
            'tipo'     => $request->tipo ?? $user->tipo,
            'status'   => $request->status ?? $user->status,
            'cpf'      => $request->cpf,
            'telefone' => $request->telefone,
            'cep'      => $request->cep,
            'logradouro' => $request->logradouro,
            'numero'   => $request->numero,
            'complemento' => $request->complemento,
            'bairro'   => $request->bairro,
            'cidade'   => $request->cidade,
            'estado'   => $request->estado,
            'dois_fatores' => $request->boolean('dois_fatores', $user->dois_fatores),
        ];

        if ($request->filled('password')) {
            $dados['password'] = Hash::make($request->password);
        }

        // Upload avatar
        if ($request->hasFile('avatar')) {
            // Deletar avatar antigo se existir
            if ($user->avatar && \Storage::disk('public')->exists($user->avatar)) {
                \Storage::disk('public')->delete($user->avatar);
            }
            $path = $request->file('avatar')->store('avatars', 'public');
            $dados['avatar'] = $path;
        }

        $anterior = $user->toArray();
        $user->update($dados);

        // Adicionar avatar_url ao retorno
        $user->append('avatar_url');

        auditoria('editou','Usuarios','users',$user->id,$anterior,$dados);
        return response()->json(['sucesso'=>true,'mensagem'=>'Usuario atualizado!','dado'=>$user->fresh()]);
    }

    public function destroy(int $id): JsonResponse
    {
        $this->exigirAdmin();
        $user = User::findOrFail($id);
        if ($user->tipo === 'superadmin') {
            return response()->json(['sucesso'=>false,'mensagem'=>'Nao e possivel excluir o superadmin.'],422);
        }
        if ($user->id === auth()->id()) {
            return response()->json(['sucesso'=>false,'mensagem'=>'Voce nao pode excluir sua propria conta.'],422);
        }
        auditoria('excluiu','Usuarios','users',$user->id,$user->toArray(),null);
        $user->delete();
        return response()->json(['sucesso'=>true,'mensagem'=>'Usuario excluido!']);
    }

    /**
     * Acesso supervisionado - Admin/Superadmin entra na conta de outro usuario
     */
    public function impersonate(int $id): JsonResponse
    {
        $this->exigirAdmin();

        $admin = auth()->user();
        $targetUser = User::findOrFail($id);

        // Superadmin pode acessar qualquer conta
        // Admin pode acessar apenas usuarios comuns (nao admin/superadmin)
        if (!$admin->is_superadmin && $targetUser->tipo !== 'usuario') {
            return response()->json([
                'sucesso' => false,
                'mensagem' => 'Apenas superadmins podem acessar contas de admin.'
            ], 403);
        }

        // Nao pode acessar a propria conta
        if ($targetUser->id === $admin->id) {
            return response()->json([
                'sucesso' => false,
                'mensagem' => 'Voce ja esta logado na sua conta.'
            ], 422);
        }

        // Verifica se usuario esta ativo
        if ($targetUser->status !== 'ativo') {
            return response()->json([
                'sucesso' => false,
                'mensagem' => 'Usuario inativo. Nao e possivel acessar esta conta.'
            ], 422);
        }

        // Salva ID do admin original na sessao
        session(['impersonate_admin_id' => $admin->id]);
        session(['impersonate_target_id' => $targetUser->id]);
        session(['impersonate_started_at' => now()->toDateTimeString()]);

        // Faz login como o usuario alvo
        auth()->login($targetUser);

        auditoria('impersonate', 'Usuarios', 'users', $targetUser->id,
            ['admin_id' => $admin->id, 'admin_name' => $admin->name],
            ['user_id' => $targetUser->id, 'user_name' => $targetUser->name],
            'Acesso supervisionado iniciado'
        );

        return response()->json([
            'sucesso' => true,
            'mensagem' => "Acessando conta de {$targetUser->name} ({$targetUser->email})",
            'redirect' => route('admin.dashboard')
        ]);
    }

    /**
     * Sai do modo supervisionado e volta a conta do admin
     */
    public function stopImpersonating(): JsonResponse
    {
        $adminId = session('impersonate_admin_id');

        if (!$adminId) {
            return response()->json([
                'sucesso' => false,
                'mensagem' => 'Nenhuma sessao de acesso supervisionado ativa.'
            ], 422);
        }

        $targetUser = auth()->user();
        $admin = User::findOrFail($adminId);

        // Limpa dados da sessao
        session()->forget(['impersonate_admin_id', 'impersonate_target_id', 'impersonate_started_at']);

        // Volta para conta do admin
        auth()->login($admin);

        auditoria('stop_impersonate', 'Usuarios', 'users', $targetUser->id,
            ['user_id' => $targetUser->id, 'user_name' => $targetUser->name],
            ['admin_id' => $admin->id, 'admin_name' => $admin->name],
            'Acesso supervisionado encerrado'
        );

        return response()->json([
            'sucesso' => true,
            'mensagem' => 'Voltou a sua conta de administrador.',
            'redirect' => route('admin.dashboard')
        ]);
    }

    /**
     * Verifica se esta em modo supervisionado
     */
    public function impersonateStatus(): JsonResponse
    {
        $isImpersonating = session()->has('impersonate_admin_id');

        return response()->json([
            'sucesso' => true,
            'impersonating' => $isImpersonating,
            'admin' => $isImpersonating ? User::find(session('impersonate_admin_id'))?->only(['id', 'name', 'email']) : null,
            'started_at' => session('impersonate_started_at')
        ]);
    }
}
