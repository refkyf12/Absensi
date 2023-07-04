<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Hash;

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
        Session::flush();
        Auth::logout();
  
        return Redirect('login');
    }

    public function index()
    {
        //
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
    public function show(Users $users)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Users $users)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Users $users)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Users $users)
    {
        //
    }
}
