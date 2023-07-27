<?php

namespace App\Http\Controllers;

use App\Models\lebihKerja;
use Illuminate\Http\Request;
use Exception;

class LebihKerjaController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $this->validate();
        $lebihKerja = lebihKerja::with('log_absen')->get();
        //dd($lebihKerja);
		return view('lebihKerja.index',['data'=>$lebihKerja]);
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
    public function show(Lembur $lembur)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Lembur $lembur)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Lembur $lembur)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Lembur $lembur)
    {
        //
    }

}
