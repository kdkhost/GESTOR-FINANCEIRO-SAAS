<?php
namespace App\Modules\Usuarios\Controllers;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
class UsuarioController extends Controller
{
    public function index() { return view('admin.usuarios.index'); }
}