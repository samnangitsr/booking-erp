<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Destination extends Model
{
    protected $table = 'destinations';

    protected $fillable = [
        'country_id',
        'city_id',
        'area_id',
        'name',
        'slug',
        'description',
        'image',
        'status',
    ];

    protected $casts = [
        'country_id' => 'integer',
        'city_id' => 'integer',
        'area_id' => 'integer',
    ];
}