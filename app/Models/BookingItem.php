<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class BookingItem extends Model
{
    protected $table = 'booking_items';

    protected $fillable = [
        'booking_id',
        'property_id',
        'room_type_id',
        'rate_plan_id',
        'room_id',
        'room_name',
        'rate_plan_name',
        'check_in_date',
        'check_out_date',
        'nights',
        'rooms_count',
        'adults',
        'children',
        'unit_price',
        'total_price',
        'status',
    ];

    protected $casts = [
        'booking_id' => 'integer',
        'property_id' => 'integer',
        'room_type_id' => 'integer',
        'rate_plan_id' => 'integer',
        'room_id' => 'integer',
        'check_in_date' => 'date',
        'check_out_date' => 'date',
        'nights' => 'integer',
        'rooms_count' => 'integer',
        'adults' => 'integer',
        'children' => 'integer',
        'unit_price' => 'decimal:2',
        'total_price' => 'decimal:2',
    ];
}