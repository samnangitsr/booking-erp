<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class RoomBlock extends Model
{
    protected $table = 'room_blocks';

    protected $fillable = [
        'room_id',
        'property_id',
        'start_date',
        'end_date',
        'reason',
        'status',
        'created_by',
    ];

    protected $casts = [
        'room_id' => 'integer',
        'property_id' => 'integer',
        'start_date' => 'date',
        'end_date' => 'date',
        'created_by' => 'integer',
    ];
}