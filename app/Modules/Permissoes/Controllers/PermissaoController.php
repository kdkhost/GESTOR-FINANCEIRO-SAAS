<?php
namespace App\Modules\Permissoes\Controllers;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
class PermissaoController extends Controller
{
    public function index() { return view('admin.permissoes.index'); }
}