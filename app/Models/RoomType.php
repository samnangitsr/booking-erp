<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class RoomType extends Model
{
    use SoftDeletes;

    protected $table = 'room_types';

    protected $fillable = [
        'property_id',
        'room_type_code',
        'name',
        'slug',
        'description',
        'max_adults',
        'max_children',
        'max_occupancy',
        'room_size',
        'room_size_unit',
        'total_rooms',
        'base_price',
        'status',
    ];

    protected $casts = [
        'property_id' => 'integer',
        'max_adults' => 'integer',
        'max_children' => 'integer',
        'max_occupancy' => 'integer',
        'room_size' => 'decimal:2',
        'total_rooms' => 'integer',
        'base_price' => 'decimal:2',
    ];
}