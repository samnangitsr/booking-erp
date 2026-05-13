<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class City extends Model
{
    protected $table = 'cities';

    protected $fillable = [
        'country_id',
        'name',
        'slug',
        'status',
    ];

    protected $casts = [
        'country_id' => 'integer',
    ];
}