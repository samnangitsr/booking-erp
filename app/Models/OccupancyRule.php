<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class OccupancyRule extends Model
{
    protected $table = 'occupancy_rules';

    protected $fillable = [
        'room_type_id',
        'max_adults',
        'max_children',
        'max_infants',
        'max_total_guests',
        'allow_extra_bed',
    ];

    protected $casts = [
        'room_type_id' => 'integer',
        'max_adults' => 'integer',
        'max_children' => 'integer',
        'max_infants' => 'integer',
        'max_total_guests' => 'integer',
        'allow_extra_bed' => 'integer',
    ];
}