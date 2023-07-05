<?php

namespace App\Imports;

use App\Models\logAbsen;
use App\Models\Lembur;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\ToCollection;

class LogAbsenImport implements ToCollection
{
    /**
    * @param array $row
    *
    * @return \Illuminate\Database\Eloquent\Model|null
    */
    public function collection(Collection $rows)
    {
        foreach ($rows as $row) {
            $temp = explode(" ",$row[3]);
            $time = strtotime($temp[0]);
            $newformat = date('Y-m-d',$time);
            $keluar = strtotime($row[4]);
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
            $batas = "09:00:00";
            if ($masuk > strtotime($batas))  {
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

            $batasKerja = strtotime("17:00:00");
        
            $totalLembur = $keluar-$batasKerja;
        
            $totalJamLembur = $totalLembur/3600;
            $totalJamLembur = (int)$totalJamLembur;
        
            $totalMenitLembur = ($totalLembur%3600)/60;
        
            if ($totalJamLembur / 10 < 1){
                $totalJamLembur = "0".$totalJamLembur;
            }
        
            if ($totalMenitLembur / 10 < 1){
                $totalMenitLembur = "0".$totalMenitLembur;
            }
        
            $totalLembur = $totalJamLembur.":".$totalMenitLembur;

            Lembur::create([
                'users_id' => $row[1],
                'absen_id' => $row[0],
                'total_jam' => $totalLembur,
            ]);
        }
        
    }

}
