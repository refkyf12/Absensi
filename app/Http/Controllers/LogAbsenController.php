<?php

namespace App\Http\Controllers;

use App\Models\JamKurang;
use App\Models\lebihKerja;
use App\Models\Lembur;
use App\Models\logAbsen;
use App\Models\User;
use App\Models\Rules;
use App\Traits\jamKeInt;
use Illuminate\Http\Request;
use App\Imports\LogAbsenImport;
use Session;
use DB;
use Maatwebsite\Excel\Facades\Excel;
use Exception;
use Illuminate\Support\Facades\Auth;

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
        if(request()-> segment(1) =='api') return response()->json([
            "error"=> false,
            "list" => $log_absen,
        ]);
        
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
        try{
            $data = LogAbsen::find($id);
        $dataLembur = Lembur::where('users_id', $data->users_id)
                            ->where('tanggal', $data->tanggal)
                            ->first(); 
        $user = User::find($data->users_id);
        $batasWaktu = $this->getBatasKerja();
        $batasWaktu = $this->timeToInteger($batasWaktu)/60;


        $KurangOrLebih = lebihKerja::where('absen_id', $data->id)
            ->first();

        if($KurangOrLebih){
            $KurangOrLebih->delete();
        }

        $KurangOrLebih = JamKurang::where('absen_id', $data->id)
            ->first();


        if($KurangOrLebih){
            $KurangOrLebih->delete();
        }

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
                    $temp = ($this->timeToInteger($data->jam_keluar) - $this->timeToInteger($data->jam_masuk))/60;
                    $temp2 = $temp;

                    $totalJamLebih = $temp/60;
                    $totalJamLebih = (int)$totalJamLebih;

                    $totalMenitLebih = ($temp%60);
                
                    if ($totalJamLebih / 10 < 1){
                        $totalJamLebih = "0".$totalJamLebih;
                    }
                
                    if ($totalMenitLebih / 10 < 1){
                        $totalMenitLebih = "0".$totalMenitLebih;
                    }
                    $temp = $totalJamLebih.":".$totalMenitLebih;


                    $data->total_jam = $temp;

                    $lamaKerja = $this->getLamaKerja();
                    $lamaKerja = $lamaKerja * 60;

                    if($temp2 < $lamaKerja){
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
                    $temp = ($this->timeToInteger($data->jam_keluar) - $this->timeToInteger($data->jam_masuk))/60;
                    $temp2 = $temp;

                    $totalJamLebih = $temp/60;
                    $totalJamLebih = (int)$totalJamLebih;

                    $totalMenitLebih = ($temp%60);
                
                    if ($totalJamLebih / 10 < 1){
                        $totalJamLebih = "0".$totalJamLebih;
                    }
                
                    if ($totalMenitLebih / 10 < 1){
                        $totalMenitLebih = "0".$totalMenitLebih;
                    }
                    $temp = $totalJamLebih.":".$totalMenitLebih;


                    $data->total_jam = $temp;

                    $lamaKerja = $this->getLamaKerja();
                    $lamaKerja = $lamaKerja * 60;

                    if($temp2 < $lamaKerja){
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
                $temp = ($this->timeToInteger($data->jam_keluar) - $this->timeToInteger($data->jam_masuk))/60;
                $temp2 = $temp;

                $totalJamLebih = $temp/60;
                $totalJamLebih = (int)$totalJamLebih;

                $totalMenitLebih = ($temp%60);
            
                if ($totalJamLebih / 10 < 1){
                    $totalJamLebih = "0".$totalJamLebih;
                }
            
                if ($totalMenitLebih / 10 < 1){
                    $totalMenitLebih = "0".$totalMenitLebih;
                }
                $temp = $totalJamLebih.":".$totalMenitLebih;


                $data->total_jam = $temp;

                $lamaKerja = $this->getLamaKerja();
                $lamaKerja = $lamaKerja * 60;

                if($temp2 < $lamaKerja){
                    $data->keterlambatan = 1;
                }else{
                    $data->keterlambatan = 0;
                }

                $data->deskripsi = $request->deskripsi;
            }

        }else{
            $data->jam_masuk = $request->jam_masuk;
            $data->jam_keluar = $request->jam_keluar;
            $temp = ($this->timeToInteger($data->jam_keluar) - $this->timeToInteger($data->jam_masuk))/60;
            $temp2 = $temp;

                $totalJamLebih = $temp/60;
                $totalJamLebih = (int)$totalJamLebih;

                $totalMenitLebih = ($temp%60);
            
                if ($totalJamLebih / 10 < 1){
                    $totalJamLebih = "0".$totalJamLebih;
                }
            
                if ($totalMenitLebih / 10 < 1){
                    $totalMenitLebih = "0".$totalMenitLebih;
                }
                $temp = $totalJamLebih.":".$totalMenitLebih;


                $data->total_jam = $temp;

                $lamaKerja = $this->getLamaKerja();
                $lamaKerja = $lamaKerja * 60;

                if($temp2 < $lamaKerja){
                    $data->keterlambatan = 1;
                }else{
                    $data->keterlambatan = 0;
                }

            $data->deskripsi = $request->deskripsi;
        }

        $data->update();
        $user->update();

        $lebihOrKurang = ($this->timeToInteger($data->jam_keluar) - $this->timeToInteger($data->jam_masuk))/60;

        if($lebihOrKurang >= $this->getLamaKerja()*60){
            $totalLebih = $lebihOrKurang + ((int)$this->getLamaKerja())*60;
    
                    $totalJamForLebih = $totalLebih;
                
                    $totalJamForLebih = $totalLebih/60;
                    $totalJamForLebih = (int)$totalJamForLebih;
                
                    $totalMenitLebih = $totalLebih%60;
                
                    if ($totalJamForLebih / 10 < 1){
                        $totalJamForLebih = "0".$totalJamForLebih;
                    }
                
                    if ($totalMenitLebih / 10 < 1){
                        $totalMenitLebih = "0".$totalMenitLebih;
                    }
                
                    $totalKurang = $totalJamForLebih.":".$totalMenitLebih;
    
                    lebihKerja::create([
                        'users_id' => $data->users_id,
                        'absen_id' => $data->id,
                        'total_jam' => $totalKurang,
                    ]);

        }else{
            $totalKurang = ((int)$this->getLamaKerja())*60 - $lebihOrKurang;
    
                    $totalJamForKurang = $totalKurang;
                
                    $totalJamForKurang = $totalKurang/60;
                    $totalJamForKurang = (int)$totalJamForKurang;
                
                    $totalMenitKurang = $totalKurang%60;
                
                    if ($totalJamForKurang / 10 < 1){
                        $totalJamForKurang = "0".$totalJamForKurang;
                    }
                
                    if ($totalMenitKurang / 10 < 1){
                        $totalMenitKurang = "0".$totalMenitKurang;
                    }
                
                    $totalKurang = $totalJamForKurang.":".$totalMenitKurang;
    
                    JamKurang::create([
                        'users_id' => $data->users_id,
                        'absen_id' => $data->id,
                        'total_jam_kurang' => $totalKurang,
                    ]);
        }

        return redirect('/log_absen')->with('success', 'Data berhasil diperbarui');
        }catch(Exception $e){
            $errorMessage = $e->getMessage();
            return redirect('/log_absen')->with('error', 'Data gagal diperbarui' . $errorMessage);
        }
        
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
        try{
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
		return redirect('/log_absen')->with('success', 'Import data berhasil');
        }catch(Exception $e){
            $errorMessage = $e->getMessage();
            return redirect('/log_absen')->with('error', 'Import data gagal. Error : ' . $errorMessage);
        }
		
	}

    public function filter(Request $request){
        $start_date = $request->start_date;
        $end_date = $request->end_date;

        $log_absen = logAbsen::whereDate('tanggal','>=',$start_date)->whereDate('tanggal','<=',$end_date)->get();

        //dd($log_absen);

        return view('log_absen.LogAbsen', ['data'=>$log_absen]);
    }

    public function akumulasiFilter(Request $request){
        $tanggalMulai = $request->input('tanggal_mulai');
        $tanggalAkhir = $request->input('tanggal_akhir');

        session(['tanggal_mulai' => $tanggalMulai, 'tanggal_akhir' => $tanggalAkhir]);

        $data = logAbsen::whereBetween('tanggal', [$tanggalMulai, $tanggalAkhir])
            ->select(
                'users_id',
                \DB::raw('SUM(TIME_TO_SEC(total_jam)) as total_kerja_seconds'),
                \DB::raw('SUM(CASE WHEN keterlambatan = 1 THEN 1 ELSE 0 END) as total_keterlambatan')
                )
                ->groupBy('users_id')
                ->get();
            foreach ($data as $item) {
                $totalSeconds = $item->total_kerja_seconds;
                $hours = floor($totalSeconds / 3600);
                $minutes = floor(($totalSeconds % 3600) / 60);
                $item->total_kerja = sprintf('%02d:%02d', $hours, $minutes);
                $item->nama = $item->users->nama;
            }
        return view('AkumulasiLogAbsen.index', ['data'=>$data]);
    }

    public function indexAkumulasi(){
        return view('AkumulasiLogAbsen.filter');
    }

    public function showDetailLogAbsen($id, Request $request){
        $tanggalMulai = session('tanggal_mulai');
        $tanggalAkhir = session('tanggal_akhir');

        $dataDetail = logAbsen::where('users_id', $id)
            ->whereBetween('tanggal', [$tanggalMulai, $tanggalAkhir])
            ->get();
        
        return view('AkumulasiLogAbsen.detail', compact('dataDetail'));
    }
    public function show_edit($id){
        $data = LogAbsen::with('users')->find($id);        
        //dd($data);

        return view('log_absen.form_edit_LogAbsen', compact('data'));
    }

    public function getLogAbsen()
    {
        // // Mendapatkan user yang sedang login
        // $user = Auth::user();

        // // Mendapatkan data absen untuk user yang sedang login
        // $logAbsen = LogAbsen::where('users_id', $user->id)->get();
        $logAbsen = logAbsen::all();
        if(request()-> segment(1) =='api') return response()->json([
            "error"=> false,
            "list" => $logAbsen,
        ]);
    }

}
