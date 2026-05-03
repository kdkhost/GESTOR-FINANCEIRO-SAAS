<?php
namespace App\Modules\Financeiro\Controllers;
use App\Http\Controllers\Controller;
use App\Modules\Financeiro\Models\Transferencia;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

class TransferenciaController extends Controller
{
    public function __construct() { $this->middleware('auth'); }

    public function index() { return view('admin.financeiro.transferencias.index'); }

    public function listar(Request $request): JsonResponse
    {
        $query = Transferencia::with(['contaOrigem','contaDestino'])->doUsuario(auth()->id());
        if ($request->filled('inicio')) $query->whereDate('data_transferencia', '>=', data_banco($request->inicio));
        if ($request->filled('fim'))    $query->whereDate('data_transferencia', '<=', data_banco($request->fim));
        $dados = $query->orderByDesc('data_transferencia')->paginate($request->get('per_page', 15));
        return response()->json(['sucesso'=>true,'dados'=>$dados->items(),'total'=>$dados->total(),'paginas'=>$dados->lastPage()]);
    }

    public function store(Request $request): JsonResponse
    {
        $request->validate([
            'conta_origem_id'    => 'required|integer|different:conta_destino_id',
            'conta_destino_id'   => 'required|integer',
            'valor'              => 'required',
            'data_transferencia' => 'required',
        ]);
        try {
            $dados = $request->all();
            $dados['user_id']              = auth()->id();
            $dados['valor']                = moeda_para_float($dados['valor']);
            $dados['data_transferencia']   = data_banco($dados['data_transferencia']);
            $transferencia = Transferencia::create($dados);
            auditoria('criou','Financeiro','transferencias',$transferencia->id,null,$dados);
            return response()->json(['sucesso'=>true,'mensagem'=>'Transferência realizada com sucesso!','dado'=>$transferencia->load(['contaOrigem','contaDestino'])],201);
        } catch (\Throwable $e) {
            return response()->json(['sucesso'=>false,'mensagem'=>'Erro ao realizar transferência.','erro'=>config('app.debug')?$e->getMessage():null],500);
        }
    }

    public function destroy(int $id): JsonResponse
    {
        $transf = Transferencia::doUsuario(auth()->id())->findOrFail($id);
        auditoria('excluiu','Financeiro','transferencias',$transf->id,$transf->toArray(),null);
        $transf->delete();
        return response()->json(['sucesso'=>true,'mensagem'=>'Transferência excluída!']);
    }
}