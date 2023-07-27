<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Cuti extends Model
{
    use HasFactory;
    protected $table = 'cuti';

    protected $fillable = [
        'id',
        'users_id',
        'tanggal_awal',
        'tanggal_akhir',
        'jumlah_hari',
        'deskripsi',
        'status',
    ];

    public function user(){
        return $this->belongsTo(User::class, 'users_id');
    }
}