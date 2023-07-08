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
        'jumlah_jam',
    ];

    public function user(){
        return $this->belongsTo(User::class, 'users_id');
    }
}