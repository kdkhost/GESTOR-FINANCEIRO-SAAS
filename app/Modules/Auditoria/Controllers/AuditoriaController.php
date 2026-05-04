<?php
namespace App\Modules\Auditoria\Controllers;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
class AuditoriaController extends Controller
{
    public function index() { return view('admin.auditoria.index'); }
}