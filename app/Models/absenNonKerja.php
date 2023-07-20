<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\User;

class absenNonKerja extends Model
{
    use HasFactory;

    protected $table = 'absen_non_kerja';

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
}
