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
        return response()->json(['sucesso'=>true,'dados'=>$dados->items(),'total'=>$dados->total(),'paginas'=>$dados->lastPage()]);
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
}
