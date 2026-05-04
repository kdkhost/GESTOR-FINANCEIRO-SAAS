<?php
namespace App\Modules\Configuracoes\Controllers;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
class ConfiguracaoController extends Controller
{
    public function index() { return view('admin.configuracoes.index'); }
}