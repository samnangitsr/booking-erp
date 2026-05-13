<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PropertyAmenity extends Model
{
    protected $table = 'property_amenity';

    protected $fillable = [
        'property_id',
        'amenity_id',
    ];

    protected $casts = [
        'property_id' => 'integer',
        'amenity_id' => 'integer',
    ];
}