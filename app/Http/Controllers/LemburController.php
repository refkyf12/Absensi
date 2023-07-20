<?php

namespace App\Http\Controllers;

use App\Models\lebihKerja;
use Illuminate\Http\Request;
use App\Models\Lembur;
use App\Models\logAbsen;
use App\Models\User;
use App\Traits\jamKeInt;
use DB;
use App\Models\logKegiatan;
use Illuminate\Support\Facades\Auth;


class LemburController extends Controller
{
    use jamKeInt;
    public function index(){
        $this->validate();
        //$lembur = Lembur::with('User')->get();

        $lembur = DB::table('lembur')
            ->join('log_absen', function ($join) {
                $join->on('lembur.tanggal', '=', 'log_absen.tanggal')
                    ->on('lembur.users_id', '=', 'log_absen.users_id');
            })
            ->join('users', 'lembur.users_id', '=', 'users.id')
            ->select('lembur.*', 'log_absen.jam_keluar','users.nama as user_nama')
            ->get();

        // foreach ($lembur as $item) {
        //     $user = DB::table('users')
        //         ->where('id', $item->users_id)
        //         ->first();
            
        //     $item->user = $user;
        // }

        
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
        $temp = ($this->timeToInteger($request->jam_akhir) - $this->timeToInteger($request->jam_awal))/60;
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
        $data = Lembur::find($id);
        $data->status = $request->status;
        $totalJamLembur = $data->jumlah_jam;        
        

        //1 diterima, 2 ditolak, null belum diproses
        if($request->status == 1){
            $user = User::find($data->users_id);
            $logAbsen = LogAbsen::where('tanggal', $data->tanggal)->where('users_id', $data->users_id)->first();

            if ($logAbsen != null){
                $lebihFromLebihKerja = lebihKerja::where('absen_id', $logAbsen->id)->first();
                $kurangForLebihInUser = $this->timeToInteger($lebihFromLebihKerja->total_jam)/60;
                //dd($user->jam_lebih);
                //LEBIH JAM PADA USER HARUS DIKURANGI DULU DENGAN LEBIH JAM PADA HARI TERSEBUT - JANGAN LUPA DIBUAT
                $jamMasuk = $this->timeToInteger($logAbsen->jam_masuk)/60;
                $jamKeluar = $this->timeToInteger($logAbsen->jam_keluar)/60;
                $jamAwalLembur = $this->timeToInteger($data->jam_awal)/60;
                $jamAkhirLembur = $this->timeToInteger($data->jam_akhir)/60;

                $lebih = ($jamKeluar-$jamMasuk)-(28800/60);

                //UBAH KE YANG BARU-------------------------------------------
                $masukLebih1 = ($jamAwalLembur - ($jamMasuk+(28800/60)));//dari selesai jam kerja hingga jam awal lembur
                //dd($masukLebih1);
                $lebih = $lebih - $masukLebih1;
                // dd($lebih);

                $user->jam_lembur = $user->jam_lembur + $totalJamLembur;

                $lebih = $lebih - $totalJamLembur;


                if($lebih <= 0){
                    $lebih = 0;
                }

                $user->jam_lebih = $user->jam_lebih + ($masukLebih1 + $lebih - $kurangForLebihInUser);

                //dd($user->jam_lebih);

                //------------------------------------------------------------
            }else{
                $user->jam_lembur = $user->jam_lembur + $totalJamLembur;
            }
            $user->update();
        }

        $data->update();
        return redirect('/lembur')->with('msg', 'data lembur berhasil diperbarui');
        
    }

    public function show_edit($id){
        $data = Lembur::find($id);

        return view('lembur.form_edit_lembur', compact('data'));
    }

    public function edit($id, Request $request){
        $data = Lembur::find($id);
        $logAbsen = LogAbsen::where('tanggal', $data->tanggal)->where('users_id', $data->users_id)->first();
        $user = User::find($data->users_id);

        if($data->jam_awal != $request->jam_awal || $data->jam_akhir != $request->jam_akhir){
            if ($logAbsen != null && $data->status == 1){
                $totalJamLembur = $data->jumlah_jam;  

                //LEBIH JAM PADA USER HARUS DIKURANGI DULU DENGAN LEBIH JAM PADA HARI TERSEBUT - JANGAN LUPA DIBUAT
                $jamMasuk = $this->timeToInteger($logAbsen->jam_masuk)/60;
                $jamKeluar = $this->timeToInteger($logAbsen->jam_keluar)/60;
                $jamAwalLembur = $this->timeToInteger($data->jam_awal)/60;
                $jamAkhirLembur = $this->timeToInteger($data->jam_akhir)/60;

                $lebih = ($jamKeluar-$jamMasuk)-(28800/60);

                //UBAH KE YANG BARU-------------------------------------------
                $masukLebih1 = ($jamAwalLembur - ($jamMasuk+(28800/60)));//dari selesai jam kerja hingga jam awal lembur
                // dd($masukLebih1);
                $lebih = $lebih - $masukLebih1;

                $user->jam_lembur = $user->jam_lembur - $totalJamLembur;
                //dd($user->jam_lembur);

                $lebih = $lebih - $totalJamLembur;
                //dd($lebih);

                if($lebih <= 0){
                    $lebih = 0;
                }

                $user->jam_lebih = $user->jam_lebih - ($masukLebih1 + $lebih);
                //dd($user->jam_lembur);
                //------------------------------------------------------------

                $data->jam_awal = $request->jam_awal;
                $data->jam_akhir = $request->jam_akhir;
                //dd($data->jam_akhir);

                $temp = ($this->timeToInteger($request->jam_akhir) - $this->timeToInteger($request->jam_awal))/60;
                // dd($temp);
                $data->jumlah_jam  = $temp;
                //dd($data->jumlah_jam);

                $totalJamLembur = $data->jumlah_jam; 

                $jamAwalLembur = $this->timeToInteger($data->jam_awal)/60;
                $jamAkhirLembur = $this->timeToInteger($data->jam_akhir)/60;

                $lebih = ($jamKeluar-$jamMasuk)-(28800/60);

                //dd($jamAwalLembur, $jamAkhirLembur, $lebih);

                //UBAH KE YANG BARU-------------------------------------------
                $masukLebih1 = ($jamAwalLembur - ($jamMasuk+(28800/60)));//dari selesai jam kerja hingga jam awal lembur
                //dd($masukLebih1);
                $lebih = $lebih - $masukLebih1;

                //dd($lebih);

                $user->jam_lembur = $user->jam_lembur + $totalJamLembur;
                //dd($user->jam_lembur);

                $lebih = $lebih - $totalJamLembur;
                //dd($lebih);

                if($lebih <= 0){
                    $lebih = 0;
                }

                $user->jam_lebih = $user->jam_lebih + ($masukLebih1 + $lebih);
                //dd($user->jam_lebih);

                $data->update();
                $user->update();

            }else{
                $data->jam_awal = $request->jam_awal;
                $data->jam_akhir = $request->jam_akhir;

                if($data->status == 1){
                    $user->jam_lembur = $user->jam_lembur - $data->jumlah_jam;
                }

                $temp = ($this->timeToInteger($request->jam_akhir) - $this->timeToInteger($request->jam_awal))/60;
                $data->jumlah_jam = $temp;

                if($data->status == 1){
                    $user->jam_lembur = $user->jam_lembur + $temp;
                    $user->update();
                }
                $data->update();
            }

            $data->update();
        }   

        return redirect('/lembur')->with('msg', 'Lembur berhasil di edit');
    }

    public function akumulasiLembur(Request $request){
        $tanggalMulai = $request->input('tanggal_mulai');
        $tanggalAkhir = $request->input('tanggal_akhir');

        session(['tanggal_mulai' => $tanggalMulai, 'tanggal_akhir' => $tanggalAkhir]);

        $data = Lembur::where('status', 1)
            ->whereBetween('tanggal', [$tanggalMulai, $tanggalAkhir])
            ->select(
                'users_id',
                \DB::raw('SUM(jumlah_jam) as total_jam'),
                )
                ->groupBy('users_id')
                ->get();
            foreach ($data as $item) {
                $item->nama = $item->user->nama;
            }

        return view('AkumulasiLembur.index', compact('data'));
    }

    public function indexAkumulasiLembur(){
        return view('AkumulasiLembur.filter');
    }

    public function showDetailLembur($id, Request $request){
        $tanggalMulai = session('tanggal_mulai');
        $tanggalAkhir = session('tanggal_akhir');

        $dataDetail = Lembur::where('users_id', $id)
            ->where('status', 1)
            ->whereBetween('tanggal', [$tanggalMulai, $tanggalAkhir])
            ->get();
        
        return view('AkumulasiLembur.detail', compact('dataDetail'));
    }
}
