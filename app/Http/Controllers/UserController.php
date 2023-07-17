<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Session;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use App\Models\logKegiatan;
use App\Models\Cuti;
use App\Models\Lembur;
use App\Models\logAbsen;
use App\Models\LogActivity;
use App\Models\JamKurang;
use App\Models\Role;
use App\Models\lebihKerja;
use App\Models\AkumulasiTahunan;


class UserController extends Controller
{
    /**
     * Display a listing of the resource.
     */

    public function loginview()
    {
        return view('auth.login');
    } 
    
    public function getId()
    {
        return $this->id;
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
        $karyawan->id = $request->users_id;
        $karyawan->nama = $request->nama;
        $karyawan->email = $request->email;
        $pass_crypt = bcrypt($request->password);
        $karyawan->role_id = $request->role_id;
        $karyawan->password = $pass_crypt;

        $karyawan->save();
        if (Auth::check())
                {
                    date_default_timezone_set("Asia/Jakarta");
                    $id = Auth::id();
                    $date = date("Y-m-d h:i:sa");
                    $data = $request->nama;
                    $text = 'Melakukan Tambah Karyawan ' . $data;
                    $logKegiatan = new logKegiatan;
                    $logKegiatan->users_id = $id;
                    $logKegiatan->kegiatan = $text;
                    $logKegiatan->created_at = $date;
                    $logKegiatan->save();
                }
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
        $lebihKerja = lebihKerja::find($users_id);
        $cuti = Cuti::find($users_id);
        if ($request->password != ""){
            $data->id = $request->id;
            // $lebihKerja->users_id = $request->id;
            $cuti->users_id = $request->id;
            $data->nama = $request->nama;
            $data->email = $request->email;
            $pass_crypt = bcrypt($request->password);
            $data->password = $pass_crypt;
            $data->role_id = $request->role;
            $cuti->update();
            // $lebihKerja->update();
            $data->update();

            if (Auth::check())
                {
                    date_default_timezone_set("Asia/Jakarta");
                    $id = Auth::id();
                    $karyawan = $request->nama;
                    $date = date("Y-m-d h:i:sa");
                    $text = 'Melakukan Edit Karyawan ' . $karyawan;
                    $logKegiatan = new logKegiatan;
                    $logKegiatan->users_id = $id;
                    $logKegiatan->kegiatan = $text;
                    $logKegiatan->created_at = $date;
                    $logKegiatan->save();
                }
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

        $users = User::all();
        date_default_timezone_set('Asia/Jakarta');
        $date = date('Y-m-d H:i:s');

        foreach ($users as $user) {
            // Buat entri baru di tabel "akumulasitahunan" dengan data pengguna
            AkumulasiTahunan::create([
                'users_id' => $user->id,
                'nama' => $user->nama,
                'email' => $user->email,
                'role_id' => $user->role_id,
                'jam_lebih' => $user->jam_lebih,
                'jam_kurang' => $user->jam_kurang,
                'jam_lembur' => $user->jam_lembur,
                'created_at' => $date,
                // Masukkan field lainnya sesuai kebutuhan
            
            ]);

            
        }

        foreach ($users as $user){
            $roles = Role::find($user->role_id);
            $user->sisa_cuti = $roles->sisa_cuti;
            $user->save();

           
        }
        User::query()->update([
            'jam_lebih' => null,
            'jam_lembur' => null,
            'jam_kurang' => null,
            ]);
        

        
        

        

        if (Auth::check())
                {
                    date_default_timezone_set("Asia/Jakarta");
                    $id = Auth::id();
                    $date = date("Y-m-d h:i:sa");
                    $text = 'Melakukan Reset Pada Karyawan ';
                    $logKegiatan = new logKegiatan;
                    $logKegiatan->users_id = $id;
                    $logKegiatan->kegiatan = $text;
                    $logKegiatan->created_at = $date;
                    $logKegiatan->save();
                }
        
        return redirect('/karyawan')->with('msg', 'Tambah akun berhasil');
    }
}
