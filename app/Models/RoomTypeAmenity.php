<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class RoomTypeAmenity extends Model
{
    protected $table = 'room_type_amenity';

    protected $fillable = [
        'room_type_id',
        'amenity_id',
    ];

    protected $casts = [
        'room_type_id' => 'integer',
        'amenity_id' => 'integer',
    ];
}