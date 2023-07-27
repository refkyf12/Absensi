<?php

namespace App\Http\Controllers;

use App\Models\AkumulasiTahunan;
use Illuminate\Http\Request;
use Exception;

class AkumulasiTahunanController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $this->validate();
        $akumulasiTahunan = AkumulasiTahunan::all();
		return view('akumulasiTahunan.index',['data'=>$akumulasiTahunan]);
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
    public function show(AkumulasiTahunan $akumulasiTahunan)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(AkumulasiTahunan $akumulasiTahunan)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, AkumulasiTahunan $akumulasiTahunan)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(AkumulasiTahunan $akumulasiTahunan)
    {
        //
    }
}
