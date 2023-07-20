<?php

namespace App\Http\Controllers;

use App\Models\Lembur;
use App\Models\logAbsen;
use App\Models\User;
use App\Models\Rules;
use App\Traits\jamKeInt;
use Illuminate\Http\Request;
use App\Imports\LogAbsenImport;
use Session;
use Maatwebsite\Excel\Facades\Excel;

class LogAbsenController extends Controller
{
    use jamKeInt;

    public function getBatasKerja(){
        $rules = Rules::where('key', "batas_waktu")->first();
        $batasKerja = $rules["value"];
        return $batasKerja;
    }

    public function getLamaKerja(){
        $rules = Rules::where('key', "lama_kerja")->first();
        $lamaKerja = $rules["value"];
        return $lamaKerja;
    }

    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $this->validate();
        $log_absen = logAbsen::all();
		return view('log_absen.LogAbsen',['data'=>$log_absen]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     */
    public function show(logAbsen $logAbsen)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit($id, Request $request)
    {
        $data = LogAbsen::find($id);
        $dataLembur = Lembur::where('users_id', $data->users_id)
                            ->where('tanggal', $data->tanggal)
                            ->first(); 
        $user = User::find($data->users_id);
        $batasWaktu = $this->getBatasKerja();
        $batasWaktu = $this->timeToInteger($batasWaktu)/60;

        if($dataLembur != null){
            if($dataLembur->status == 1 && $dataLembur->status_kerja == 1){

                $totalJamLembur = $dataLembur->jumlah_jam;

                $jamMasuk = $this->timeToInteger($data->jam_masuk)/60;
                $jamKeluar = $this->timeToInteger($data->jam_keluar)/60;
                $jamAwalLembur = $this->timeToInteger($dataLembur->jam_awal)/60;
                $jamAkhirLembur = $this->timeToInteger($dataLembur->jam_akhir)/60;

                $jamMasukKantor = $this->getBatasKerja();
                $jamMasukKantor = $this->timeToInteger($jamMasukKantor)/60;

                $lebih = ($jamKeluar-$jamMasuk)-(((int)$this->getLamaKerja())*60);

                if($jamAwalLembur > ($jamMasuk+(((int)$this->getLamaKerja())*60))){
                    $masukLebih1 = ($jamAwalLembur - ($jamMasuk+(((int)$this->getLamaKerja())*60)));
                    $lebih = $lebih - $masukLebih1;

                    $lebih = $lebih - $totalJamLembur;

                    if($lebih <= 0){
                        $lebih = 0;
                    }

                    $user->jam_lebih = $user->jam_lebih - ($masukLebih1 + $lebih);
                    //------------------------------------------------------------

                    $data->jam_masuk = $request->jam_masuk;
                    $data->jam_keluar = $request->jam_keluar;
                    $data->total_jam = ($this->timeToInteger($data->jam_keluar) - $this->timeToInteger($data->jam_masuk)) / 60;

                    if(($this->timeToInteger($data->jam_masuk)/60) > $batasWaktu){
                        $data->keterlambatan = 1;
                    }else{
                        $data->keterlambatan = 0;
                    }

                    $data->deskripsi = $request->deskripsi;

                    //------------------------------------------------------------

                    $jamMasuk = $this->timeToInteger($data->jam_masuk)/60;
                    $jamKeluar = $this->timeToInteger($data->jam_keluar)/60;

                    $lebih = ($jamKeluar-$jamMasuk)-(((int)$this->getLamaKerja())*60);

                    if($jamAwalLembur > ($jamMasuk+(((int)$this->getLamaKerja())*60))){
                        $masukLebih1 = ($jamAwalLembur - ($jamMasuk+(((int)$this->getLamaKerja())*60)));//dari selesai jam kerja hingga jam awal lembur
                        $lebih = $lebih - $masukLebih1;

                        $lebih = $lebih - $totalJamLembur;

                        if($lebih <= 0){
                            $lebih = 0;
                        }

                        $user->jam_lebih = $user->jam_lebih + ($masukLebih1 + $lebih);
                    }else if($jamAwalLembur < $jamMasukKantor){
                        $lebih1 = ($jamAwalLembur - $jamMasuk);

                        if($lebih1 < 0){
                            $lebih1 = 0;
                        }

                        if($jamAkhirLembur > $jamMasuk){
                            $lebih2 = $jamMasukKantor - $jamAkhirLembur;
                        }else{
                            $lebih2 = $jamAkhirLembur - $jamMasukKantor;
                        }
                        

                        if($lebih2 < 0){
                            $lebih2 = 0;
                        }

                        $lebih = $lebih - ($lebih1 + $lebih2 + $totalJamLembur);

                        if($lebih < 0){
                            $lebih = 0;
                        }

                        $user->jam_lebih = $user->jam_lebih + ($lebih1 + $lebih2 + $lebih);
                    }

                }

                if($jamAwalLembur < $jamMasukKantor){
                    $lebih1 = ($jamAwalLembur - $jamMasuk);

                    if($lebih1 < 0){
                        $lebih1 = 0;
                    }

                    if($jamAkhirLembur > $jamMasuk){
                        
                        $lebih2 = $jamMasukKantor - $jamAkhirLembur;
                    }else{
                        $lebih2 = $jamAkhirLembur - $jamMasukKantor;
                    }
                    

                    if($lebih2 < 0){
                        $lebih2 = 0;
                    }

                    $lebih = $lebih - ($lebih1 + $lebih2 + $totalJamLembur);

                    if($lebih < 0){
                        $lebih = 0;
                    }

                    $user->jam_lebih = $user->jam_lebih - ($lebih1 + $lebih2 + $lebih);
                    //-----------------------------------------------------------------
                    $data->jam_masuk = $request->jam_masuk;
                    $data->jam_keluar = $request->jam_keluar;
                    $data->total_jam = ($this->timeToInteger($data->jam_keluar) - $this->timeToInteger($data->jam_masuk)) / 60;

                    if(($this->timeToInteger($data->jam_masuk)/60) > $batasWaktu){
                        $data->keterlambatan = 1;
                    }else{
                        $data->keterlambatan = 0;
                    }

                    $data->deskripsi = $request->deskripsi;

                    //------------------------------------------------------------

                    $jamMasuk = $this->timeToInteger($data->jam_masuk)/60;
                    $jamKeluar = $this->timeToInteger($data->jam_keluar)/60;

                    $lebih = ($jamKeluar-$jamMasuk)-(((int)$this->getLamaKerja())*60);

                    if($jamAwalLembur > ($jamMasuk+(((int)$this->getLamaKerja())*60))){
                        $masukLebih1 = ($jamAwalLembur - ($jamMasuk+(((int)$this->getLamaKerja())*60)));//dari selesai jam kerja hingga jam awal lembur
                        $lebih = $lebih - $masukLebih1;

                        $lebih = $lebih - $totalJamLembur;

                        if($lebih <= 0){
                            $lebih = 0;
                        }

                        $user->jam_lebih = $user->jam_lebih + ($masukLebih1 + $lebih);
                    }else if($jamAwalLembur < $jamMasukKantor){
                        $lebih1 = ($jamAwalLembur - $jamMasuk);

                        if($lebih1 < 0){
                            $lebih1 = 0;
                        }

                        if($jamAkhirLembur > $jamMasuk){
                            $lebih2 = $jamMasukKantor - $jamAkhirLembur;
                        }else{
                            $lebih2 = $jamAkhirLembur - $jamMasukKantor;
                        }
                        

                        if($lebih2 < 0){
                            $lebih2 = 0;
                        }

                        $lebih = $lebih - ($lebih1 + $lebih2 + $totalJamLembur);

                        if($lebih < 0){
                            $lebih = 0;
                        }

                        $user->jam_lebih = $user->jam_lebih + ($lebih1 + $lebih2 + $lebih);
                    }
                }

            }else{
                $data->jam_masuk = $request->jam_masuk;
                $data->jam_keluar = $request->jam_keluar;
                $data->total_jam = ($this->timeToInteger($data->jam_keluar) - $this->timeToInteger($data->jam_masuk)) / 60;

                if(($this->timeToInteger($data->jam_masuk)/60) > $batasWaktu){
                    $data->keterlambatan = 1;
                }else{
                    $data->keterlambatan = 0;
                }

                $data->deskripsi = $request->deskripsi;
            }

        }else{
            $data->jam_masuk = $request->jam_masuk;
            $data->jam_keluar = $request->jam_keluar;
            $data->total_jam = ($this->timeToInteger($data->jam_keluar) - $this->timeToInteger($data->jam_masuk)) / 60;

            if(($this->timeToInteger($data->jam_masuk)/60) > $batasWaktu){
                $data->keterlambatan = 1;
            }else{
                $data->keterlambatan = 0;
            }

            $data->deskripsi = $request->deskripsi;
        }

        $data->update();
        $user->update();

        return redirect('/log_absen')->with('msg', 'Log Absen berhasil di edit');
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, logAbsen $logAbsen)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(logAbsen $logAbsen)
    {
        //
    }

    public function import_excel(Request $request) 
	{
		// validasi
		$this->validate($request, [
			'file' => 'required|mimes:csv,xls,xlsx'
		]);
 
		// menangkap file excel
		$file = $request->file('file');
 
		// membuat nama file unik
		$nama_file = rand().$file->getClientOriginalName();
 
		// upload ke folder file_siswa di dalam folder public
		$file->move('file_log_absen',$nama_file);
 
        // import data
        $batas = new LogAbsenImport;
        $rules = Rules::where('key', "batas_waktu")->first(); 
        // $batasKerja = Rules::where('key', "batas_kerja")->first();
        // $batasKerja->setBatasKerja($batasKerja["value"]);
        $batas->setBatasWaktu($rules["value"]);
		Excel::import($batas, public_path('/file_log_absen/'.$nama_file));
 
		// notifikasi dengan session
		Session::flash('sukses','Data Absen Berhasil Diimport!');
 
		// alihkan halaman kembali
		return redirect('/log_absen');
	}

    public function filter(Request $request){
        $start_date = $request->start_date;
        $end_date = $request->end_date;

        $log_absen = logAbsen::whereDate('tanggal','>=',$start_date)->whereDate('tanggal','<=',$end_date)->get();

        //dd($log_absen);

        return view('log_absen.LogAbsen', ['data'=>$log_absen]);
    }

    public function show_edit($id){
        $data = LogAbsen::with('users')->find($id);        
        //dd($data);

        return view('log_absen.form_edit_LogAbsen', compact('data'));
    }

}
