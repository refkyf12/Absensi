<?php

namespace App\Http\Controllers;

// require 'vendor/autoload.php';



use Illuminate\Http\Request;

use Monolog\Logger;
use Monolog\Handler\StreamHandler;
use Monolog\Handler\FirePHPHandler;

use App\lib\TAD;
use App\Models\logAbsen;
use App\Models\Lembur;
use App\Models\lebihKerja;
use App\Models\User;
use App\Models\Rules;
use App\Models\JamKurang;
use App\Http\Controllers\TADFactory;

class SoapController extends Controller
{
    public function getDataTAD(){
        $tad = (new TADFactory((['ip'=> '10.50.0.60', 'com_key'=>0])))->get_instance();
        return $tad;
    }

    public function getBatasWaktu(){
        $rules = Rules::where('key', "batas_waktu")->first();
        $batas = $rules["value"];
        return $batas;
    }
    public function getLamaKerja(){
        $rules = Rules::where('key', "lama_kerja")->first();
        $lamaKerja = $rules["value"];
        return $lamaKerja;
    }
    public function logAbsenStore(Request $request)
    {
        // $dotenv = Dotenv\Dotenv::create(_DIR_);
        // $dotenv->load();   
        $logger = new Logger('soap-service');
        // Now add some handlers
        // $logger->pushHandler(new StreamHandler(_DIR_.'/logs/'.date( "Y-m-d").'.log', Logger::DEBUG));
        // $logger->pushHandler(new FirePHPHandler());
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

        $yesterday = date('Y-m-d',strtotime("-2 days"));
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

                $total = (strtotime($jamAkhir) - strtotime($jamAwal));
                $totaljam = $total/3600;
                $totaljam = (int)$totaljam;
                $totalmenit = ($total%3600)/60;

                

                if ($totaljam / 10 < 1){
                    $totaljam = "0".$totaljam;
                }
        
                if ($totalmenit / 10 < 1){
                    $totalmenit = "0".$totalmenit;
                }
        
                $totalWaktu = $totaljam.":".$totalmenit;

                $lamaKerja = $this->getLamaKerja();
                $batasWaktu = $this->getBatasWaktu();
                if (strtotime($jamAwal) > strtotime($batasWaktu))  {
                    $statusTerlambat = true;
                }else{
                    $statusTerlambat = false;
                }

                $logAbsen->total_jam = $totalWaktu;
                $logAbsen->keterlambatan = $statusTerlambat;
                $logAbsen->save();

                $id = $logAbsen->id;

                if ($total >= 28800){ //28800 = 8 jam
                    $totalLebih = ((strtotime($jamAkhir)-1688947200)-(strtotime($jamAwal)-1688947200))-28800;
                    $jamKerjaLebih = $totalLebih;
                    $lebihForLembur = $totalLebih/60;
                    // --------------------------------------------------------------------------------------------------
                    $lembur = Lembur::where('users_id', $item['id_karyawan'])->where('tanggal', $yesterday)->first();                
                    if($lembur != null){
                        if($lembur->status == 1){
                            if($lebihForLembur > $lembur->jumlah_jam){ //tambahan
                                $lebihForLembur = $lebihForLembur - $lembur->jumlah_jam;
                                $totalLebih = $lebihForLembur*60;
                            }else{
                                $totalLebih = 0;
                            }
                        }
                        //$newValue = $newValue - $lembur->jumlah_jam;
                    }
                    //----------------------------------------------------------------------------------------------------------------
                    $totalJamForLebih = $totalLebih;
                
                    //----------------------------------------------------------------------------------------------------------------
                    $totalJamLebih = $jamKerjaLebih/3600;
                    $totalJamLebih = (int)$totalJamLebih;
                
                    $totalMenitLebih = ($jamKerjaLebih%3600)/60;
                
                    if ($totalJamLebih / 10 < 1){
                        $totalJamLebih = "0".$totalJamLebih;
                    }
                
                    if ($totalMenitLebih / 10 < 1){
                        $totalMenitLebih = "0".$totalMenitLebih;
                    }
                
                    $jamKerjaLebih = $totalJamLebih.":".$totalMenitLebih;
    
                    lebihKerja::create([
                        'users_id' => $item['id_karyawan'],
                        'absen_id' => $id,
                        'total_jam' => $jamKerjaLebih,
                    ]);
                    //----------------------------------------------------------------------------------------------------------------
    
    
                    $newValue = $totalJamForLebih/60;
    
                    // User::where('id', $row[1])->update(['jam_lebih' => $newValue]);
    
                    $user = User::find($item['id_karyawan']);
                    $user->jam_lebih = $user->jam_lebih + $newValue;
                    $user->save();
                    
                }
                
                if ($total < 28800){
                    $totalKurang = 28800 - $total;
                    $newValue = $totalKurang/60;
    
                    $totalJamForKurang = $totalKurang;
                
                    $totalJamForKurang = $totalKurang/3600;
                    $totalJamForKurang = (int)$totalJamForKurang;
                
                    $totalMenitKurang = ($totalKurang%3600)/60;
                
                    if ($totalJamForKurang / 10 < 1){
                        $totalJamForKurang = "0".$totalJamForKurang;
                    }
                
                    if ($totalMenitKurang / 10 < 1){
                        $totalMenitKurang = "0".$totalMenitKurang;
                    }
                
                    $totalKurang = $totalJamForKurang.":".$totalMenitKurang;
    
                    JamKurang::create([
                        'users_id' => $item['id_karyawan'],
                        'absen_id' => $id,
                        'total_jam_kurang' => $totalKurang,
                    ]);
    
                    $user = User::find($item['id_karyawan']);
                    $user->jam_kurang = $user->jam_kurang + $newValue;
                    $user->save();
                }
            }
        }
        return redirect('/log_absen')->with('msg', 'Tambah akun berhasil');
    }


    public function getJulyData(Request $request)
    {
        // $dotenv = Dotenv\Dotenv::create(_DIR_);
        // $dotenv->load();   
        $logger = new Logger('soap-service');
        // Now add some handlers
        // $logger->pushHandler(new StreamHandler(_DIR_.'/logs/'.date( "Y-m-d").'.log', Logger::DEBUG));
        // $logger->pushHandler(new FirePHPHandler());
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
        
        date_default_timezone_set("Asia/Jakarta");
        $yesterday = date('Y-m-d',strtotime("-1 days"));
        $e = 0;
        $startDate = strtotime('2023-07-01');
        $endTime = strtotime('2023-07-30');

        $absensi = [];


        foreach ($conv['Row'] as $data){
            $id = $data['PIN'];
            $tanggal = $data['DateTime'];
            $datetime = strtotime($data['DateTime']);

            if($datetime >= $startDate){
                if(isset($absensi[$id])) {
                    if($datetime > strtotime($absensi[$id]['jam_terakhir'])){
                        $absensi[$id]['jam_terakhir'] = $tanggal;
                    }
                } else {
                    $absensi[$id] = [
                        'id_karyawan' => $id,
                        'jam_pertama' => $tanggal,
                        'jam_terakhir' => $tanggal
                    ];
                }
            }
            
        }

        foreach ($absensi as $item) {
            $logAbsen = new logAbsen;
            $tanggalLog = urldecode(date("Y-m-d", strtotime($data['DateTime'])));
            $jamAwal = urldecode(date("H:i", strtotime($item['jam_pertama'])));
            $jamAkhir = urldecode(date("H:i", strtotime($item['jam_terakhir'])));
            if($logAbsen->users_id != $item['id_karyawan']){
                $logAbsen->users_id = $item['id_karyawan'] ;
                $logAbsen->tanggal = $tanggalLog;
                $logAbsen->jam_masuk = $jamAwal;
                $logAbsen->jam_keluar = $jamAkhir;

                $total = (strtotime($jamAkhir) - strtotime($jamAwal));
                $totaljam = $total/3600;
                $totaljam = (int)$totaljam;
                $totalmenit = ($total%3600)/60;

                

                if ($totaljam / 10 < 1){
                    $totaljam = "0".$totaljam;
                }
        
                if ($totalmenit / 10 < 1){
                    $totalmenit = "0".$totalmenit;
                }
        
                $totalWaktu = $totaljam.":".$totalmenit;

                $lamaKerja = $this->getLamaKerja();
                $batasWaktu = $this->getBatasWaktu();
                if (strtotime($jamAwal) > strtotime($batasWaktu))  {
                    $statusTerlambat = true;
                }else{
                    $statusTerlambat = false;
                }

                $logAbsen->total_jam = $totalWaktu;
                $logAbsen->keterlambatan = $statusTerlambat;
                $logAbsen->save();

                $id = $logAbsen->id;

                if ($total >= 28800){ //28800 = 8 jam
                    $totalLebih = ((strtotime($jamAkhir)-1688947200)-(strtotime($jamAwal)-1688947200))-28800;
                    $jamKerjaLebih = $totalLebih;
                    $lebihForLembur = $totalLebih/60;
                    // --------------------------------------------------------------------------------------------------
                    $lembur = Lembur::where('users_id', $item['id_karyawan'])->where('tanggal', $yesterday)->first();                
                    if($lembur != null){
                        if($lembur->status == 1){
                            if($lebihForLembur > $lembur->jumlah_jam){ //tambahan
                                $lebihForLembur = $lebihForLembur - $lembur->jumlah_jam;
                                $totalLebih = $lebihForLembur*60;
                            }else{
                                $totalLebih = 0;
                            }
                        }
                        //$newValue = $newValue - $lembur->jumlah_jam;
                    }
                    //----------------------------------------------------------------------------------------------------------------
                    $totalJamForLebih = $totalLebih;
                
                    //----------------------------------------------------------------------------------------------------------------
                    $totalJamLebih = $jamKerjaLebih/3600;
                    $totalJamLebih = (int)$totalJamLebih;
                
                    $totalMenitLebih = ($jamKerjaLebih%3600)/60;
                
                    if ($totalJamLebih / 10 < 1){
                        $totalJamLebih = "0".$totalJamLebih;
                    }
                
                    if ($totalMenitLebih / 10 < 1){
                        $totalMenitLebih = "0".$totalMenitLebih;
                    }
                
                    $jamKerjaLebih = $totalJamLebih.":".$totalMenitLebih;
    
                    lebihKerja::create([
                        'users_id' => $item['id_karyawan'],
                        'absen_id' => $id,
                        'total_jam' => $jamKerjaLebih,
                    ]);
                    //----------------------------------------------------------------------------------------------------------------
    
    
                    $newValue = $totalJamForLebih/60;
    
                    // User::where('id', $row[1])->update(['jam_lebih' => $newValue]);
    
                    $user = User::find($item['id_karyawan']);
                    $user->jam_lebih = $user->jam_lebih + $newValue;
                    $user->save();
                    
                }
                
                if ($total < 28800){
                    $totalKurang = 28800 - $total;
                    $newValue = $totalKurang/60;
    
                    $totalJamForKurang = $totalKurang;
                
                    $totalJamForKurang = $totalKurang/3600;
                    $totalJamForKurang = (int)$totalJamForKurang;
                
                    $totalMenitKurang = ($totalKurang%3600)/60;
                
                    if ($totalJamForKurang / 10 < 1){
                        $totalJamForKurang = "0".$totalJamForKurang;
                    }
                
                    if ($totalMenitKurang / 10 < 1){
                        $totalMenitKurang = "0".$totalMenitKurang;
                    }
                
                    $totalKurang = $totalJamForKurang.":".$totalMenitKurang;
    
                    JamKurang::create([
                        'users_id' => $item['id_karyawan'],
                        'absen_id' => $id,
                        'total_jam_kurang' => $totalKurang,
                    ]);
    
                    $user = User::find($item['id_karyawan']);
                    $user->jam_kurang = $user->jam_kurang + $newValue;
                    $user->save();
                }
            }
        }
        return redirect('/log_absen')->with('msg', 'Tambah akun berhasil');
    }
}