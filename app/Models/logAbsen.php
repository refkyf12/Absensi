<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class logAbsen extends Model
{
    protected $table = 'log_absen';

    protected $fillable = [
        'users_id',
        'nama',
        'tanggal',
        'jam_masuk',
        'jam_keluar',
        'total_jam',
        'keterlambatan',
    ];

    public function users(){
        return $this->belongsTo(Users::class);
    }
}
