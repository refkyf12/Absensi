<?php

namespace App\Imports;

use App\Models\JamKurang;
use App\Models\Lembur;
use App\Models\logAbsen;
use App\Models\lebihKerja;
use App\Models\User;
use App\Models\logKegiatan;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\ToCollection;

class LogAbsenImport implements ToCollection
{

    public function setBatasWaktu($batas){
		$this->batas = $batas;
	}
    public function getBatasWaktu() {
		return $this->batas; 
    }
    public function setBatasKerja($batasKerja){
		$this->batasKerja = $batasKerja;
	}
    public function getBatasKerja() {
		return $this->batasKerja; 
    }
    public function setLog($kegiatan){
		$this->kegiatan = $kegiatan;
	}
    public function getLog() {
		return $this->kegiatan; 
	}
    /**
    * @param array $row
    *
    * @return \Illuminate\Database\Eloquent\Model|null
    */
    public function collection(Collection $rows)
    {
        foreach ($rows as $row) {
            $temp = explode(" ",$row[3]);
            //dd($temp[1]);
            $time = strtotime($temp[0]);
            $newformat = date('Y-m-d',$time);
            $keluar = strtotime($row[4]);
            if ($row[0] == 64){
                dd($row);
            }
            $masuk = $temp[1];
            $total = (strtotime($row[4]) - strtotime($masuk));
            //coba
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
            //---
            $batas = $this->getBatasWaktu();
            if (strtotime($masuk) > strtotime($batas))  {
                $statusTerlambat = true;
            }else{
                $statusTerlambat = false;
            }
            
            logAbsen::create([
                'id' => $row[0],
                'users_id' => $row[1],
                'tanggal' => $newformat,
                'jam_masuk' => $masuk,
                'jam_keluar' => $row[4],
                'total_jam' => $totalWaktu,
                'keterlambatan'=> $statusTerlambat,
            ]);

            if ($total >= 28800){ //28800 = 8 jam
                $totalLebih = (($keluar-1688947200)-(strtotime($masuk)-1688947200))-28800;
                $lebihForLembur = $totalLebih/60;
                // --------------------------------------------------------------------------------------------------
                $lembur = Lembur::where('users_id', $row[1])->where('tanggal', $newformat)->first();                
                if($lembur != null){
                    if($lembur->status == 1){
                        $lebihForLembur = $lebihForLembur - $lembur->jumlah_jam;
                        $totalLebih = $lebihForLembur*60;
                    }
                    //$newValue = $newValue - $lembur->jumlah_jam;
                }
                //----------------------------------------------------------------------------------------------------------------
                $totalJamForLebih = $totalLebih;
            
                $totalJamLebih = $totalLebih/3600;
                $totalJamLebih = (int)$totalJamLebih;
            
                $totalMenitLebih = ($totalLebih%3600)/60;
            
                if ($totalJamLebih / 10 < 1){
                    $totalJamLebih = "0".$totalJamLebih;
                }
            
                if ($totalMenitLebih / 10 < 1){
                    $totalMenitLebih = "0".$totalMenitLebih;
                }
            
                $totalLebih = $totalJamLebih.":".$totalMenitLebih;

                lebihKerja::create([
                    'users_id' => $row[1],
                    'absen_id' => $row[0],
                    'total_jam' => $totalLebih,
                ]);
            

                $newValue = $totalJamForLebih/60;

                // User::where('id', $row[1])->update(['jam_lebih' => $newValue]);

                $user = User::find($row[1]);
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
                    'users_id' => $row[1],
                    'absen_id' => $row[0],
                    'total_jam_kurang' => $totalKurang,
                ]);

                $user = User::find($row[1]);
                $user->jam_kurang = $user->jam_kurang + $newValue;
                $user->save();
            }
        }

        if (Auth::check())
                {
                    date_default_timezone_set("Asia/Jakarta");
                    $id = Auth::id();
                    $date = date("Y-m-d h:i:sa");
                    $text = 'Melakukan Import Excel';
                    $logKegiatan = new logKegiatan;
                    $logKegiatan->users_id = $id;
                    $logKegiatan->kegiatan = $text;
                    $logKegiatan->created_at = $date;
                    $logKegiatan->save();
                }
        
    }

}
