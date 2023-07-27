<?php

namespace App\Http\Controllers;

use App\Models\LogActivity;
use Illuminate\Http\Request;

use Monolog\Logger;
use Monolog\Handler\StreamHandler;
use Monolog\Handler\FirePHPHandler;

use App\lib\TAD;
use App\Models\logAbsen;
use App\Models\Lembur;
use App\Models\User;
use App\Models\Rules;
use App\Models\JamKurang;
use App\Http\Controllers\TADFactory;
use Exception;

class LogActivityController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $log_activity = LogActivity::all();
		return view('logActivity.index',['data'=>$log_activity]);
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
        try{
            $logger = new Logger('soap-service');
        $logger->pushHandler(new StreamHandler(__DIR__.'/logs/'.date( "Y-m-d").'.log', Logger::DEBUG));
        $logger->pushHandler(new FirePHPHandler());
        $tad = (new TADFactory((['ip'=> '10.50.0.60', 'com_key'=>0])))->get_instance();
        echo 'starting read data in machine finger print ..'. getenv('IP_MESIN_ABSEN') . "<br/>";
        $logs = $tad->get_att_log();
        $data = $logs->to_json();


        $conv = json_decode($data,true);

        $yesterday = date('Y-m-d',strtotime("-1 days"));
        foreach ($conv['Row'] as $data){
            $id = $data['PIN'];
            $datetime = $data['DateTime'];
            $tanggalLog = urldecode(date("Y-m-d", strtotime($data['DateTime'])));
            $jamTapping = urldecode(date("H:i", strtotime($data['DateTime'])));
            if($tanggalLog == $yesterday){
                $logActivity = new LogActivity;
                $logActivity->users_id = $id;
                $logActivity->tanggal = $tanggalLog;
                $logActivity->jam_tapping = $jamTapping;
                $logActivity->save();
            }

            
        }
        return redirect('/log_activity')->with('success', 'Berhasil mengambil data');
        }catch(Exception $e){
            $errorMessage = $e->getMessage();
            return redirect('/log_activity')->with('error', 'Gagal mengambil data. Error : ' . $errorMessage);
        }
        
    }

    /**
     * Display the specified resource.
     */
    public function show(LogActivity $logActivity)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(LogActivity $logActivity)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, LogActivity $logActivity)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(LogActivity $logActivity)
    {
        //
    }
}
