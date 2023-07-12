<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Lembur;
use App\Models\User;
use DB;
use App\Models\logKegiatan;
use Illuminate\Support\Facades\Auth;


class LemburController extends Controller
{
    public function index(){
        $this->validate();
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
        $lemburData->jam_awal = $request->jam_awal;
        $lemburData->jam_akhir = $request->jam_akhir;
        $temp = (strtotime($request->jam_akhir) - strtotime($request->jam_awal))/60;
        $lemburData->jumlah_jam = $temp;
        

        $lemburData->save();

        if (Auth::check())
                {
                    date_default_timezone_set("Asia/Jakarta");
                    $id = Auth::id();
                    $date = date("Y-m-d h:i:sa");
                    $data = $request->nama;
                    $text = 'Melakukan Tambah Lembur Karyawan ' . $data;
                    $logKegiatan = new logKegiatan;
                    $logKegiatan->users_id = $id;
                    $logKegiatan->kegiatan = $text;
                    $logKegiatan->created_at = $date;
                    $logKegiatan->save();
                }
        if (request() ->segment(1)=='api') return response()->json([
            "error" => false,
            "message" => 'Tambah Berhasil',
        ]);

        // harus diubah
        
        // sampe sini

        

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

    public function filter(Request $request){
        $start_date = $request->start_date;
        $end_date = $request->end_date;

        $lembur = Lembur::whereDate('tanggal','>=',$start_date)->whereDate('tanggal','<=',$end_date)->get();

        //dd($log_absen);

        return view('lembur.index', ['data'=>$lembur]);
    }

    public function show_approval($id){
        $data = Lembur::find($id);

        return view('lembur.approval_lembur', compact('data'));
    }
    public function approval($id, Request $request)
    {
        //dd($id);
        $data = Lembur::find($id);
        $data->status = $request->status;
        $temp = $data->jumlah_jam;
        
        

        //1 diterima, 2 ditolak, null belum diproses
        if($request->status == 1){
            $user = User::find($data->users_id);
            $user->jam_lembur = $user->jam_lembur + $temp;
            $user->update();
        }

        $data->update();
        return redirect('/lembur')->with('msg', 'data lembur berhasil diperbarui');
        
    }

    // public function getData(){
    //     $users = DB::table('users')->get();
    //     //dd($users);
    //     // return $users;
    //     return view('lembur.form_add_lembur', ['data' => $users]);
    // }
}
