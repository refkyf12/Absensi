<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Rules;
use App\Models\logKegiatan;
use Exception;
use Illuminate\Support\Facades\Auth;

class RulesController extends Controller
{
    public function index()
    {
        $this->validate();
        $rules = Rules::all();
		return view('rules.index',['data'=>$rules]);
    }

    public function show($id)
    {
        $data = Rules::find($id);
    	return view('rules.form_edit_rules',compact('data'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, int $id)
    {
        try{
            $data = Rules::find($id);
        if ($request->value != ""){
            $data->key = $request->key;
            $data->value = $request->value;
            $data->update();
            if (Auth::check())
                {
                    date_default_timezone_set("Asia/Jakarta");
                    $id = Auth::id();
                    $date = date("Y-m-d h:i:sa");
                    $waktu = $request->value;
                    $text = 'Melakukan Edit Rules Menjadi ' . $waktu;
                    $logKegiatan = new logKegiatan;
                    $logKegiatan->users_id = $id;
                    $logKegiatan->kegiatan = $text;
                    $logKegiatan->created_at = $date;
                    $logKegiatan->save();
                }
        }
        return redirect('/rules')->with('success', 'Rules berhasil diperbarui'); 
        }catch(Exception $e){
            $errorMessage = $e->getMessage();
            return redirect('/rules')->with('error', 'Rules gagal diperbarui. Error : ' . $errorMessage);
        }
        
    }
}
