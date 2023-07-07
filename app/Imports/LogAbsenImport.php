<?php

namespace App\Imports;

use App\Models\logAbsen;
use App\Models\lebihKerja;
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

            $batasKerja = strtotime("17:00:00");
        
            $totalLebih = $keluar-$batasKerja;
        
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
        }
        
    }

}
