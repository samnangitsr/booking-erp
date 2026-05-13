<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AvailabilityCalendar extends Model
{
    protected $table = 'availability_calendars';

    protected $fillable = [
        'property_id',
        'room_type_id',
        'available_date',
        'total_rooms',
        'booked_rooms',
        'blocked_rooms',
        'available_rooms',
        'stop_sell',
    ];

    protected $casts = [
        'property_id' => 'integer',
        'room_type_id' => 'integer',
        'available_date' => 'date',
        'total_rooms' => 'integer',
        'booked_rooms' => 'integer',
        'blocked_rooms' => 'integer',
        'available_rooms' => 'integer',
        'stop_sell' => 'integer',
    ];
}