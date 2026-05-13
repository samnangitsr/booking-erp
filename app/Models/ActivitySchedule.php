<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ActivitySchedule extends Model
{
    protected $table = 'activity_schedules';

    protected $fillable = [
        'activity_id',
        'activity_date',
        'start_time',
        'available_slots',
        'price',
        'status',
    ];

    protected $casts = [
        'activity_id' => 'integer',
        'activity_date' => 'date',
        'start_time' => 'string',
        'available_slots' => 'integer',
        'price' => 'decimal:2',
    ];
}