<?php

namespace App\Http\Controllers;

// require 'vendor/autoload.php';



use Illuminate\Http\Request;

use Monolog\Logger;
use Monolog\Handler\StreamHandler;
use Monolog\Handler\FirePHPHandler;

use App\lib\TAD;
use App\Http\Controllers\_DIR_;
use App\Models\logAbsen;
use App\Models\Lembur;
use App\Models\lebihKerja;
use App\Models\User;
use App\Models\JamKurang;
use App\Models\Rules;
use App\Http\Controllers\TADFactory;
use App\Traits\jamKeInt;


class SoapController extends Controller
{
    public function getBatasWaktu(){
        $rules = Rules::where('key', "batas_waktu")->first();
        $batas = $rules["value"];
        return $batas;
    }

    use jamKeInt;

    public function getDataTAD(){
        $tad = (new TADFactory((['ip'=> '10.50.0.60', 'com_key'=>0])))->get_instance();
        return $tad;
    }
    public function logAbsenStore(Request $request)
    {
        // $dotenv = Dotenv\Dotenv::create(_DIR_);
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

        //$yesterday = date('Y-m-d',strtotime("-1 days"));
        //$e = 0;

        $yesterday=date('2023-07-11');

        //while($yesterday != '2023-07-01'){
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

                $total = ($this->timeToInteger($jamAkhir) - $this->timeToInteger($jamAwal));

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

                $batas = $this->getBatasWaktu();
                if ($this->timeToInteger($jamAwal) > $this->timeToInteger($batas))  {
                    $statusTerlambat = true;
                }else{
                    $statusTerlambat = false;
                }

                $logAbsen->total_jam = $totalWaktu;
                $logAbsen->keterlambatan = $statusTerlambat;
                $logAbsen->save();

                $id = $logAbsen->id;

                if ($total >= 28800){ //28800 = 8 jam
                    $lembur = Lembur::where('users_id', $item['id_karyawan'])->where('tanggal', $yesterday)->first();                
                    $jamMasuk = $this->timeToInteger($jamAwal)/60;
                    $jamKeluar = $this->timeToInteger($jamAkhir)/60;

                    if($lembur != null){
                        $jamAwalLembur = $this->timeToInteger($lembur->jam_awal)/60;
                        $jamAkhirLembur = $this->timeToInteger($lembur->jam_akhir)/60;
                    }

                    //UBAH KE YANG BARU---------------------------------------------------------
                    $totalLebih = ($jamKeluar-$jamMasuk)-(28800/60);
                    $jamKerjaLebih = $totalLebih;
                    $lebihForLembur = $totalLebih;
                    // --------------------------------------------------------------------------------------------------
                    if($lembur != null){
                        if($lembur->status == 1){
                            $masukLebih1 = ($jamAwalLembur - ($jamMasuk+(28800/60)));//dari selesai jam kerja hingga jam awal lembur
                            $lebihForLembur = $lebihForLembur - ($masukLebih1+$lembur->jumlah_jam);
                            if($lebihForLembur <= 0){
                                $lebihForLembur = 0;
                            }
    
                            $totalJamForLebih = $masukLebih1 + $lebihForLembur;
                            //dd($totalJamForLebih);
    
                        }else{
                            $totalJamForLebih = $totalLebih;  
                        }
                    }else{
                        $totalJamForLebih = $totalLebih;
                    }

                    
                
                    //----------------------------------------------------------------------------------------------------------------
                    //kodingan dibawah untuk tambah data ke lebihKerja
                    $totalJamLebih = $jamKerjaLebih/60;
                    $totalJamLebih = (int)$totalJamLebih;
                
                    $totalMenitLebih = ($jamKerjaLebih%60);
                
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
    
    
                    $newValue = $totalJamForLebih;
    
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
        //$yesterday = date('Y-m-d', strtotime($yesterday . ' - 1 day'));
        
        return redirect('/log_absen')->with('msg', 'Tambah akun berhasil');
    }
}