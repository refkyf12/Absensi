<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Lembur;
use App\Models\User;
use DB;


class LemburController extends Controller
{
    public function index(){
        $lembur = Lembur::with('User')->get();
        if (request()->segment(1) == 'api') return response()->json([
            "error"=>false,
            "list"=>$lembur,
        ]);
        // dd($lembur);
        return view('Lembur.index', ['data' => $lembur]);
    }

    public function create(){
        $users = DB::table('users')->get();
        return view('lembur.form_add_lembur', [
            'users' => $users,
            'title' => 'Tambah Lembur',
            'method' => 'POST',
            'action' => 'lembur'
        ]);
    }

    public function store(Request $request){
        $lemburData = new Lembur;
        $lemburData->users_id = $request->nama ;
        $lemburData->tanggal = $request->tanggal;
        $lemburData->jumlah_jam = $request->jumlah_jam;
        $lemburData->save();
        if (request() ->segment(1)=='api') return response()->json([
            "error" => false,
            "message" => 'Tambah Berhasil',
        ]);

        $user = User::find($request->nama);
        $user->jam_lembur = $user->jam_lembur + $request->jumlah_jam;
        $user->save();

        // $user = User::find($request->nama);
        // $user->jam_lebih = $user->jam_lebih - ($request->jumlah_jam*60); // Subtract $newValue from the old value
        // $user->save();

        return redirect('/lembur')->with('msg', 'Data berhasil di hapus');
    }

    public function delete($id){
        $data = Lembur::find($id);
        $data -> delete();
        return redirect('/lembur')->with('msg', 'Data berhasil di hapus');
    }
}
