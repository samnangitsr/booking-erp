<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class RoomTypeImage extends Model
{
    protected $table = 'room_type_images';

    protected $fillable = [
        'room_type_id',
        'image_path',
        'title',
        'is_cover',
        'sort_order',
    ];

    protected $casts = [
        'room_type_id' => 'integer',
        'is_cover' => 'boolean',
        'sort_order' => 'integer',
    ];
}