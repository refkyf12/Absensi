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
        $temp = explode(" ",$row[3]);
        $time = strtotime($temp[0]);
        $newformat = date('Y-m-d',$time);
        
        $masuk = strtotime($temp[1]);
        $batas = "09:15:00";



        if ($masuk > strtotime($batas))  {
            $statusTerlambat = true;
        }else{
            $statusTerlambat = false;
        }

        return new logAbsen([
            'users_id' => $row[1],
            'nama' => $row[2],
            'tanggal' => $newformat,
            'jam' => $temp[1],
            'status'=>$temp[2],
            'keterlambatan'=> $statusTerlambat,
        ]);
    }

}
