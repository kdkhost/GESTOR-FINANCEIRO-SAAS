<?php

namespace App\Modules\Auditoria\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use App\Modules\Auditoria\Models\Auditoria;

class AuditoriaController extends Controller
{
    public function index()
    {
        abort_unless(auth()->check() && auth()->user()?->is_admin, 403);
        return view('admin.auditoria.index');
    }

    public function listar(Request $request): JsonResponse
    {
        abort_unless(auth()->check() && auth()->user()?->is_admin, 403);

        $query = Auditoria::with('user');

        // Filtros
        if ($request->filled('acao')) {
            $query->where('acao', $request->acao);
        }

        if ($request->filled('entidade')) {
            $query->where('entidade', $request->entidade);
        }

        if ($request->filled('usuario')) {
            $query->where(function($q) use ($request) {
                $q->where('user_name', 'like', '%' . $request->usuario . '%')
                  ->orWhere('user_email', 'like', '%' . $request->usuario . '%');
            });
        }

        if ($request->filled('data_inicio')) {
            $query->whereDate('created_at', '>=', $request->data_inicio);
        }

        if ($request->filled('data_fim')) {
            $query->whereDate('created_at', '<=', $request->data_fim);
        }

        if ($request->filled('busca')) {
            $query->where(function($q) use ($request) {
                $q->where('entidade', 'like', '%' . $request->busca . '%')
                  ->orWhere('observacao', 'like', '%' . $request->busca . '%')
                  ->orWhere('url', 'like', '%' . $request->busca . '%');
            });
        }

        // Se solicitou CSV, retorna download
        if ($request->get('format') === 'csv') {
            $auditorias = $query->orderBy('created_at', 'desc')->get();
            return $this->exportarCsv($auditorias);
        }

        $auditorias = $query->orderBy('created_at', 'desc')
                           ->paginate(25);

        // Formata datas para exibição
        $auditorias->getCollection()->transform(function ($item) {
            $item->created_at_formatado = $item->created_at ? $item->created_at->format('d/m/Y H:i') : null;
            $item->updated_at_formatado = $item->updated_at ? $item->updated_at->format('d/m/Y H:i') : null;
            return $item;
        });

        return response()->json($auditorias);
    }

    public function detalhes($id): JsonResponse
    {
        abort_unless(auth()->check() && auth()->user()?->is_admin, 403);

        $auditoria = Auditoria::with('user')->findOrFail($id);

        return response()->json([
            'sucesso' => true,
            'auditoria' => $auditoria
        ]);
    }

    public function estatisticas(): JsonResponse
    {
        abort_unless(auth()->check() && auth()->user()?->is_admin, 403);

        $hoje = now();
        $inicioMes = now()->startOfMonth();

        $stats = [
            'total_hoje' => Auditoria::whereDate('created_at', $hoje)->count(),
            'total_mes' => Auditoria::whereBetween('created_at', [$inicioMes, $hoje])->count(),
            'total_geral' => Auditoria::count(),
            'acoes_hoje' => Auditoria::whereDate('created_at', $hoje)
                                      ->selectRaw('acao, count(*) as total')
                                      ->groupBy('acao')
                                      ->pluck('total', 'acao'),
            'usuarios_ativos_hoje' => Auditoria::whereDate('created_at', $hoje)
                                                ->distinct('user_id')
                                                ->count('user_id'),
        ];

        return response()->json($stats);
    }

    public function limpar(Request $request): JsonResponse
    {
        abort_unless(auth()->check() && auth()->user()?->is_admin, 403);

        $request->validate([
            'dias' => 'required|integer|min:7|max:365'
        ]);

        $dataCorte = now()->subDays($request->dias);
        $excluidos = Auditoria::where('created_at', '<', $dataCorte)->delete();

        return response()->json([
            'sucesso' => true,
            'mensagem' => number_format($excluidos, 0, ',', '.') . ' registros antigos excluidos.'
        ]);
    }

    public function entidades(): JsonResponse
    {
        abort_unless(auth()->check() && auth()->user()?->is_admin, 403);

        $entidades = Auditoria::distinct()->pluck('entidade');

        return response()->json($entidades);
    }

    private function exportarCsv($auditorias)
    {
        $headers = [
            'Content-Type' => 'text/csv; charset=UTF-8',
            'Content-Disposition' => 'attachment; filename="auditoria_' . date('Y-m-d_H-i-s') . '.csv"',
        ];

        $output = fopen('php://temp', 'r+');

        // Cabeçalhos CSV
        fputcsv($output, ['ID', 'Data', 'Usuário', 'Ação', 'Entidade', 'Registro ID', 'Observação', 'URL', 'IP']);

        foreach ($auditorias as $auditoria) {
            fputcsv($output, [
                $auditoria->id,
                $auditoria->created_at ? $auditoria->created_at->format('d/m/Y H:i:s') : '',
                $auditoria->user_name ?? 'Sistema',
                $auditoria->acao,
                $auditoria->entidade,
                $auditoria->entidade_id ?? '',
                $auditoria->observacao ?? '',
                $auditoria->url ?? '',
                $auditoria->ip ?? '',
            ]);
        }

        rewind($output);
        $csv = stream_get_contents($output);
        fclose($output);

        // Adiciona BOM para UTF-8 (Excel)
        $csv = "\xEF\xBB\xBF" . $csv;

        return response($csv, 200, $headers);
    }
}
