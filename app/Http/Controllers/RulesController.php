<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Rules;

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
        $data = Rules::find($id);
        if ($request->value != ""){
            $data->key = $request->key;
            $data->value = $request->value;
            $data->update();
            return redirect('/rules')->with('msg', 'Rules berhasil diperbarui');
        } else {
            return Redirect::back()->withErrors(['msg' => 'Password harus diisi']);
        }
    }
}
