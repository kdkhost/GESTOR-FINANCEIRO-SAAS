<?php
namespace App\Modules\Financeiro\Controllers;
use App\Http\Controllers\Controller;
use App\Modules\Financeiro\Models\Anexo;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Storage;

class AnexoController extends Controller
{


    public function store(Request $request): JsonResponse
    {
        $request->validate([
            'arquivo'       => 'required|file|max:10240|mimes:pdf,jpg,jpeg,png,gif,doc,docx,xls,xlsx,txt,zip',
            'entidade_tipo' => 'required|string',
            'entidade_id'   => 'required|integer',
        ]);

        try {
            $arquivo  = $request->file('arquivo');
            $caminho  = $arquivo->store('anexos/'.auth()->id(), 'public');
            $anexo    = Anexo::create([
                'user_id'       => auth()->id(),
                'entidade_tipo' => $request->entidade_tipo,
                'entidade_id'   => $request->entidade_id,
                'nome_original' => $arquivo->getClientOriginalName(),
                'nome_arquivo'  => basename($caminho),
                'caminho'       => $caminho,
                'tamanho'       => $arquivo->getSize(),
                'mime_type'     => $arquivo->getMimeType(),
            ]);
            return response()->json(['sucesso'=>true,'mensagem'=>'Arquivo enviado com sucesso!','dado'=>$anexo],201);
        } catch (\Throwable $e) {
            return response()->json(['sucesso'=>false,'mensagem'=>'Erro ao enviar arquivo.','erro'=>config('app.debug')?$e->getMessage():null],500);
        }
    }

    public function destroy(int $id): JsonResponse
    {
        $anexo = Anexo::doUsuario(auth()->id())->findOrFail($id);
        Storage::disk('public')->delete($anexo->caminho);
        $anexo->delete();
        return response()->json(['sucesso'=>true,'mensagem'=>'Arquivo excluído!']);
    }
}