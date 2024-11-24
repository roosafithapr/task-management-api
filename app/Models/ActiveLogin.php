<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ActiveLogin extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'session_id',
    ];
}
