<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Cuti;
use App\Models\User;

class CutiController extends Controller
{
    public function index(){
        $this->validate();
        $cuti = Cuti::with('User')->get();
        if (request()->segment(1) == 'api') return response()->json([
            "error"=>false,
            "list"=>$cuti,
        ]);
        return view('Cuti.index', ['data' => $cuti]);
    }

    public function filter(Request $request){
        $start_date = $request->start_date;
        $end_date = $request->end_date;

        $cuti = Cuti::whereDate('tanggal','>=',$start_date)->whereDate('tanggal','<=',$end_date)->get();

        //dd($log_absen);

        return view('cuti.index', ['data'=>$cuti]);
    }
}
