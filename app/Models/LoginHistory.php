<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class LoginHistory extends Model
{
    protected $table = 'login_histories';

    protected $fillable = [
        'user_id',
        'login_at',
        'logout_at',
        'ip_address',
        'device',
        'browser',
        'status',
    ];

    protected $casts = [
        'user_id' => 'integer',
        'login_at' => 'datetime',
        'logout_at' => 'datetime',
    ];
}