<?php

namespace App\Http\Controllers;

use App\Models\Role;
use App\Models\logKegiatan;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Exception;

class RoleController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $this->validate();
        $role = Role::all();
		return view('role.index',['data'=>$role]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('role.add_form_role', [
            'title' => 'Tambah Karyawan',
            'method' => 'POST',
            'action' => 'role'
        ]); 
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        try{
            $role = new Role;
        $role->id = $request->id;
        $role->nama_role = $request->nama_role;
        $role->sisa_cuti = $request->sisa_cuti;
        $role->save();
        if (Auth::check())
                {
                    date_default_timezone_set("Asia/Jakarta");
                    $id = Auth::id();
                    $date = date("Y-m-d h:i:sa");
                    $data = $request->nama_role;
                    $text = 'Melakukan Tambah Role ' . $data;
                    $logKegiatan = new logKegiatan;
                    $logKegiatan->users_id = $id;
                    $logKegiatan->kegiatan = $text;
                    $logKegiatan->created_at = $date;
                    $logKegiatan->save();
                }
        return redirect('/role')->with('success', 'Tambah data berhasil');
        }catch(Exception $e){
            $errorMessage = $e->getMessage();
            return redirect('/role')->with('error', 'Tambah data gagal. Error : ' . $errorMessage);
        }
        
    }

    /**
     * Display the specified resource.
     */
    public function show($role)
    {
        $data = Role::find($role);
        return view('role.edit_form_role', compact('data'));
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
    public function update(Request $request, int $role)
    {
        try{
            $data = Role::find($role);
        if ($request->id != ""){
            $data->id = $request->id;
            $data->nama_role = $request->nama_role;
            $data->sisa_cuti = $request->sisa_cuti;
            if (Auth::check())
                {
                    date_default_timezone_set("Asia/Jakarta");
                    $id = Auth::id();
                    $data = $request->nama_role;
                    $date = date("Y-m-d h:i:sa");
                    $text = 'Melakukan Edit Role ' . $data;
                    $logKegiatan = new logKegiatan;
                    $logKegiatan->users_id = $id;
                    $logKegiatan->kegiatan = $text;
                    $logKegiatan->created_at = $date;
                    $logKegiatan->save();
                }
            $data->update();
            }
            return redirect('/role')->with('success', 'Data berhasil diperbarui');
        }catch(Exception $e){
            $errorMessage = $e->getMessage();
            return redirect('/role')->with('error', 'Data gagal diperbarui. Error : ' . $errorMessage);
        }
        
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Role $role)
    {
        //
    }
}
