<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\User;

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
    ];

    public function users(){
        return $this->belongsTo(User::class);
    }

    public function lembur(){
        return $this->hasMany(Lembur::class);
    }
}
