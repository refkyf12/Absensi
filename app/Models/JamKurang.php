<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\User;
use App\Models\logAbsen;

class JamKurang extends Model
{
    use HasFactory;
    protected $table = 'jam_kurang';

    protected $fillable = [
        'users_id',
        'absen_id',
        'total_jam_kurang',
    ];

    public function users(){
        return $this->belongsTo(User::class);
    }

    public function log_absen(){
        return $this->belongsTo(logAbsen::class, 'absen_id');
    }
}
