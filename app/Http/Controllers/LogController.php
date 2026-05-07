<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\DB;

class LogController extends Controller
{
    public function index()
    {
        $logs = DB::table('logs_importacao')
            ->join('users', 'logs_importacao.user_id', '=', 'users.id')
            ->select('logs_importacao.*', 'users.name as user_name')
            ->orderBy('created_at', 'desc')
            ->get();

        return view('logs.index', compact('logs'));
    }
}
