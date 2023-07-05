<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\User;

class Lembur extends Model
{
    use HasFactory;

    protected $table = 'lembur';

    protected $fillable = [
        'users_id',
        'absen_id',
        'total_jam',
        'disetujui',
    ];

    public function users(){
        return $this->belongsTo(User::class);
    }

    public function log_absen(){
        return $this->belongsTo(logAbsen::class);
    }
}
