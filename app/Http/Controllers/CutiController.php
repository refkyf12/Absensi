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
}
