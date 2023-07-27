<?php

namespace App\Http\Controllers;

use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Routing\Controller as BaseController;

class Controller extends BaseController
{
    use AuthorizesRequests, ValidatesRequests;

    public function validate(){
        // if(\Auth::user()->role_id == 0) {
        //     return redirect('/login')->with('error', 'Karyawan tidak bisa menggunakan aplikasi');
        // }
    }
}
