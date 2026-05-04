<?php
namespace App\Modules\Relatorios\Controllers;
use App\Http\Controllers\Controller;
class RelatorioController extends Controller
{
    public function fluxoCaixa()      { return view('admin.relatorios.fluxo-caixa'); }
    public function dre()             { return view('admin.relatorios.dre'); }
    public function evolucao()        { return view('admin.relatorios.evolucao'); }
    public function inadimplencia()   { return view('admin.relatorios.inadimplencia'); }
    public function saudeFinanceira() { return view('admin.relatorios.saude-financeira'); }
}