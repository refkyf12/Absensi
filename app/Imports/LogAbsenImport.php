<?php

namespace App\Imports;

use App\Models\logAbsen;
use Maatwebsite\Excel\Concerns\ToModel;

class LogAbsenImport implements ToModel
{
    /**
    * @param array $row
    *
    * @return \Illuminate\Database\Eloquent\Model|null
    */
    public function model(array $row)
    {
        $temp = explode(" ",$row[2]);
        $time = strtotime($temp[0]);
        $newformat = date('Y-m-d',$time);
        $masuk = strtotime($temp[1]);
        $keluar = strtotime($row[3]);
        $total = (strtotime($row[3]) - strtotime($temp[1])) / 3600;
        //coba
        $totaljam = $total*3600/3600;
        $totalmenit = (($total*3600)%3600)/60;
        $totalWaktu = (int)$totaljam." Jam ".(int)$totalmenit." Menit";
        //---
        $batas = "09:00:00";
        if ($masuk > strtotime($batas))  {
            $statusTerlambat = true;
        }else{
            $statusTerlambat = false;
        }

        return new logAbsen([
            'users_id' => $row[0],
            'nama' => $row[1],
            'tanggal' => $newformat,
            'jam_masuk' => $temp[1],
            'jam_keluar' => $row[3],
            'total_jam' => $totalWaktu,
            'keterlambatan'=> $statusTerlambat,
        ]);
    }

}
