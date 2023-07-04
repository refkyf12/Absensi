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
        return new logAbsen([
            'users_id' => $row[1],
            'nama' => $row[2],
            'jam' => $row[3],
        ]);
    }
}
