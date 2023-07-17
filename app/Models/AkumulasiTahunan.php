<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\lebihKerja;
use App\Models\logAbsen;
use App\Models\Role;
use App\Models\logKegiatan;

class AkumulasiTahunan extends Model
{
    use HasFactory;
    protected $table = 'akumulasi_tahunan';

    protected $fillable = [
        'id',
        'users_id',
        'nama',
        'email',
        'password',
        'role_id',
        'jam_lebih',
        'created_at',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
    ];

    public function log_absen(){
        return $this->hasMany(logAbsen::class);
    }

    public function log_kegiatan(){
        return $this->hasMany(logKegiatan::class);
    }

    public function lebihKerja(){
        return $this->hasMany(lebihKerja::class);
    }

    public function lembur(){
        return $this->hasMany(Lembur::class);
    }

    public function cuti(){
        return $this->hasMany(Cuti::class);
    }

    public function role(){
        return $this->belongsTo(Role::class);
    }
}
