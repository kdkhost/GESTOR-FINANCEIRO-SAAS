<?php
namespace App\Modules\Cron\Controllers;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
class CronController extends Controller
{
    public function index() { return view('admin.cron.index'); }
}