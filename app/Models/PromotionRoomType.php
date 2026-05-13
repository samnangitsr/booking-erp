<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PromotionRoomType extends Model
{
    protected $table = 'promotion_room_types';

    protected $fillable = [
        'promotion_id',
        'room_type_id',
    ];

    protected $casts = [
        'promotion_id' => 'integer',
        'room_type_id' => 'integer',
    ];
}