<?php
namespace App\Modules\Usuarios\Controllers;
use App\Http\Controllers\Controller;
use App\Modules\Usuarios\Models\User;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Hash;

class UsuarioController extends Controller
{
    public function index() { return view('admin.usuarios.index'); }

    public function listar(Request $request): JsonResponse
    {
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
        $request->validate([
            'name'     => 'required|string|max:100',
            'email'    => 'required|email|unique:users,email',
            'password' => 'required|min:8',
            'tipo'     => 'nullable|in:usuario,admin,superadmin',
            'status'   => 'nullable|in:ativo,inativo',
        ]);
        try {
            $user = User::create([
                'name'     => $request->name,
                'email'    => $request->email,
                'password' => Hash::make($request->password),
                'tipo'     => $request->tipo ?? 'usuario',
                'status'   => $request->status ?? 'ativo',
            ]);
            auditoria('criou','Usuarios','users',$user->id,null,['name'=>$user->name,'email'=>$user->email]);
            return response()->json(['sucesso'=>true,'mensagem'=>'Usuario criado com sucesso!','dado'=>$user],201);
        } catch (\Throwable $e) {
            return response()->json(['sucesso'=>false,'mensagem'=>'Erro ao criar usuario.','erro'=>config('app.debug')?$e->getMessage():null],500);
        }
    }

    public function show(int $id): JsonResponse
    {
        $user = User::findOrFail($id);
        return response()->json(['sucesso'=>true,'dado'=>$user]);
    }

    public function update(Request $request, int $id): JsonResponse
    {
        $user = User::findOrFail($id);
        $request->validate([
            'name'     => 'required|string|max:100',
            'email'    => 'required|email|unique:users,email,'.$id,
            'password' => 'nullable|min:8',
        ]);
        $dados = $request->only(['name','email','tipo','status']);
        if ($request->filled('password')) $dados['password'] = Hash::make($request->password);
        $anterior = $user->toArray();
        $user->update($dados);
        auditoria('editou','Usuarios','users',$user->id,$anterior,$dados);
        return response()->json(['sucesso'=>true,'mensagem'=>'Usuario atualizado!','dado'=>$user->fresh()]);
    }

    public function destroy(int $id): JsonResponse
    {
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