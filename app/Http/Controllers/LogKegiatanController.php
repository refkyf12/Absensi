<?php

namespace App\Http\Controllers;

use App\Models\LogKegiatan;
use App\Models\User;
use Illuminate\Http\Request;
use Exception;

class LogKegiatanController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $this->validate();
        $log_kegiatan = LogKegiatan::with('User')->get();
        if (request()->segment(1) == 'api') return response()->json([
            "error"=>false,
            "list"=>$log_kegiatan,
        ]);
        // dd($lembur);
        return view('logKegiatan.index', ['data' => $log_kegiatan]);
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
    public function show(LogKegiatan $logKegiatan)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(LogKegiatan $logKegiatan)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, LogKegiatan $logKegiatan)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(LogKegiatan $logKegiatan)
    {
        //
    }
}
