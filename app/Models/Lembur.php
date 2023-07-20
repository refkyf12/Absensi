<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Lembur extends Model
{
    use HasFactory;
    protected $table = 'lembur';

    protected $fillable = [
        'id',
        'users_id',
        'tanggal',
        'jam_awal',
        'jam_akhir',
        'jumlah_jam',
        'status_kerja',
        'status',
    ];

    public function user(){
        return $this->belongsTo(User::class, 'users_id');
    }
}