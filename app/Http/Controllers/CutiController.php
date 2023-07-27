<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Cuti;
use App\Models\User;
use App\Models\Rules;
use App\Models\liburNasional;
use DB;
use Illuminate\Support\Facades\Auth;
use App\Models\logKegiatan;
use Exception;

class CutiController extends Controller
{
    public function getLamaKerja(){
        $rules = Rules::where('key', "lama_kerja")->first();
        $lamaKerja = $rules["value"];
        return $lamaKerja;
    }
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
        $libur_nasional = liburNasional::pluck('tanggal')->toArray();
        $users = DB::table('users')->get();
        return view('cuti.add_form_cuti', [
            'users' => $users,
            'title' => 'Tambah Cuti',
            'method' => 'POST',
            'action' => 'cuti'
        ], compact('libur_nasional'));
    }

    public function store(Request $request){
        try{
            $user = User::find($request->nama);
        //dd($user);

        $liburNasional = LiburNasional::pluck('tanggal')->toArray();
        $total = 0;
        $cutiData = new Cuti;
        $cutiData->users_id = $request->nama ;
        $cutiData->tanggal_awal = $request->tanggal_awal;
        $cutiData->tanggal_akhir = $request->tanggal_akhir;
        $currentDate = $request->tanggal_awal;
        $endDate = $request->tanggal_akhir;

        //dd($liburNasional == null);
        if($liburNasional != null){
            foreach($liburNasional as $items){
                    while($currentDate <= $endDate){
                        $dayOfWeek = date('l', strtotime($currentDate));
                        $currentDate = date('Y-m-d', strtotime($currentDate. '+1 day'));
                        if($dayOfWeek != "Sunday" && $dayOfWeek != "Saturday" && $currentDate != $items && $endDate != $items){
                            $total = $total + 1;
                            
                        }
                        
                    }
                    $cutiData->jumlah_hari = $total;
                    
            }
        }else{
            while($currentDate <= $endDate){
                $dayOfWeek = date('l', strtotime($currentDate));
                $currentDate = date('Y-m-d', strtotime($currentDate. '+1 day'));
                if($dayOfWeek != "Sunday" && $dayOfWeek != "Saturday" ){
                    $total = $total + 1;
                    
                }
                
            }
            $cutiData->jumlah_hari = $total;
        }
        
        $cutiData->deskripsi = $request->deskripsi;
        $cutiData->save();
        if (request()->segment(1)=='api') return response()->json([
            "error" => false,
            "message" => 'Tambah cuti berhasil',
        ]);
        

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

        return redirect('/cuti')->with('success', 'Data berhasil di tambah');
        }catch(Exception $e){
            $errorMessage = $e->getMessage();
            return redirect('/cuti')->with('error', 'Data gagal di tambah. Error : ' . $errorMessage);
        }        
        
    }


    public function show_approval($id){
        $data = Cuti::find($id);

        return view('cuti.approval_cuti', compact('data'));
    }
    public function approval($id, Request $request)
    {
        try{
            $data = Cuti::find($id);
        $data->status = (int)$request->status;

        //1 diterima, 2 ditolak, null belum diproses
        if($request->status == 1){
            $user = User::find($data->users_id);
            $temp = $user->sisa_cuti - $data->jumlah_hari;
            $user->sisa_cuti = $temp;

            $data->update();
            $user->update();
            
            
            if (Auth::check())
                {
                    date_default_timezone_set("Asia/Jakarta");
                    $id = Auth::id();
                    $date = date("Y-m-d h:i:sa");
                    $data = $user->nama;
                    $text = 'Melakukan Approval Cuti Pada Karyawan ' . $data;
                    $logKegiatan = new logKegiatan;
                    $logKegiatan->users_id = $id;
                    $logKegiatan->kegiatan = $text;
                    $logKegiatan->created_at = $date;
                    $logKegiatan->save();
                }
        }else if($request->status == 2){
            $user = User::find($data->users_id);
            $data->update();

            if (Auth::check())
                {
                    date_default_timezone_set("Asia/Jakarta");
                    $id = Auth::id();
                    $date = date("Y-m-d h:i:sa");
                    $data = $user->nama;
                    $text = 'Melakukan Approval Cuti Pada Karyawan ' . $data;
                    $logKegiatan = new logKegiatan;
                    $logKegiatan->users_id = $id;
                    $logKegiatan->kegiatan = $text;
                    $logKegiatan->created_at = $date;
                    $logKegiatan->save();
                }
        }

        
        
        return redirect('/cuti')->with('success', 'Data cuti berhasil diperbarui');
        }catch(Exception $e){
            $errorMessage = $e->getMessage();
            return redirect('/cuti')->with('error', 'Data cuti gagal diperbarui');
        }
        //dd($id);
        
        
    }

    public function storeMobile(Request $request){
        try{
            $user = User::find($request->nama);


        $liburNasional = LiburNasional::pluck('tanggal')->toArray();
        $total = 0;
        $cutiData = new Cuti;
        $cutiData->users_id = $request->nama ;
        $cutiData->tanggal_awal = $request->tanggal_awal;
        $cutiData->tanggal_akhir = $request->tanggal_akhir;
        $currentDate = $request->tanggal_awal;
        $endDate = $request->tanggal_akhir;


        if($liburNasional != null){

            foreach($liburNasional as $items){
                    while($currentDate <= $endDate){
                        $dayOfWeek = date('l', strtotime($currentDate));
                        $currentDate = date('Y-m-d', strtotime($currentDate. '+1 day'));
                        if($dayOfWeek != "Sunday" && $dayOfWeek != "Saturday" && $currentDate != $items && $endDate != $items){
                            $total = $total + 1;
                            
                        }
                        
                    }
                    $cutiData->jumlah_hari = $total;
                    
            }

        }else{

            while($currentDate <= $endDate){
                $dayOfWeek = date('l', strtotime($currentDate));
                $currentDate = date('Y-m-d', strtotime($currentDate. '+1 day'));
                if($dayOfWeek != "Sunday" && $dayOfWeek != "Saturday" ){
                    $total = $total + 1;
                    
                }
                
            }
            $cutiData->jumlah_hari = $total;
        }


        
        $cutiData->deskripsi = $request->deskripsi;
        $cutiData->save();




        if (request()->segment(1)=='api') return response()->json([
            "error" => false,
            "message" => 'Tambah cuti berhasil',
        ]);
        

        }catch(Exception $e){
            $errorMessage = $e->getMessage();
        }        
        
    }
}
