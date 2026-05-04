<?php

namespace App\Modules\Usuarios\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rules\Password;

class PerfilController extends Controller
{


    public function index()
    {
        $user = auth()->user();
        return view('admin.perfil.index', compact('user'));
    }

    public function update(Request $request): JsonResponse
    {
        $user = auth()->user();

        $request->validate([
            'name'                  => 'required|string|max:100',
            'email'                 => 'required|email|unique:users,email,' . $user->id,
            'current_password'      => 'nullable|string',
            'password'              => ['nullable', 'confirmed', Password::min(8)],
        ]);

        $dados = [
            'name'  => $request->name,
            'email' => $request->email,
        ];

        if ($request->filled('current_password')) {
            if (!Hash::check($request->current_password, $user->password)) {
                return response()->json(['sucesso' => false, 'mensagem' => 'Senha atual incorreta.'], 422);
            }
            $dados['password'] = Hash::make($request->password);
        }

        $user->update($dados);

        return response()->json(['sucesso' => true, 'mensagem' => 'Perfil atualizado com sucesso!']);
    }

    public function uploadAvatar(Request $request): JsonResponse
    {
        $request->validate([
            'avatar' => 'required|image|max:2048',
        ]);

        $user = auth()->user();

        if ($user->avatar && Storage::disk('public')->exists($user->avatar)) {
            Storage::disk('public')->delete($user->avatar);
        }

        $path = $request->file('avatar')->store('avatars', 'public');
        $user->update(['avatar' => $path]);

        return response()->json([
            'sucesso'  => true,
            'mensagem' => 'Avatar atualizado com sucesso!',
            'url'      => Storage::url($path),
        ]);
    }
}
