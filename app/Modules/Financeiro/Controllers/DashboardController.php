<?php

namespace App\Modules\Financeiro\Controllers;

use App\Http\Controllers\Controller;
use App\Modules\Financeiro\Services\DashboardService;
use App\Modules\Financeiro\Services\SaudeFinanceiraService;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Carbon\Carbon;

class DashboardController extends Controller
{


    /**
     * Exibe a view principal do dashboard.
     */
    public function index()
    {
        return view('admin.dashboard.index');
    }

    /**
     * Retorna os KPIs via AJAX conforme período selecionado.
     *
     * GET /admin/dashboard/kpis?periodo=mes
     */
    public function kpis(Request $request): JsonResponse
    {
        $periodo = $request->get('periodo', 'mes');
        [$inicio, $fim] = $this->resolverPeriodo($periodo, $request);

        $service = new DashboardService(auth()->id());

        return response()->json([
            'sucesso'  => true,
            'periodo'  => ['inicio' => data_br($inicio), 'fim' => data_br($fim)],
            'kpis'     => $service->kpis($inicio, $fim),
        ]);
    }

    /**
     * Retorna dados de saúde financeira via AJAX.
     *
     * GET /admin/dashboard/saude
     */
    public function saude(Request $request): JsonResponse
    {
        $mes = (int) $request->get('mes', now()->month);
        $ano = (int) $request->get('ano', now()->year);

        $service = new SaudeFinanceiraService(auth()->id());

        return response()->json([
            'sucesso' => true,
            'saude'   => $service->calcular($mes, $ano),
        ]);
    }

    /**
     * Converte data do formato dd/mm/yyyy ou yyyy-mm-dd para yyyy-mm-dd.
     */
    private function parseDateInput(string $data): string
    {
        if (str_contains($data, '/')) {
            try {
                return Carbon::createFromFormat('d/m/Y', $data)->toDateString();
            } catch (\Throwable) {
                return now()->toDateString();
            }
        }
        return $data;
    }

    /**
     * Converte string de período para datas de início e fim.
     */
    private function resolverPeriodo(string $periodo, Request $request): array
    {
        return match ($periodo) {
            'hoje'        => [today()->toDateString(), today()->toDateString()],
            'semana'      => [now()->startOfWeek()->toDateString(), now()->endOfWeek()->toDateString()],
            'mes'         => [now()->startOfMonth()->toDateString(), now()->endOfMonth()->toDateString()],
            'trimestre'   => [now()->firstOfQuarter()->toDateString(), now()->lastOfQuarter()->toDateString()],
            'semestre'    => [now()->startOfYear()->toDateString(), now()->month > 6
                ? now()->endOfYear()->toDateString()
                : Carbon::create(now()->year, 6, 30)->toDateString()],
            'ano'         => [now()->startOfYear()->toDateString(), now()->endOfYear()->toDateString()],
            'personalizado' => [
                $this->parseDateInput($request->get('inicio', now()->startOfMonth()->toDateString())),
                $this->parseDateInput($request->get('fim',    now()->endOfMonth()->toDateString())),
            ],
            default => [now()->startOfMonth()->toDateString(), now()->endOfMonth()->toDateString()],
        };
    }
}
