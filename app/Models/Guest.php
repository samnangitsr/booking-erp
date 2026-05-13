<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Guest extends Model
{
    protected $table = 'guests';

    protected $fillable = [
        'booking_id',
        'first_name',
        'last_name',
        'gender',
        'age',
        'guest_type',
        'phone',
        'email',
        'nationality',
        'is_primary',
    ];

    protected $casts = [
        'booking_id' => 'integer',
        'age' => 'integer',
        'is_primary' => 'boolean',
    ];
}