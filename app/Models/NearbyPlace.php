<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class NearbyPlace extends Model
{
    protected $table = 'nearby_places';

    protected $fillable = [
        'property_id',
        'name',
        'place_type',
        'distance_km',
        'description',
    ];

    protected $casts = [
        'property_id' => 'integer',
        'distance_km' => 'decimal:2',
    ];
}