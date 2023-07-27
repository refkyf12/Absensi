<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Ketidakhadiran;
use App\Models\User;
use App\Models\Cuti;
use App\Models\logAbsen;
use App\Models\Rules;
use Exception;
use DB;

class KetidakhadiranController extends Controller
{
    public function index(){
        $this->validate();
        $ketidakhadiran = Ketidakhadiran::all();
        return view('ketidakhadiran.index',['data'=>$ketidakhadiran]);
    }

    public function getLamaKerja(){
        $rules = Rules::where('key', "lama_kerja")->first();
        $lamaKerja = $rules["value"];
        return $lamaKerja;
    }

    public function store(){
        try{
        $tanggalKemarin  = date('Y-m-d',strtotime("-3 days"));
        $users = User::all();
        
        //$tanggalKemarin = '2023-07-17';

        $temp=[];
        foreach($users as $user){
            $logAbsenKemarin = logAbsen::where('users_id', $user->id)
            ->whereDate('tanggal', $tanggalKemarin)
            ->get();

            if($logAbsenKemarin->isEmpty()){

                $cuti = Cuti::where('users_id', $user->id)
                    ->where('tanggal_awal', '<=', $tanggalKemarin)
                    ->where('tanggal_akhir', '>=', $tanggalKemarin)
                    ->first();

                if($cuti != null){
                    DB::table('ketidakhadiran')->insert([
                        'users_id' => $user->id,
                        'tanggal' => $tanggalKemarin,
                        'deskripsi'=> $cuti->deskripsi,
                    ]);
                }else{
                    DB::table('ketidakhadiran')->insert([
                        'users_id' => $user->id,
                        'tanggal' => $tanggalKemarin,
                    ]);

                    $lamaKerja = $this->getLamaKerja();
                    $lamaKerja = $lamaKerja*60;
                    $user->jam_kurang = $user->jam_kurang + $lamaKerja;
                    $user->update();
                }
            }
            
        }

        
        // if($logAbsenKemarin -> isEmpty()){
        //     DB::table('ketidakhadiran')->insert([
        //         'users_id' => $users->id,
        //         'tanggal' => $tanggalKemarin,
        //     ]);
        // }

        
        // $logAbsenKemarin = $logAbsenKemarin->pluck('users_id')->toArray();

        // // Mengambil seluruh data user
        // $users = User::all();
        // $users = $users->pluck('id')->toArray();

        // // Memeriksa ketidakhadiran karyawan berdasarkan data logAbsen dan data user
        // $karyawanTidakHadir = [];
        // foreach ($users as $user) {
        //     // Periksa apakah karyawan sudah absen di logAbsen
        //     $karyawanHadir = in_array($user, $logAbsenKemarin);


        //     // Jika karyawan tidak hadir, tambahkan ke daftar karyawan yang tidak hadir
        //     if ($karyawanHadir) {
        //         $karyawanTidakHadir[] = [
        //             'users_id' => $user->id,
        //             'tanggal' => $tanggalKemarin,
        //         ];
        //     }
            
        // }
        // dd(1);
        // dd($users, $logAbsenKemarin, $karyawanTidakHadir);
        // dd($karyawanTidakHadir);

        // // Simpan karyawan yang tidak hadir ke dalam database tabel ketidakhadiran
        // Ketidakhadiran::insert($karyawanTidakHadir);

        // Response atau pesan berhasil
            return redirect('/ketidakhadiran')->with('success', 'Berhasil mengambil data');
        }catch(Exception $e){
            $errorMessage = $e->getMessage();
            return redirect('/ketidakhadiran')->with('msg', 'Gagal mengambil data. Error : '. $errorMessage);
        }
        
  
    }

    public function edit($id, Request $request){
        $data = Ketidakhadiran::find($id);

        $user = User::where('id', $data->users_id)->first();

        $lamaKerja = $this->getLamaKerja();
        $lamaKerja = $lamaKerja*60;

        if($data->deskripsi == null && $request->deskripsi != null){
            $user->jam_kurang = $user->jam_kurang - $lamaKerja;
            $user->update();
        }

        $data->deskripsi = $request->deskripsi;

        $data->update();

        return redirect('/ketidakhadiran')->with('msg', 'Tambah akun berhasil');
    }

    public function show_edit($id){
        $data = Ketidakhadiran::with('users')->find($id);        
        //dd($data);

        return view('ketidakhadiran.form_edit_ketidakhadiran', compact('data'));
    }
}
