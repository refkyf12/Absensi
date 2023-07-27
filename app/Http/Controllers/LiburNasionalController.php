<?php

namespace App\Http\Controllers;

use App\Models\liburNasional;
use Illuminate\Http\Request;
use DB;
use Illuminate\Support\Facades\Auth;
use App\Models\logKegiatan;
use Exception;

class LiburNasionalController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $this->validate();
        $libur_nasional = liburNasional::all();
		return view('liburNasional.index',['data'=>$libur_nasional]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(){
        return view('liburNasional.add_form_libur', [
            'title' => 'Tambah Libur',
            'method' => 'POST',
            'action' => 'libur_nasional'
        ]);
    }

    public function store(Request $request){
        try{
            $libur_nasional = new liburNasional;
        $libur_nasional->tanggal = $request->tanggal ;
        $libur_nasional->deskripsi = $request->deskripsi;
        $libur_nasional->save();

        if (Auth::check())
                {
                    date_default_timezone_set("Asia/Jakarta");
                    $id = Auth::id();
                    $date = date("Y-m-d h:i:sa");
                    $data = $request->nama;
                    $text = 'Melakukan Tambah Hari Libur ' . $data;
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

        return redirect('/libur')->with('msg', 'Data berhasil di tambah');
        }catch(Exception $e){
            $errorMessage = $e->getMessage();
            return redirect('/libur')->with('msg', 'Data gagal di tambah. Error : ' . $errorMessage);
        }
        
    }

    /**
     * Display the specified resource.
     */
    public function show($id)
    {
        $libur_nasional = liburNasional::find($id);
        return view('liburNasional.edit_form_libur',['data'=>$libur_nasional]);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Role $role)
    {

    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, int $id)
    {
        try{
            $data = liburNasional::find($id);
        if ($request->tanggal != ""){
            $data->tanggal = $request->tanggal;
            $data->deskripsi = $request->deskripsi;
            $data->update();

            if (Auth::check())
                {
                    date_default_timezone_set("Asia/Jakarta");
                    $id = Auth::id();
                    $data = $request->deskripsi;
                    $date = date("Y-m-d h:i:sa");
                    $text = 'Melakukan Edit Libur Nasional ' . $data;
                    $logKegiatan = new logKegiatan;
                    $logKegiatan->users_id = $id;
                    $logKegiatan->kegiatan = $text;
                    $logKegiatan->created_at = $date;
                    $logKegiatan->save();
                }
            }
            return redirect('/libur')->with('success', 'Data berhasil diperbarui');
        }catch(Exception $e){
            $errorMessage = $e->getMessage();
            return redirect('/libur')->with('error', 'Data gagal diperbarui' . $errorMessage);
        }
        
    }
    
}
