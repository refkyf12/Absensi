<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Rules extends Model
{
    protected $table = 'rules';

    protected $fillable = [
        'id',
        'key',
        'value',
    ];
}
