<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Exception;

class DashboardController extends Controller
{
    function index()
    {
        $this->validate();
        $result=DB::select(DB::raw("select count(*) as total, keterlambatan from log_absen group by keterlambatan"));
        dd($result);
        //return view('dashboard');
    }
}
