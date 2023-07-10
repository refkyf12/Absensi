<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\User;

class LogKegiatan extends Model
{
    use HasFactory;

    protected $table = 'log_kegiatan';

    protected $fillable = [
        'id',
        'users_id',
        'kegiatan',
    ];

    public function user(){
        return $this->belongsTo(User::class, 'users_id');
    }
}
