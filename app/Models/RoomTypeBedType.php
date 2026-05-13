<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class RoomTypeBedType extends Model
{
    protected $table = 'room_type_bed_type';

    protected $fillable = [
        'room_type_id',
        'bed_type_id',
        'quantity',
    ];

    protected $casts = [
        'room_type_id' => 'integer',
        'bed_type_id' => 'integer',
        'quantity' => 'integer',
    ];
}