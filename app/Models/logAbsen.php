<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\User;
use App\Models\liburNasional;
use App\Models\lebihKerja;

class logAbsen extends Model
{
    protected $table = 'log_absen';

    protected $fillable = [
        'id',
        'users_id',
        'tanggal',
        'jam_masuk',
        'jam_keluar',
        'total_jam',
        'keterlambatan',
        'deskripsi',
    ];

    public function users(){
        return $this->belongsTo(User::class);
    }

    public function lebihKerja(){
        return $this->hasOne(lebihKerja::class);
    }

    public function liburNasional()
    {
        return $this->belongsTo(liburNasional::class, 'tanggal', 'tanggal');
    }
}
