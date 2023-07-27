<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use App\Models\lebihKerja;
use App\Models\logAbsen;
use App\Models\Role;
use App\Models\logKegiatan;
use App\Models\LogActivity;
use App\Models\absenNonKerja;
use App\Models\Ketidakhadiran;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'id',
        'nama',
        'email',
        'password',
        'role_id',
        'jam_lebih',
        'jam_kurang',
        'jam_lembur',
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

    public function absen_non_kerja(){
        return $this->hasMany(absenNonKerja::class);
    }

    public function log_activity(){
        return $this->hasMany(LogActivity::class);
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

    public function ketidakhadiran(){
        return $this->hasMany(Ketidakhadiran::class);
    }
}
