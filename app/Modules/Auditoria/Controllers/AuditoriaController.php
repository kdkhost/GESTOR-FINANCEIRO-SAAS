<?php
namespace App\Modules\Auditoria\Controllers;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
class AuditoriaController extends Controller
{
    public function index()
    {
        abort_unless(auth()->check() && auth()->user()?->is_admin, 403);
        return view('admin.auditoria.index');
    }
}
