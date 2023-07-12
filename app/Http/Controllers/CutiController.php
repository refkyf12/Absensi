<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Cuti;
use App\Models\User;
use DB;
use Illuminate\Support\Facades\Auth;
use App\Models\logKegiatan;

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

    public function create(){
        $users = DB::table('users')->get();
        return view('cuti.add_form_cuti', [
            'users' => $users,
            'title' => 'Tambah Cuti',
            'method' => 'POST',
            'action' => 'cuti'
        ]);
    }

    public function store(Request $request){
        $total = 0;
        $cutiData = new Cuti;
        $cutiData->users_id = $request->nama ;
        $cutiData->tanggal_awal = $request->tanggal_awal;
        $cutiData->tanggal_akhir = $request->tanggal_akhir;
        $currentDate = $request->tanggal_awal;
        $endDate = $request->tanggal_akhir;
        while($currentDate <= $endDate){
            $dayOfWeek = date('l', strtotime($currentDate));
            $currentDate = date('Y-m-d', strtotime($currentDate. '+1 day'));
            if($dayOfWeek != "Sunday" && $dayOfWeek != "Saturday"){
                $total = $total + 1;
            }
        }
        $cutiData->jumlah_hari = $total;
        $cutiData->save();

        if (Auth::check())
                {
                    date_default_timezone_set("Asia/Jakarta");
                    $id = Auth::id();
                    $date = date("Y-m-d h:i:sa");
                    $data = $request->nama;
                    $text = 'Melakukan Tambah Cuti Karyawan ' . $data;
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

        // $user = User::find($request->nama);
        // $user->jam_lebih = $user->jam_lebih - ($request->jumlah_jam*60); // Subtract $newValue from the old value
        // $user->save();

        return redirect('/cuti')->with('msg', 'Data berhasil di tambah');
    }


    public function show_approval($id){
        $data = Cuti::find($id);

        return view('cuti.approval_cuti', compact('data'));
    }
    public function approval($id, Request $request)
    {
        //dd($id);
        $data = Cuti::find($id);
        $data->status = $request->status;

        //1 diterima, 2 ditolak, null belum diproses
        if($request->status == 1){
            $user = User::find($data->users_id);
            $temp = $user->sisa_cuti - $data->jumlah_hari;
            $user->sisa_cuti = $temp;
            $user->update();
        }

        $data->update();
        return redirect('/cuti')->with('msg', 'data cuti berhasil diperbarui');
        
    }
}
