<?php

namespace App\Http\Controllers;

use App\Models\logAbsen;
use Illuminate\Http\Request;
use App\Imports\LogAbsenImport;
use Session;
use Maatwebsite\Excel\Facades\Excel;

class LogAbsenController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
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
		Excel::import(new LogAbsenImport, public_path('/file_log_absen/'.$nama_file));
 
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
}
