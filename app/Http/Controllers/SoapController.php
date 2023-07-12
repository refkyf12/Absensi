<?php

namespace App\Http\Controllers;

// require 'vendor/autoload.php';



use Illuminate\Http\Request;

use Monolog\Logger;
use Monolog\Handler\StreamHandler;
use Monolog\Handler\FirePHPHandler;

use App\lib\TAD;
use App\Models\logAbsen;
use App\Http\Controllers\TADFactory;

class SoapController extends Controller
{
    public function getDataTAD(){
        $tad = (new TADFactory((['ip'=> '10.50.0.60', 'com_key'=>0])))->get_instance();
        return $tad;
    }
    public function logAbsenStore(Request $request){
        // $dotenv = Dotenv\Dotenv::create(__DIR__);
        // $dotenv->load();   
        $logger = new Logger('soap-service');
        // Now add some handlers
        $logger->pushHandler(new StreamHandler(__DIR__.'/logs/'.date( "Y-m-d").'.log', Logger::DEBUG));
        $logger->pushHandler(new FirePHPHandler());
        $tad = (new TADFactory((['ip'=> '10.50.0.60', 'com_key'=>0])))->get_instance();
        echo 'starting read data in machine finger print ..'. getenv('IP_MESIN_ABSEN') . "<br/>";
        $logs = $tad->get_att_log();
        $data = $logs->to_json();


        $conv = json_decode($data,true);

        // function getDatetimeNow() {
        //     $tz_object = new DateTimeZone('Asia/Jakarta');
        //     //date_default_timezone_set('Brazil/East');

        //     $datetime = new DateTime();
        //     $datetime->setTimezone($tz_object);
        //     return $datetime->format('Y-m-d');
        // }

        // $currentDate = getDatetimeNow();

        $yesterday = date('Y-m-d',strtotime("-1 days"));
        $e = 0;

        $absensi = [];



        foreach ($conv['Row'] as $data){
            $id = $data['PIN'];
            $datetime = $data['DateTime'];
            $tanggalLog = urldecode(date("Y-m-d", strtotime($data['DateTime'])));

            if($tanggalLog == $yesterday){

                if(isset($absensi[$id])) {
                    if($datetime > $absensi[$id]['jam_terakhir']){
                        $absensi[$id]['jam_terakhir'] = $datetime;
                    }
                } else {
                    $absensi[$id] = [
                        'id_karyawan' => $id,
                        'jam_pertama' => $datetime,
                        'jam_terakhir' => $datetime
                    ];
                }
            }

            
        }

        foreach ($absensi as $item) {
            $logAbsen = new logAbsen;
            $jamAwal = urldecode(date("H:i", strtotime($item['jam_pertama'])));
            $jamAkhir = urldecode(date("H:i", strtotime($item['jam_terakhir'])));
            if($logAbsen->users_id != $item['id_karyawan']){
                $logAbsen->users_id = $item['id_karyawan'] ;
                $logAbsen->tanggal = $yesterday;
                $logAbsen->jam_masuk = $jamAwal;
                $logAbsen->jam_keluar = $jamAkhir;
                $logAbsen->save();
            }
        }
        return redirect('/log_absen')->with('msg', 'Tambah akun berhasil');
    }
}
