<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Session;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;


class UserController extends Controller
{
    /**
     * Display a listing of the resource.
     */

    public function loginview()
    {
        return view('auth.login');
    }  

    public function login(Request $request)
    {
        $request->validate([
            'email' => 'required',
            'password' => 'required',
        ]);
   
        if (Auth::attempt([
            'email' => $request->email,
            'password' => $request->password
            ])){
                session()->put('nama', Auth::user()->name);
                return redirect('/log_absen');
        } else {
            return redirect('/login')->with('msg', 'Email/Password salah');   
        }
    }

    public function dashboard()
    {
        if(Auth::check()){
            return view('LogAbsen');
        }
  
        return redirect("login")->withSuccess('You are not allowed to access');
    }
    
    public function logout() {
        \Session::flush();
        Auth::logout();
  
        return Redirect('login');
    }

    public function index()
    {
        $karyawan = User::all();
        if(request()-> segment(1) == 'api') return response()->json([
            "error"=>false,
            "list"=>$karyawan,
        ]);

        return view('karyawan.index', ['data' => $karyawan]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('karyawan.form_add_account', [
            'title' => 'Tambah Karyawan',
            'method' => 'POST',
            'action' => 'karyawan'
        ]); 
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $karyawan = new User;
        $karyawan->nama = $request->nama;
        $karyawan->email = $request->email;
        $pass_crypt = bcrypt($request->password);
        $karyawan->password = $pass_crypt;

        $karyawan->save();
        return redirect('/karyawan')->with('msg', 'Tambah akun berhasil');
    }

    /**
     * Display the specified resource.
     */
    public function show($users_id)
    {
        $data = User::find($users_id);
        return view('karyawan.form_edit_account', compact('data'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit($users_id)
    {
        $dt = User::find($users_id);
    	$title = "Edit Karyawan $dt->nama";

    	return view('karyawan.edit',compact('dt'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, int $users_id)
    {
        $data = User::find($users_id);
        if ($request->password != ""){
            $data->nama = $request->nama;
            $data->email = $request->email;
            $pass_crypt = bcrypt($request->password);
            $data->password = $pass_crypt;
            $data->update();
            return redirect('/karyawan')->with('msg', 'Akun berhasil diperbarui');
        } else {
            $users_id = optional(Auth::user())->users_id;
            return Redirect::back()->withErrors(['msg' => 'Password harus diisi']);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function delete($users_id)
    {
        $data = User::find($users_id);
        $data -> delete();
        return redirect('/karyawan')->with('msg', 'Data Berhasil di Hapus');
    }

    public function lebihKurangLembur($id)
    {
        $user = User::find($id);
        $user->jam_lebih = $user->jam_lebih -  ($user->jam_lembur*60);
        $user->jam_lembur = null;
        $user->save();

        return redirect('/karyawan')->with('msg', 'Tambah akun berhasil');
    }

    public function reset()
    {

        User::query()->update([
            'sisa_cuti' => 12,
            'jam_lebih' => null,
            'jam_lembur' => null,
        ]);

        return redirect('/karyawan')->with('msg', 'Tambah akun berhasil');
    }
}
