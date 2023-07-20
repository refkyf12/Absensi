<?php

namespace App\Http\Controllers;

use App\Models\logAbsen;
use App\Models\Rules;
use Illuminate\Http\Request;
use App\Imports\LogAbsenImport;
use Session;
use DB;
use Maatwebsite\Excel\Facades\Excel;

class LogAbsenController extends Controller
{
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
    public function edit(logAbsen $logAbsen)
    {
        //
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
}
