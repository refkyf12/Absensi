<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\User;

class Ketidakhadiran extends Model{


    protected $table = 'ketidakhadiran';

    protected $fillable = [
        'id',
        'users_id',
        'tanggal',
        'deskripsi',
    ];

    public function users(){
        return $this->belongsTo(User::class);
    }
}