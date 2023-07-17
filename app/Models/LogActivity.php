<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\User;

class LogActivity extends Model
{
    use HasFactory;

    protected $table = 'log_activity';

    protected $fillable = [
        'id',
        'users_id',
        'tanggal',
        'jam_tapping',
    ];

    public function users(){
        return $this->belongsTo(User::class);
    }
}
